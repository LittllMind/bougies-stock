<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // bougie_id ajouté dans create_order_items_table maintenant
        // Gardée pour compatibilité historique
    }

    public function down(): void
    {
        // Pas de rollback - col gère par create_order_items_table
    }
};