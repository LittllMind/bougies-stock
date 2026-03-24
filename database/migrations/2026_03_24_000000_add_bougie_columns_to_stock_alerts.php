<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cette migration est désactivée car la table stock_alerts
        // utilise maintenant une structure polymorphique avec stockable_type/stockable_id
        // et les colonnes (type, stock_actuel, seuil, message) sont déjà présentes.
        // Pas besoin d'ajouter quoi que ce soit.
    }

    public function down(): void
    {
        // Pas de rollback nécessaire
    }
};
