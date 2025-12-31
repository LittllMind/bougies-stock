<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test que la page d'accueil redirige correctement (authentification requise)
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Option 1 : Tester la redirection (si auth requis)
        $response = $this->get('/');
        $response->assertStatus(302); // Redirige vers /login
        
        // Option 2 : Tester une page publique (si elle existe)
        // $response = $this->get('/kiosque');
        // $response->assertStatus(200);
    }
}
