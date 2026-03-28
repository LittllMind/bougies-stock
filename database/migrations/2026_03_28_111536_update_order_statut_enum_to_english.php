<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL nécessite suppression et recréation pour modifier un ENUM
        DB::statement("ALTER TABLE orders MODIFY COLUMN statut ENUM('pending', 'paid', 'processing', 'ready', 'shipped', 'cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN statut ENUM('en_attente', 'payee', 'en_preparation', 'prete', 'livree', 'annulee') DEFAULT 'en_attente'");
    }
};
