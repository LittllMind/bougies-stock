<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fonds', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'Miroir' ou 'Doré'
            $table->string('visuel')->nullable(); // Description ou image
            $table->integer('quantite')->default(0);
            $table->decimal('prix_achat', 8, 2)->default(2.00); // 2€ par défaut
            $table->decimal('prix_vente', 8, 2); // 8€ ou 13€
            $table->timestamps();
            
            // Index pour recherche rapide par type
            $table->index('type');
        });
        
        // Insertion des données initiales
        DB::table('fonds')->insert([
            [
                'type' => 'Miroir',
                'visuel' => 'Pochette plastique transparente miroir',
                'quantite' => 0,
                'prix_achat' => 2.00,
                'prix_vente' => 8.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Doré',
                'visuel' => 'Pochette plastique dorée premium',
                'quantite' => 0,
                'prix_achat' => 2.00,
                'prix_vente' => 13.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fonds');
    }
};
