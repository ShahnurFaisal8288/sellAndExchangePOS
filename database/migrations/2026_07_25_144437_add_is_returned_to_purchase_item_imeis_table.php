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
            $table->boolean('is_returned')->default(false)->after('is_sold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_item_imeis', function (Blueprint $table) {
            $table->dropColumn('is_returned');
        });
    }
};
