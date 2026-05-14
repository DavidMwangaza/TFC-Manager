<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_profil_est_affichee(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_informations_profil_non_modifiables_si_feature_desactivee(): void
    {
        $user = User::factory()->create();
        $initialName = $user->name;
        $initialEmail = $user->email;

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response->assertStatus(405);

        $user->refresh();

        $this->assertSame($initialName, $user->name);
        $this->assertSame($initialEmail, $user->email);
    }

    public function test_statut_verification_email_inchange_si_mise_a_jour_profil_desactivee(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response->assertStatus(405);

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_utilisateur_ne_peut_pas_supprimer_compte_si_feature_desactivee(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertStatus(405);

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh());
    }

    public function test_validation_mot_de_passe_suppression_non_disponible_si_feature_desactivee(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response->assertStatus(405);

        $this->assertNotNull($user->fresh());
    }
}
