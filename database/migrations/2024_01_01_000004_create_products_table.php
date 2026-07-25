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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Nullable: every product is created inline while entering a
            // purchase, so category/brand aren't picked up front.
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();

            $table->string('name', 150);
            $table->string('model', 100)->nullable();

            // Country of origin/manufacture, typed in during purchase entry.
            // e.g. "US", "JP", "CN".
            $table->string('country_code', 10)->nullable();

            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sale_price', 10, 2)->default(0); // set later, not captured at purchase time
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_alert')->default(5);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
