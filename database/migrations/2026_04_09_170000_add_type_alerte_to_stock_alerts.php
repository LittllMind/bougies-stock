<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_alerts', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_alerts', 'type_alerte')) {
                $table->enum('type_alerte', ['sous_seuil', 'rupture', 'surstock'])->default('sous_seuil')->after('seuil_alerte');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_alerts', function (Blueprint $table) {
            if (Schema::hasColumn('stock_alerts', 'type_alerte')) {
                $table->dropColumn('type_alerte');
            }
        });
    }
};
