<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('client_nom', 100);
            $table->string('client_telephone', 20);
            $table->string('client_email', 100)->nullable();
            $table->date('date');
            $table->time('heure');
            $table->enum('prestation', ['express', 'essentiel', 'premium']);
            $table->decimal('prix', 8, 2);
            $table->enum('statut', ['nouveau', 'confirme', 'annule', 'termine'])->default('nouveau');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('reservations');
    }
};
