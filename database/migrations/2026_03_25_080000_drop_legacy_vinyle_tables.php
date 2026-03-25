<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pour MySQL : Désactiver les contraintes FK
        // Pour SQLite : On laisse Schema gérer
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        
        // Supprimer les tables legacy vinyles dans le bon ordre
        Schema::dropIfExists('ligne_ventes');  // dépend de ventes
        Schema::dropIfExists('ventes');        // dépend de vinyles
        Schema::dropIfExists('fonds');         // dépend de vinyles  
        Schema::dropIfExists('vinyles');        // table principale
        
        // Réactiver les contraintes FK pour MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    public function down(): void
    {
        // Les tables legacy ne sont pas recréées - migration unidirectionnelle
        // Si rollback nécessaire: restaurer depuis backup Git
        throw new \Exception('Rollback non supporté. Restaurer les migrations legacy depuis l\'archive Git si besoin.');
    }
};
