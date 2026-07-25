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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
        $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
        $table->string('invoice_no', 50);
        $table->decimal('total_return_value', 10, 2);
        $table->decimal('due_cancelled', 10, 2);
        $table->decimal('cash_refunded', 10, 2);
        $table->date('return_date');
        $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
