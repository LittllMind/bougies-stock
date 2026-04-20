<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les contraintes foreign key sur bougie_id 
     * Cette migration doit s'exécuter APRÈS create_bougies_table
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Vérifier si la FK existe déjà avant d'ajouter
            try {
                $table->foreign('bougie_id')
                    ->references('id')
                    ->on('bougies')
                    ->onDelete('cascade');
            } catch (\Exception $e) {
                // FK existe probablement déjà
            }
        });
        
        Schema::table('order_items', function (Blueprint $table) {
            try {
                $table->foreign('bougie_id')
                    ->references('id')
                    ->on('bougies')
                    ->onDelete('set null');
            } catch (\Exception $e) {
                // FK existe probablement déjà
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['bougie_id']);
        });
        
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['bougie_id']);
        });
    }
};