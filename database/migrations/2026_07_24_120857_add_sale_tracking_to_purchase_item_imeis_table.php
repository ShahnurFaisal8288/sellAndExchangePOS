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
        Schema::table('purchase_item_imeis', function (Blueprint $table) {
            $table->boolean('is_sold')->default(false)->after('imei_serial');
            $table->foreignId('sale_item_id')
                  ->nullable()
                  ->after('is_sold')
                  ->constrained('sale_items')
                  ->nullOnDelete();

            $table->index(['product_id', 'is_sold']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_item_imeis', function (Blueprint $table) {
            $table->dropForeign(['sale_item_id']);
            $table->dropColumn(['is_sold', 'sale_item_id']);
        });
    }
};
