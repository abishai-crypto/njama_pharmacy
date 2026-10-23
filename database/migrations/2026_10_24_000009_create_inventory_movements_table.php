<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->nullOnDelete();
            $table->enum('movement_type', [
                'purchase_receipt',
                'sale',
                'sale_return',
                'adjustment_in',
                'adjustment_out',
                'quarantine',
                'quarantine_release',
                'write_off',
                'opening_stock'
            ]);
            $table->decimal('quantity', 12, 2); // positive for addition, negative for deduction
            $table->decimal('balance_after', 12, 2);
            $table->decimal('unit_cost', 14, 2)->default(0.00);
            $table->string('reference_type')->nullable(); // Sale, Purchase, Adjustment, etc.
            $table->string('reference_id')->nullable();
            $table->string('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id', 'created_at']);
            $table->index(['batch_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
