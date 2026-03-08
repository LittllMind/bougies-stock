<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vinyles', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('id')->unique();
            $table->string('artiste')->nullable()->after('nom');
            $table->string('genre')->nullable()->after('modele');
            $table->string('style')->nullable()->after('genre');
        });
    }

    public function down(): void
    {
        Schema::table('vinyles', function (Blueprint $table) {
            $table->dropColumn(['reference', 'artiste', 'genre', 'style']);
        });
    }
};
