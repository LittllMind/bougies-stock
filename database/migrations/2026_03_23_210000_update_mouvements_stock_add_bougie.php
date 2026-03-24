<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTE: Cette migration est désactivée car la table mouvements_stock
     * utilise maintenant une structure polymorphique (stockable_type/stockable_id)
     * et la colonne produit_type n'existe plus.
     * Le support des bougies est géré via les colonnes polymorphiques.
     */
    public function up(): void
    {
        // Rien à faire - déjà migré vers structure polymorphique
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rien à annuler
    }
};
