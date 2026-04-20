<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AuthenticationUiTest extends TestCase
{
    /**
     * Test que la page login affiche le titre correct "Les bougies de Séraphie"
     * et non pas "Stock Vinyles" ou autre legacy.
     *
     * @return void
     */
    public function test_page_login_affiche_titre_seraphie_et_non_vinyles()
    {
        // Configurer explicitement le nom de l'application
        Config::set('app.name', 'Les bougies de Séraphie');

        $response = $this->get('/login');

        $response->assertStatus(200);

        // Vérifier que le titre contient "Les bougies de Séraphie"
        $response->assertSee('Les bougies de Séraphie', false);

        // Vérifier qu'il n'y a PAS de référence à "vinyles" (insensible à la casse)
        $content = strtolower($response->getContent());
        $this->assertStringNotContainsString('vinyles', $content, 'La page ne doit pas contenir le mot "vinyles"');
        $this->assertStringNotContainsString('vinyle', $content, 'La page ne doit pas contenir le mot "vinyle"');

        // Vérifier le sous-titre contextuel "Connexion"
        $response->assertSee('Connexion', false);
    }

    /**
     * Test que la page d'inscription affiche le titre correct
     *
     * @return void
     */
    public function test_page_register_affiche_titre_seraphie_et_non_vinyles()
    {
        Config::set('app.name', 'Les bougies de Séraphie');

        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Les bougies de Séraphie', false);

        $content = strtolower($response->getContent());
        $this->assertStringNotContainsString('vinyles', $content);
        $this->assertStringNotContainsString('vinyle', $content);

        $response->assertSee('Inscription', false);
    }

    /**
     * Test que la page de mot de passe oublié affiche le titre correct
     *
     * @return void
     */
    public function test_page_forgot_password_affiche_titre_seraphie_et_non_vinyles()
    {
        Config::set('app.name', 'Les bougies de Séraphie');

        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Les bougies de Séraphie', false);

        $content = strtolower($response->getContent());
        $this->assertStringNotContainsString('vinyles', $content);
        $this->assertStringNotContainsString('vinyle', $content);
    }

    /**
     * Test que la page de confirmation mot de passe affiche le titre correct
     *
     * @return void
     */
    public function test_page_confirm_password_affiche_titre_seraphie_et_non_vinyles()
    {
        $user = User::factory()->create();

        Config::set('app.name', 'Les bougies de Séraphie');

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
        $response->assertSee('Les bougies de Séraphie', false);

        $content = strtolower($response->getContent());
        $this->assertStringNotContainsString('vinyles', $content);
        $this->assertStringNotContainsString('vinyle', $content);
    }

    /**
     * Test que la page de demande de vérification email affiche le titre correct
     *
     * @return void
     */
    public function test_page_verify_email_affiche_titre_seraphie_et_non_vinyles()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Config::set('app.name', 'Les bougies de Séraphie');

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
        $response->assertSee('Les bougies de Séraphie', false);

        $content = strtolower($response->getContent());
        $this->assertStringNotContainsString('vinyles', $content);
        $this->assertStringNotContainsString('vinyle', $content);
    }

    /**
     * Test que le layout client affiche également le titre correct
     *
     * @return void
     */
    public function test_layout_client_affiche_titre_seraphie_et_non_vinyles()
    {
        $user = User::factory()->create();

        Config::set('app.name', 'Les bougies de Séraphie');

        $response = $this->actingAs($user)->get('/client/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Les bougies de Séraphie', false);
        $response->assertSee('Mes Commandes', false);

        $content = strtolower($response->getContent());
        $this->assertStringNotContainsString('vinyles', $content);
        $this->assertStringNotContainsString('vinyle', $content);
    }

    /**
     * Test que toutes les pages de profil utilisent la charte graphique Séraphie
     *
     * @return void
     */
    public function test_page_profile_affiche_titre_seraphie_et_non_vinyles()
    {
        $user = User::factory()->create();

        Config::set('app.name', 'Les bougies de Séraphie');

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Les bougies de Séraphie', false);

        $content = strtolower($response->getContent());
        $this->assertStringNotContainsString('vinyles', $content);
        $this->assertStringNotContainsString('vinyle', $content);
    }
}
