<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Ajouter bougie_id pour les commandes de bougies
            if (!Schema::hasColumn('cart_items', 'bougie_id')) {
                $table->foreignId('bougie_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete()
                    ->after('cart_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'bougie_id')) {
                $table->dropForeign(['bougie_id']);
                $table->dropColumn('bougie_id');
            }
        });
    }
};
