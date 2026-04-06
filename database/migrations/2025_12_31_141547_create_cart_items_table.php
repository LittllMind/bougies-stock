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

            $table->foreignId('vinyle_id')
                ->constrained()
                ->cascadeOnDelete();

            // Note: fond_id retiré - refactoring en cours

            $table->integer('quantite');
            $table->decimal('prix_unitaire', 8, 2);
            $table->timestamps();

            // ✅ Unicité : même vinyle dans le même panier
            $table->unique(
                ['cart_id', 'vinyle_id'],
                'unique_cart_vinyle'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
