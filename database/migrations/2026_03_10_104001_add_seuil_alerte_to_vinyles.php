<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration legacy - vinyles table supprimée
 * Gardée pour compatibilité historique
 */
return new class extends Migration
{
    public function up(): void
    {
        // Table vinyles supprimée - rien à faire
    }

    public function down(): void
    {
        // Table vinyles n'existe plus
    }
};