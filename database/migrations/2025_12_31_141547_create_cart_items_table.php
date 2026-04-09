<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                ->constrained()
                ->cascadeOnDelete();

            // 🕯️ Lien vers la bougie - sans contrainte temporairement (ajoutée après create_bougies)
            $table->foreignId('bougie_id')
                ->nullable();

            $table->integer('quantite');
            $table->decimal('prix_unitaire', 8, 2);
            $table->timestamps();

            // ✅ Index pour performance
            $table->index(['cart_id', 'bougie_id'], 'cart_bougie_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};