<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Supprimer colonnes legacy vinyles si elles existent
            if (Schema::hasColumn('order_items', 'vinyle_id')) {
                $table->dropColumn('vinyle_id');
            }
            if (Schema::hasColumn('order_items', 'fond_id')) {
                $table->dropColumn('fond_id');
            }
            if (Schema::hasColumn('order_items', 'titre_vinyle')) {
                $table->dropColumn('titre_vinyle');
            }
            if (Schema::hasColumn('order_items', 'artiste_vinyle')) {
                $table->dropColumn('artiste_vinyle');
            }
            if (Schema::hasColumn('order_items', 'reference_vinyle')) {
                $table->dropColumn('reference_vinyle');
            }
            
            // Ajouter colonnes bougies si manquantes
            if (!Schema::hasColumn('order_items', 'nom_bougie')) {
                $table->string('nom_bougie')->nullable();
            }
            if (!Schema::hasColumn('order_items', 'parfum')) {
                $table->string('parfum')->nullable();
            }
            if (!Schema::hasColumn('order_items', 'reference_bougie')) {
                $table->string('reference_bougie')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Pas de rollback nécessaire
    }
};
