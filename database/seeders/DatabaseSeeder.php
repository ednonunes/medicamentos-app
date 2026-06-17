<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Medication;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\MedicationSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Cria um utilizador de teste com dados fixos para o seu login
        $user = User::factory()->create([
            'name' => 'Utilizador de Teste',
            'email' => 'teste@email.com',
            'password' => bcrypt('12345678'),
            'phone' => '5533999999999',
        ]);

        $this->call([
            MedicationSeeder::class,
        ]);

        // 3. Opcional: Cria mais 5 utilizadores aleatórios, cada um com 5 remédios só para encher o banco
        User::factory(5)->create()->each(function ($otherUser) {
            Medication::factory(5)->create(['user_id' => $otherUser->id]);
        });
    }
}
