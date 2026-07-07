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
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();

            // Which real-world case this is
            $table->enum('exchange_type', ['with_receipt', 'no_receipt', 'warranty', 'trade_in']);

            // The sale of the NEW product to the customer
            $table->foreignId('sale_id')->nullable()->constrained('sales')->cascadeOnUpdate()->nullOnDelete();

            // Set only for trade_in — the purchase created for buying the old item
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->cascadeOnUpdate()->nullOnDelete();

            // Was the item originally sold by this shop, or brought in from outside
            $table->enum('old_product_source', ['this_shop', 'external'])->nullable();

            // this_shop: product being returned. external/trade_in: new product row created for the traded-in item
            $table->foreignId('old_product_id')->nullable()->constrained('products')->cascadeOnUpdate()->nullOnDelete();

            // Free-text description when item isn't in the catalog yet
            $table->string('old_product_description', 255)->nullable();

            // Product the customer is taking home
            $table->foreignId('new_product_id')->constrained('products')->cascadeOnUpdate()->restrictOnDelete();

            // Determines whether the item returns to sellable stock
            $table->enum('condition', ['resellable', 'damaged'])->nullable();

            // Snapshot of new product's price at time of exchange
            $table->decimal('new_product_price', 10, 2);

            // Credit given for the old product (after depreciation/policy cut)
            $table->decimal('old_product_return_value', 10, 2)->nullable();

            // = new_product_price - old_product_return_value; negative = refund
            $table->decimal('additional_payment', 10, 2)->nullable();

            $table->date('exchange_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
