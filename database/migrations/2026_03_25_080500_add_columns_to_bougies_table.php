<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // NOTE: Cette migration est désactivée car les colonnes slug, image, etc.
        // ne sont pas utilisées dans la base actuelle. Le modèle Bougie utilise
        // uniquement les colonnes définies dans create_bougies_table.
        // 
        // Pour ajouter ces colonnes plus tard, créer une nouvelle migration.
    }

    public function down(): void
    {
        // Aucune action nécessaire
    }
};
