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
        Schema::create('bougies', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('image')->nullable();
            $table->string('parfum');           
            $table->string('nom');              
            $table->string('collection')->nullable();  
            $table->string('format')->nullable();      
            $table->string('type_cire')->nullable();   
            $table->integer('temps_brulure')->nullable(); 
            $table->text('notes')->nullable();   
            $table->decimal('prix', 10, 2);
            $table->integer('quantite')->default(0);
            $table->integer('seuil_alerte')->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bougies');
    }
};
