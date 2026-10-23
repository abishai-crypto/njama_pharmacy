<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('generic_name');
            $table->string('brand_name')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units_of_measure')->nullOnDelete();
            $table->string('dosage_form')->nullable();
            $table->string('strength')->nullable();
            $table->string('pack_size')->nullable();
            $table->string('barcode')->nullable()->index();
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('is_restricted')->default(false);
            $table->decimal('purchase_cost', 14, 2)->default(0.00);
            $table->decimal('selling_price', 14, 2)->default(0.00);
            $table->integer('minimum_stock_alert')->default(5);
            $table->string('storage_conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['generic_name', 'brand_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
