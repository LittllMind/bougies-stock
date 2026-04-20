<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // bougie_id ajouté dans create_cart_items_table
        // Gardée pour compatibilité historique
    }

    public function down(): void
    {
        // Pas de rollback - colonne gérée par create_cart_items_table
    }
};