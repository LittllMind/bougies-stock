<?php
// database/migrations/2026_03_25_063000_make_titre_vinyle_nullable.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mig nom_bougie déjà dans create_order_items_table
        // Gardée pour compatibilité historique
    }

    public function down(): void
    {
        // No longer applicable
    }
};