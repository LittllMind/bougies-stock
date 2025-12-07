<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vinyles', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('modele');
            $table->decimal('prix', 8, 2);
            $table->integer('quantite')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vinyles');
    }
};
