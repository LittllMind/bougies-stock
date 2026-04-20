<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration legacy ligne_ventes - ARCHIVÉE
 * La table n'est plus utilisée, création minimale sans FK
 */
return new class extends Migration
{
    public function up(): void
    {
        // Table vide sans FK pour compatibilité historique
        if (!Schema::hasTable('ligne_ventes')) {
            Schema::create('ligne_ventes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vente_id')->nullable();
                $table->unsignedBigInteger('vinyle_id')->nullable();
                $table->string('titre_vinyle')->nullable();
                $table->integer('quantite')->default(0);
                $table->decimal('prix_unitaire', 8, 2)->default(0);
                $table->decimal('total', 10, 2)->default(0);
                $table->string('fond')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_ventes');
    }
};