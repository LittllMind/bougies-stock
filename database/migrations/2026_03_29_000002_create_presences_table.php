<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lieu_id')->constrained('lieux');
            $table->date('date');
            $table->time('heure_debut')->default('09:00');
            $table->time('heure_fin')->default('19:00');
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('presences');
    }
};
