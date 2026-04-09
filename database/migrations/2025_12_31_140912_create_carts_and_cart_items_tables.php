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
        // Cette migration était une erreur - remplacée par create_carts_table et create_cart_items_table
        // On la supprime proprement
        Schema::dropIfExists('carts_and_cart_items_tables');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rien à rollback - la table n'est plus utilisée
    }
};