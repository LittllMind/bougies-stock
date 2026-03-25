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
        Schema::table('bougies', function (Blueprint $table) {
            $table->string('image')->nullable()->after('nom');
            $table->string('slug')->nullable()->after('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bougies', function (Blueprint $table) {
            $table->dropColumn(['image', 'slug']);
        });
    }
};
