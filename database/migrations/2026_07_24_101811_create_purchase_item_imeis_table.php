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
        // One row per physical unit's IMEI/serial for a purchase line item.
        // A line item with quantity = 5 can have up to 5 rows here.
        // product_id is denormalized from purchase_items for fast lookups
        // (e.g. "find the IMEI history for this product") without a join
        // through purchases.
        Schema::create('purchase_item_imeis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_item_id')->constrained('purchase_items')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('imei_serial', 100);
            $table->timestamps();

            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_item_imeis');
    }
};
