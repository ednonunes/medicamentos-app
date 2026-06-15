<?php

namespace Database\Factories;

use App\Models\Medication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicationFactory extends Factory
{
    protected $model = Medication::class;

    public function definition(): array
    {
        // Lista de remédios comuns para simular dados reais em português
        $remedios = ['Paracetamol', 'Dipirona', 'Amoxicilina', 'Ibuprofeno', 'Omeprazol', 'Losartana', 'Metformina', 'Simvastatina', 'Clonazepam', 'Fluoxetina'];
        $dosagens = ['500mg', '1g', '20mg', '50mg', '10 gotas', '1 comprimido', '5ml'];
        $intervalos = [4, 6, 8, 12, 24];

        return [
            // Vincula automaticamente a um usuário existente ou cria um novo se necessário
            'user_id' => User::factory(), 
            'name' => $this->faker->randomElement($remedios) . ' ' . $this->faker->optional(0.3, '')->randomElement(['Complex', 'Generic', 'Duo']),
            'dosage' => $this->faker->randomElement($dosagens),
            'interval_hours' => $this->faker->randomElement($intervalos),
            'start_time' => $this->faker->time('H:i'),
        ];
    }
}