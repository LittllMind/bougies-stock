<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function la_page_de_login_est_accessible()
    {
        $response = $this->get('/login');

        $response->status(200);
        $response->assertSee('Connexion');
        $response->assertSee('email');
        $response->assertSee('password');
    }

    /** @test */
    public function un_utilisateur_peut_se_connecter_avec_identifiants_valides()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'client',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    /** @test */
    public function connexion_echec_avec_email_invalide()
    {
        $response = $this->post('/login', [
            'email' => 'inconnu@test.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function connexion_echec_avec_mot_de_passe_invalide()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'client',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'mauvais_mot_de_passe',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function un_utilisateur_connecte_peut_se_deconnecter()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }

    /** @test */
    public function la_page_de_register_est_accessible()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Inscription');
    }

    /** @test */
    public function un_nouvel_utilisateur_peut_sinscrire()
    {
        $response = $this->post('/register', [
            'name' => 'Nouveau Client',
            'email' => 'nouveau@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'nouveau@example.com',
            'role' => 'client',
            'name' => 'Nouveau Client',
        ]);
        $response->assertRedirect('/kiosque');
    }
}
