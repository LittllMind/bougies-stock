<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\DB;
use Database\Seeders\UserSeeder;
use Database\Seeders\BougieSeeder;
use Database\Seeders\VenteSeeder;
use Database\Seeders\FondSeeder;
use Database\Seeders\MouvementStockSeeder;
>>>>>>> origin/master

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Priorité aux bougies artisanales + utilisateurs pour tests
        $this->call([
<<<<<<< HEAD
            UserSeeder::class,       // Utilisateurs: admin@test.com, employe@test.com, client@test.com
            BougieSeeder::class,     // Bougies artisanales
=======
            UserSeeder::class,
            FondSeeder::class,
            BougieSeeder::class,
            MouvementStockSeeder::class,
            VenteSeeder::class,
>>>>>>> origin/master
        ]);
    }
}
