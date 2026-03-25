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
        // Priorité aux bougies artisanales
        $this->call([
            BougieSeeder::class,
            // Désactiver les seeders legacy vinyles pour le moment
            // VinyleSeeder::class,
            // UserSeeder::class,
        ]);
    }
}
