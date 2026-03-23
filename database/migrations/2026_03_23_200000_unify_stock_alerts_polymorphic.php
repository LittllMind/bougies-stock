<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_alerts', function (Blueprint $table) {
            // Renommer les colonnes alertable_* en stockable_* pour supporter plusieurs types
            // (Vinyle, Fond, Bougie) avec polymorphisme unifié
            if (Schema::hasColumn('stock_alerts', 'alertable_type')) {
                $table->renameColumn('alertable_type', 'stockable_type');
            }
            if (Schema::hasColumn('stock_alerts', 'alertable_id')) {
                $table->renameColumn('alertable_id', 'stockable_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_alerts', function (Blueprint $table) {
            if (Schema::hasColumn('stock_alerts', 'stockable_type')) {
                $table->renameColumn('stockable_type', 'alertable_type');
            }
            if (Schema::hasColumn('stock_alerts', 'stockable_id')) {
                $table->renameColumn('stockable_id', 'alertable_id');
            }
        });
    }
};