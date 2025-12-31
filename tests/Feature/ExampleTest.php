<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        // Créer un utilisateur et se connecter
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/');
        
        $response->assertStatus(200);
    }
}
