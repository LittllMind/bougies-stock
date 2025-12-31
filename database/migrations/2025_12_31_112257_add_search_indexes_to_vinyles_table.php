<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vinyles', function (Blueprint $table) {
            $table->index('nom_vinyle'); // Recherche par nom
            $table->index('modele');     // Filtre modèle
            $table->index('prix_vente'); // Tri/filtre prix
            $table->index('stock');      // Exclure ruptures
        });
        
        Schema::table('fonds', function (Blueprint $table) {
            $table->index('type_fond'); // Filtre miroir/doré
        });
    }

    public function down(): void
    {
        Schema::table('vinyles', function (Blueprint $table) {
            $table->dropIndex(['nom_vinyle']);
            $table->dropIndex(['modele']);
            $table->dropIndex(['prix_vente']);
            $table->dropIndex(['stock']);
        });
        
        Schema::table('fonds', function (Blueprint $table) {
            $table->dropIndex(['type_fond']);
        });
    }
};
