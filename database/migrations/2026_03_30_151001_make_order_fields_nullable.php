<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('telephone')->nullable()->change();
            $table->string('adresse')->nullable()->change();
            $table->string('code_postal')->nullable()->change();
            $table->string('ville')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('telephone')->nullable(false)->change();
            $table->string('adresse')->nullable(false)->change();
            $table->string('code_postal')->nullable(false)->change();
            $table->string('ville')->nullable(false)->change();
        });
    }
};