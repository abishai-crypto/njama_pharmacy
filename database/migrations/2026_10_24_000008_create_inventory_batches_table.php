<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            $table->decimal('quantity_received', 12, 2)->default(0.00);
            $table->decimal('quantity_remaining', 12, 2)->default(0.00);
            $table->decimal('purchase_cost', 14, 2)->default(0.00);
            $table->decimal('selling_price', 14, 2)->default(0.00);
            $table->date('manufacturing_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('date_received')->nullable();
            $table->string('storage_location')->nullable();
            $table->enum('status', ['active', 'quarantined', 'expired', 'depleted', 'recalled'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['product_id', 'status', 'expiry_date'], 'batch_fefo_index');
            $table->index(['expiry_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_batches');
    }
};
