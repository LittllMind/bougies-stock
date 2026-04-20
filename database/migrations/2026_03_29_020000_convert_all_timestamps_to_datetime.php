<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['bougies', 'cart_items', 'carts', 'orders', 'order_items', 'payments', 'stock_alerts', 'addresses', 'media', 'mouvements_stock'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    try {
                        if (Schema::hasColumn($tableName, 'created_at')) {
                            $table->dateTime('created_at')->nullable()->change();
                        }
                        if (Schema::hasColumn($tableName, 'updated_at')) {
                            $table->dateTime('updated_at')->nullable()->change();
                        }
                        if (Schema::hasColumn($tableName, 'deleted_at')) {
                            $table->dateTime('deleted_at')->nullable()->change();
                        }
                        if (Schema::hasColumn($tableName, 'resolved_at')) {
                            $table->dateTime('resolved_at')->nullable()->change();
                        }
                        if (Schema::hasColumn($tableName, 'email_verified_at')) {
                            $table->dateTime('email_verified_at')->nullable()->change();
                        }
                        if (Schema::hasColumn($tableName, 'validee_at')) {
                            $table->dateTime('validee_at')->nullable()->change();
                        }
                        if (Schema::hasColumn($tableName, 'preparee_at')) {
                            $table->dateTime('preparee_at')->nullable()->change();
                        }
                        if (Schema::hasColumn($tableName, 'prete_at')) {
                            $table->dateTime('prete_at')->nullable()->change();
                        }
                        if (Schema::hasColumn($tableName, 'livree_at')) {
                            $table->dateTime('livree_at')->nullable()->change();
                        }
                        if (Schema::hasColumn($tableName, 'annulee_at')) {
                            $table->dateTime('annulee_at')->nullable()->change();
                        }
                        if (Schema::hasColumn($tableName, 'expires_at')) {
                            $table->dateTime('expires_at')->nullable()->change();
                        }
                    } catch (\Exception $e) {
                        // Ignore if not exists
                    }
                });
            }
        }
    }

    public function down(): void {}
};
