<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

Schedule::call(function () {
    
    $agora = Carbon::now('America/Sao_Paulo')->startOfMinute();
    $dataHoje = $agora->format('Y-m-d');
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
            'medications.daily_limit', // Campo adicionado
            'medications.days_of_week',
            'medications.take_on_empty_stomach',
            'medications.observations',
            'users.id as user_id',
            'users.name as user_name',
            'users.phone as user_phone'
        ])
        ->get();

    if($medications->isEmpty()) {
        return; 
    }

    $agrupamento = [];

    foreach ($medications as $medication) {

        // 1. Lógica do dia da semana
        if (!empty($medication->days_of_week)) {
            $diasMedicação = json_decode($medication->days_of_week, true) ?? explode(',', $medication->days_of_week);
            $diasMedicação = array_map('trim', $diasMedicação);

            if (!in_array($diaDaSemanaAtual, $diasMedicação)) continue;
        }
        
        // 2. Validação de horário da dose
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $dataHoje . ' ' . $medication->start_time, 'America/Sao_Paulo')->startOfMinute();
        $diferencaEmHoras = $start->diffInHours($agora);
        $ehHoraDaDose = ($diferencaEmHoras % $medication->interval_hours === 0) && ($start->format('i') === $agora->format('i'));

        if (!$ehHoraDaDose) continue;

        // 3. Checagem de Limite Diário (NOVO)
        if ($medication->daily_limit) {
            $dosesTomadasHoje = DB::table('medication_logs')
                ->where('medication_id', $medication->medication_id)
                ->whereDate('created_at', $dataHoje)
                ->count();

            if ($dosesTomadasHoje >= $medication->daily_limit) {
                Log::info("Medicação [{$medication->medication_name}] ignorada: Limite de {$medication->daily_limit} doses atingido.");
                continue;
            }
        }

        // 4. Checagem se o log deste horário específico já existe
        $jaTomou = DB::table('medication_logs')
            ->where('medication_id', $medication->medication_id)
            ->where('scheduled_time', $agora->format('Y-m-d H:i:00'))
            ->exists();

        if ($jaTomou) continue;

        // Adiciona ao agrupador
        $agrupamento[$medication->user_id]['user_id'] = $medication->user_id;
        $agrupamento[$medication->user_id]['phone'] = $medication->user_phone;
        $agrupamento[$medication->user_id]['name'] = $medication->user_name;
        $agrupamento[$medication->user_id]['items'][] = $medication;
    }

    // Envio único por usuário
    foreach ($agrupamento as $userData) {

        $primeiroNome = explode(' ', trim($userData['name']))[0];
        $texto = "⚠️ *Lembrete Dose em Dia* \n\n";        
        $texto .= "Olá, *{$primeiroNome}*! Está na hora dos seus medicamentos:\n\n";

        foreach ($userData['items'] as $item) {
            $texto .= "💊 *{$item->medication_name}* ({$item->medication_dosage})\n";
            if ($item->take_on_empty_stomach) $texto .= "   └ 🍏 *Jejum*\n";
        }

        $texto .= "\n🕒 *Horário:* " . $agora->format('H:i');
        $texto .= "\n\nPor favor, registre no sistema após tomar! 👍";

       // envio whatsapp
        // --- bloco de envio no console.php ---
        try {
            // 1. A URL no .env DEVE estar no formato: 
            // https://api.z-api.io/instances/SEU_ID_INSTANCIA/token/SEU_TOKEN_INSTANCIA/send-text
            $url = env('WHATSAPP_API_URL');

            // 2. Fazemos o POST direto, sem cabeçalhos de autenticação, 
            // pois a autenticação já está na URL.
            $resposta = Http::post($url, [
                'phone' => '55' . $userData['phone'],
                'message' => $texto
            ]);

            if ($resposta->successful()) {
                Log::info("✅ WhatsApp enviado com sucesso para {$userData['name']}");
            } else {
                // Log detalhado para diagnóstico
                Log::error("❌ Erro Z-API (Status: " . $resposta->status() . "): " . $resposta->body());
            }
        } catch (\Exception $e) {
            Log::error("❌ Erro de conexão com Z-API: " . $e->getMessage());
        }
        
        // envia push notification:
        try {
            // Usamos o ID do usuário para garantir que a notificação vá apenas para o dono
            // Se o usuário estiver logado no navegador, o OneSignal identifica pelo ID
            $client = new \GuzzleHttp\Client();
            $client->post('https://onesignal.com/api/v1/notifications', [
                'headers' => [
                    'Authorization' => 'Basic ' . env('ONESIGNAL_REST_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'app_id' => env('ONESIGNAL_APP_ID'),
                    'headings' => ['en' => '💊 Lembrete de Medicamento'],
                    'contents' => ['en' => "Olá, {$primeiroNome}! É hora de tomar seus remédios."],
                    // Aqui filtramos pelo ID do usuário (recomendo configurar no SDK web: OneSignal.setExternalUserId(userId))
                    'filters' => [
                        ['field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => (string)$userData['user_id']]
                    ]
                ]
            ]);
            
            Log::info("✅ Notificação Push enviada para usuário ID: {$userData['user_id']}");
        } catch (\Exception $e) {
            Log::error("❌ Erro OneSignal: " . $e->getMessage());
        }
    }
})->everyMinute();