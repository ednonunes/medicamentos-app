<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Medication;
use App\Models\MedicationLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Garante um usuário de teste fixo para você logar
        $user = User::updateOrCreate(
            ['email' => 'teste@email.com'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('password'),
            ]
        );

        // Limpa os dados anteriores para não acumular lixo no teste
        $user->medications()->delete();

        // 2. Criar Medicamento 1: Tomado Todos os Dias (Ex: Diário)
        $med1 = Medication::create([
            'user_id' => $user->id,
            'name' => 'Dipirona 500mg',
            'dosage' => '1 comprimido',
            'interval_hours' => 8,
            'start_time' => '06:00',
            'days_of_week' => ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'],
        ]);

        // 3. Criar Medicamento 2: Dias de semana apenas (Seg, Qua, Sex)
        $med2 = Medication::create([
            'user_id' => $user->id,
            'name' => 'Ômega 3',
            'dosage' => '2 cápsulas',
            'interval_hours' => 12,
            'start_time' => '08:00',
            'days_of_week' => ['Segunda-feira', 'Quarta-feira', 'Sexta-feira'],
        ]);

        // 4. Criar Medicamento 3: Finais de semana apenas (Sáb, Dom)
        $med3 = Medication::create([
            'user_id' => $user->id,
            'name' => 'Vitamina D3',
            'dosage' => '1 gota',
            'interval_hours' => 24,
            'start_time' => '10:00',
            'days_of_week' => ['Sábado', 'Domingo'],
        ]);

        // 5. Simular alguns Logs (Doses tomadas hoje)
        // Vamos simular que a primeira dose da Dipirona (das 06:00) já foi tomada hoje
        MedicationLog::create([
            'medication_id' => $med1->id,
            'scheduled_time' => '06:00',
            'taken_at' => Carbon::today()->setHour(6)->setMinute(5), // Tomado às 06:05
        ]);

        // Se hoje for segunda, quarta ou sexta, vamos fingir que o Ômega 3 das 08:00 também já foi tomado
        $hoje = Carbon::now()->isoFormat('dddd'); // Pega o dia por extenso se configurado, ou use a lógica do seu controller
        
        // Para garantir que o seeder crie o log baseado no que está ativo hoje:
        $diasTraduzidos = [
            'Sunday'    => 'Domingo',
            'Monday'    => 'Segunda-feira',
            'Tuesday'   => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday'  => 'Quinta-feira',
            'Friday'    => 'Sexta-feira',
            'Saturday'  => 'Sábado',
        ];
        $hojeEmPt = $diasTraduzidos[Carbon::now()->format('l')];

        if (in_array($hojeEmPt, $med2->days_of_week)) {
            MedicationLog::create([
                'medication_id' => $med2->id,
                'scheduled_time' => '08:00',
                'taken_at' => Carbon::today()->setHour(8)->setMinute(2),
            ]);
        }
    }
}