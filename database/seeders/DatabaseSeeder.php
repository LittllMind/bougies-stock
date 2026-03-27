<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Priorité aux bougies artisanales + utilisateurs pour tests
        $this->call([
            UserSeeder::class,       // Utilisateurs: admin@test.com, employe@test.com, client@test.com
            BougieSeeder::class,     // Bougies artisanales
        ]);
    }
}
