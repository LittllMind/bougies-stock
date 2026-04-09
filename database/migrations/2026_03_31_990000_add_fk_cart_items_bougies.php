<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la contrainte foreign key sur bougie_id après création de la table bougies
     */
    public function up(): void
    {
        // bougie_id ajouté dans create_cart_items_table et order_items_table
        // Cette migration est maintenant obsolète
        // Les contraintes FK sont ajoutées directement dans les migrations create_*
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback - les FK sont gérées par les migrations de création
    }
};