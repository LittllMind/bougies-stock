<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Bug #4: Ajouter onDelete('cascade') sur toutes les FK liées aux ventes
     */
    public function up(): void
    {
        // 1. Corriger ligne_ventes
        if (Schema::hasTable('ligne_ventes')) {
            Schema::table('ligne_ventes', function (Blueprint $table) {
                // Supprimer les contraintes existantes si présentes
                try {
                    $table->dropForeign(['vente_id']);
                } catch (\Exception $e) {}
                try {
                    $table->dropForeign(['vinyle_id']);
                } catch (\Exception $e) {}

                // Recréer avec onDelete cascade
                $table->foreign('vente_id')->references('id')->on('ventes')->onDelete('cascade');
                $table->foreign('vinyle_id')->references('id')->on('vinyles')->onDelete('cascade');
            });
        }

        // 2. Corriger ventes si user_id existe
        if (Schema::hasTable('ventes') && Schema::hasColumn('ventes', 'user_id')) {
            Schema::table('ventes', function (Blueprint $table) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {}
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Les contraintes restent, on ne les supprime pas en down
        // pour éviter les erreurs si les tables ont des données
    }
};
