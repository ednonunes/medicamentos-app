<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '31999999999',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_new_users_can_register_with_formatted_phone_and_save_it_clean(): void
    {
        // 🎯 Simula o envio com a máscara do frontend
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '(31) 99999-9999', 
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // 🎯 Garante que a limpeza funcionou e salvou apenas números puros no banco
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'phone' => '31999999999',
        ]);
    }

    public function test_new_users_can_register_without_providing_a_phone(): void
    {
        // Garante que o sistema aceita o cadastro sem telefone (campo opcional)
        $response = $this->post('/register', [
            'name' => 'Optional User',
            'email' => 'optional@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '', // Vazio
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        // O banco de dados deve registrar como nulo
        $this->assertDatabaseHas('users', [
            'email' => 'optional@example.com',
            'phone' => null,
        ]);
    }
}
