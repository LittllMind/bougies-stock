<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lieux', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->text('adresse')->nullable();
            $table->string('ville', 255)->nullable();
            $table->string('code_postal', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lieux');
    }
};
