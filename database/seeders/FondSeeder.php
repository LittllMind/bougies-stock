<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fond;

class FondSeeder extends Seeder
{
    public function run(): void
    {
        // Standard
        Fond::updateOrCreate(
            ['type' => 'standard'],
            [
                'quantite'   => 999,   // ou ce que tu veux
                'prix_achat' => 0,
            ]
        );

        // Miroir
        Fond::updateOrCreate(
            ['type' => 'miroir'],
            [
                'quantite'   => 100,
                'prix_achat' => 0,
            ]
        );

        // Doré
        Fond::updateOrCreate(
            ['type' => 'dore'],
            [
                'quantite'   => 100,
                'prix_achat' => 0,
            ]
        );
    }
}
