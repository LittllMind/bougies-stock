<?php

namespace Tests\Feature;

use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogueVueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test : La page catalogue-vue charge avec le titre principal
     */
    public function test_page_catalogue_vue_charge()
    {
        // Act
        $response = $this->get('/catalogue-vue');

        // Assert - Vérification basique sans routes Laravel
        $response->assertStatus(200)
            ->assertSee('Nos Bougies Artisanales')
            ->assertSee('🕯️');
    }

    /**
     * Test : La page contient le composant Vue
     */
    public function test_page_contient_app_vue()
    {
        // Act
        $response = $this->get('/catalogue-vue');

        // Assert
        $response->assertStatus(200)
            ->assertSee('id="catalogue-app"', false)
            ->assertSee('Vue.js');
    }

    /**
     * Test : La page intègre Vue.js via CDN
     */
    public function test_page_integre_vue_cdn()
    {
        // Act
        $response = $this->get('/catalogue-vue');

        // Assert
        $response->assertStatus(200)
            ->assertSee('unpkg.com/vue@3');
    }

    /**
     * Test : La page a les meta CSRF pour les requêtes POST
     */
    public function test_page_contient_csrf_meta()
    {
        // Act
        $response = $this->get('/catalogue-vue');

        // Assert - Les guillemets sont échappés par Blade
        $response->assertStatus(200)
            ->assertSee('csrf-token');
    }

    /**
     * Test : La page contient les éléments clés
     */
    public function test_page_contient_filtres_vue()
    {
        // Act
        $response = $this->get('/catalogue-vue');

        // Assert
        $response->assertStatus(200)
            ->assertSee('catalogue-app')
            ->assertSee('createApp');
    }

    /**
     * Test : L'API catalogue est appelée dans la page
     */
    public function test_page_appelle_api_catalogue()
    {
        // Act
        $response = $this->get('/catalogue-vue');

        // Assert
        $response->assertStatus(200)
            ->assertSee('/api/catalogue/bougies');
    }
}