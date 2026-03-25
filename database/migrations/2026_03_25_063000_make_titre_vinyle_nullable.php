<?php
// database/migrations/2026_03_25_063000_make_titre_vinyle_nullable.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('titre_vinyle')->nullable()->change();
            $table->string('nom_bougie')->nullable()->after('titre_vinyle');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('titre_vinyle')->nullable(false)->change();
            $table->dropColumn('nom_bougie');
        });
    }
};