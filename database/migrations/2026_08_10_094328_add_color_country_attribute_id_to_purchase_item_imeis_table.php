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
            $table->foreignId('color_attribute_id')
                  ->nullable()
                  ->after('imei_serial')
                  ->constrained('attributes')
                  ->nullOnDelete();

            $table->foreignId('country_attribute_id')
                  ->nullable()
                  ->after('color_attribute_id')
                  ->constrained('attributes')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_item_imeis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('color_attribute_id');
            $table->dropConstrainedForeignId('country_attribute_id');
        });
    }
};
