<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationUiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_page_affiche_layout_guest_séraphie()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertSee('Séraphie');
    }

    /** @test */
    public function login_page_affiche_couleurs_séraphie()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        // Classes Tailwind du style Séraphie
        $response->assertSee('bg-gradient-to-br');
        $response->assertSee('from-amber-50');
        $response->assertSee('text-amber-700');
    }

    /** @test */
    public function login_page_contient_formulaire_complet()
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Email');
        $response->assertSee('Mot de passe');
        $response->assertSee('Se souvenir de moi');
    }

    /** @test */
    public function login_fonctionne_avec_identifiants_valides()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(RouteServiceProvider::HOME);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function login_refuse_identifiants_invalides()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function register_page_affiche_layout_guest_séraphie()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertSee('Séraphie');
    }

    /** @test */
    public function register_page_affiche_couleurs_séraphie()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('bg-gradient-to-br');
        $response->assertSee('from-amber-50');
    }

    /** @test */
    public function register_page_contient_formulaire_complet()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Nom');
        $response->assertSee('Email');
        $response->assertSee('Mot de passe');
        $response->assertSee('Confirmer le mot de passe');
    }

    /** @test */
    public function register_crée_utilisateur_et_connecte()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'role' => 'client',
        ]);
    }

    /** @test */
    public function forgot_password_page_affiche_layout_guest_séraphie()
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
        $response->assertSee('Séraphie');
    }

    /** @test */
    public function forgot_password_page_affiche_couleurs_séraphie()
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('bg-gradient-to-br');
        $response->assertSee('from-amber-50');
    }

    /** @test */
    public function forgot_password_page_contient_formulaire_email()
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Email');
        $response->assertSee('Envoyer le lien');
    }

    /** @test */
    public function reset_password_page_affiche_layout_guest_séraphie()
    {
        $response = $this->get(route('password.reset', [
            'token' => 'dummy-token',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
        $response->assertSee('Séraphie');
    }

    /** @test */
    public function logout_déconnecte_utilisateur()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $this->assertAuthenticated();
        
        $response = $this->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}