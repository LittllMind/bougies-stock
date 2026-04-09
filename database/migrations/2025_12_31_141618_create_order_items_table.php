<?php
// database/migrations/xxxx_create_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // 🕯️ Lien vers la bougie (sans FK, ajoutée après)
            $table->foreignId('bougie_id')->nullable();
            
            // Snapshot des données au moment de la commande
            $table->string('nom_bougie');
            $table->string('parfum')->nullable();
            $table->string('reference_bougie')->nullable();
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 8, 2);
            $table->decimal('total', 10, 2)->default(0); // quantite * prix_unitaire
            
            $table->timestamps();
            
            // Index
            $table->index(['order_id', 'bougie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};