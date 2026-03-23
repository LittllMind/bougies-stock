<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'enum pour ajouter 'bougie'
        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->enum('produit_type', ['vinyle', 'miroir', 'dore', 'pochette', 'bougie'])
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mouvements_stock', function (Blueprint $table) {
            $table->enum('produit_type', ['vinyle', 'miroir', 'dore', 'pochette'])
                ->change();
        });
    }
};
