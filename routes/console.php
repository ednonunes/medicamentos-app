<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

Schedule::call(function () {
    $agora = Carbon::now('America/Sao_Paulo')->startOfMinute();
    $dataHoje = $agora->format('Y-m-d');
    
    // Forçamos a primeira letra maiúscula para bater com o seu banco (ex: "Quarta-feira")
    $diaDaSemanaAtual = ucfirst($agora->locale('pt_BR')->dayName); 

    $medications = DB::table('medications')
        ->join('users', 'medications.user_id', '=', 'users.id')
        ->whereNotNull('users.phone')
        ->where('users.phone', '!=', '')
        ->select([
            'medications.id as medication_id',
            'medications.name as medication_name',
            'medications.dosage as medication_dosage',
            'medications.start_time',        
            'medications.interval_hours',    
            'medications.days_of_week',      
            'medications.take_on_empty_stomach',
            'medications.observations',
            'users.name as user_name',
            'users.phone as user_phone'
        ])
        ->get();

    foreach ($medications as $medication) {
        
        // 1. Tratamento do dia da semana (Se for uma estrutura JSON ou string separada por vírgula)
        if (!empty($medication->days_of_week)) {
            // Tenta decodificar como JSON primeiro, se falhar, usa o explode tradicional
            $diasMedicação = json_decode($medication->days_of_week, true);
            if (!is_array($diasMedicação)) {
                $diasMedicação = explode(',', $medication->days_of_week);
            }
            
            // Limpa espaços em branco e padroniza a busca
            $diasMedicação = array_map('trim', $diasMedicação);

            if (!in_array($diaDaSemanaAtual, $diasMedicação)) {
                Log::info("Remédio [{$medication->medication_name}] pulado: Hoje é {$diaDaSemanaAtual} e não está configurado para este dia.");
                continue;
            }
        }

        // 2. Cálculo matemático do ciclo
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $dataHoje . ' ' . $medication->start_time, 'America/Sao_Paulo')->startOfMinute();
        
        if ($start->isFuture()) {
            Log::info("Remédio [{$medication->medication_name}] pulado: start_time no futuro.");
            continue;
        }

        $diferencaEmHoras = $start->diffInHours($agora);
        
        // Validação se a hora bate com o intervalo E se o minuto bate exatamente com o start_time original
        $ehHoraDaDose = ($diferencaEmHoras % $medication->interval_hours === 0) && ($start->format('i') === $agora->format('i'));

        if (!$ehHoraDaDose) {
            Log::info("Remédio [{$medication->medication_name}] pulado: Não coincide com o ciclo de horas ou minutos.");
            continue;
        }

        // 3. Checagem se já foi tomado
        $jaTomou = DB::table('medication_logs')
            ->where('medication_id', $medication->medication_id)
            ->where('scheduled_time', $agora->format('Y-m-d H:i:00'))
            ->exists();

        if ($jaTomou) {
            Log::info("Remédio [{$medication->medication_name}] pulado: Já consta como tomado no log.");
            continue;
        }

        // 4. Montagem e Envio da Mensagem
        $texto = "⚠️ *Lembrete Dose em Dia* ⚠️\n\n";
        $texto .= "Olá, *{$medication->user_name}*! Está na hora de tomar seu medicamento:\n\n";
        $texto .= "💊 *Medicamento:* {$medication->medication_name}\n";
        $texto .= "⚖️ *Dosagem:* {$medication->medication_dosage}\n";
        $texto .= "🕒 *Horário:* " . $agora->format('H:i') . "\n";

        if ($medication->take_on_empty_stomach) {
            $texto .= "🍏 *Aviso:* Este medicamento deve ser tomado em *JEJUM*.\n";
        }

        if ($medication->observations) {
            $texto .= "📝 *Observações:* _{$medication->observations}_\n";
        }

        $texto .= "\nPor favor, após tomar, acesse o sistema para registrar! 👍";

        try {
            $response = Http::post(env('WHATSAPP_API_URL'), [
                'phone' => '55' . $medication->user_phone,
                'message' => $texto
            ]);

            if ($response->successful()) {
                Log::info("✅ WhatsApp enviado via Z-API para {$medication->user_name} - Remédio: {$medication->medication_name}");
            } else {
                Log::error("❌ Falha Z-API no medicamento {$medication->medication_name}: " . $response->body());
            }

        } catch (\Exception $e) {
            Log::error("❌ Erro de conexão na Z-API: " . $e->getMessage());
        }
    }
})->everyMinute();