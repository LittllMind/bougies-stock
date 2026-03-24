<?php

namespace Tests\Feature;

use App\Models\Bougie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugCatalogueTest extends TestCase
{
    use RefreshDatabase;

    public function test_debug_catalogue_error()
    {
        try {
            $response = $this->get('/catalogue');
            dump($response->exception ?? 'No exception');
            $response->dump();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            dump($e->getMessage());
            dump($e->getTraceAsString());
            $this->fail('Exception: ' . $e->getMessage());
        }
    }
}
