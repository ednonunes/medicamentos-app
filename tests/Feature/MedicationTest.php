<?php

namespace Tests\Feature;

use App\Models\Medication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicationTest extends TestCase
{
    use RefreshDatabase;

    // Mudamos de anotação para o prefixo "test..." no nome da função
    public function test_an_authenticated_user_can_create_a_medication()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $medicationData = [
            'name' => 'Dipirona',
            'dosage' => '500mg',
            'interval_hours' => 6,
            'start_time' => '08:00',
        ];

        $response = $this->post(route('medications.store'), $medicationData);

        $response->assertRedirect(route('medications.index'));
        $this->assertDatabaseHas('medications', [
            'user_id' => $user->id,
            'name' => 'Dipirona',
        ]);
    }

    public function test_a_guest_user_cannot_create_a_medication()
    {
        $response = $this->post(route('medications.store'), [
            'name' => 'Paracetamol',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_a_user_cannot_delete_another_users_medication()
    {
        // 1. Criar dois usuários distintos
        $userDono = User::factory()->create();
        $userInvasor = User::factory()->create();

        // 2. Criar o medicamento vinculado ao dono usando o relacionamento
        $medication = $userDono->medications()->create([
            'name' => 'Amoxicilina',
            'dosage' => '500mg',
            'interval_hours' => 8,
            'start_time' => '12:00',
        ]);

        // 3. Logar como o invasor e tentar deletar a receita do outro
        $this->actingAs($userInvasor);
        $response = $this->delete(route('medications.destroy', $medication->id));

        // 4. Deve retornar erro 403 e manter o item no banco
        $response->assertStatus(403);
        $this->assertDatabaseHas('medications', ['id' => $medication->id]);
    }
}