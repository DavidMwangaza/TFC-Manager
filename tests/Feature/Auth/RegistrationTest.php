<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_inscription_non_disponible_si_autoregistration_desactivee(): void
    {
        $response = $this->get('/register');

        $response->assertNotFound();
    }

    public function test_nouveaux_utilisateurs_ne_peuvent_pas_s_inscrire_si_autoregistration_desactivee(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertNotFound();
    }
}
