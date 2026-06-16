<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Medication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class MedicationAgendaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se a agenda filtra corretamente os medicamentos baseando-se
     * no dia da semana atual configurado no sistema.
     */
    public function test_deve_listar_apenas_os_medicamentos_configurados_para_o_dia_da_semana_atual()
    {
        // 1. Criar um usuário e autenticá-lo
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Fixa a data do sistema em uma Segunda-feira real (15/06/2026)
        Carbon::setTestNow(Carbon::parse('2026-06-15')); 

        // 3. Criar um medicamento para Segunda-feira (Deve aparecer na listagem)
        Medication::create([
            'user_id' => $user->id,
            'name' => 'Remédio de Segunda',
            'dosage' => '1 comprimido',
            'interval_hours' => 8,
            'start_time' => '08:00',
            'days_of_week' => ['Segunda-feira', 'Quarta-feira'],
        ]);

        // 4. Criar um medicamento apenas para o Final de Semana (Não deve aparecer hoje)
        Medication::create([
            'user_id' => $user->id,
            'name' => 'Remédio de Fim de Semana',
            'dosage' => '1 gota',
            'interval_hours' => 24,
            'start_time' => '10:00',
            'days_of_week' => ['Sábado', 'Domingo'],
        ]);

        // 5. Executa a requisição GET para a rota da agenda do dia
        $response = $this->get(route('medications.agenda'));

        // 6. Asserts de validação do comportamento
        $response->assertStatus(200);
        $response->assertSee('Remédio de Segunda');
        $response->assertDontSee('Remédio de Fim de Semana');

        // 7. Reseta o relógio global do Carbon para não impactar outros testes da suite
        Carbon::setTestNow();
    }
}