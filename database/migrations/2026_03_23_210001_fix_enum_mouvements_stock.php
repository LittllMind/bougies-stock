<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL nécessite une requête brute pour modifier un ENUM
        DB::statement("ALTER TABLE mouvements_stock MODIFY COLUMN produit_type ENUM('vinyle', 'miroir', 'dore', 'pochette', 'bougie')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE mouvements_stock MODIFY COLUMN produit_type ENUM('vinyle', 'miroir', 'dore', 'pochette')");
    }
};
