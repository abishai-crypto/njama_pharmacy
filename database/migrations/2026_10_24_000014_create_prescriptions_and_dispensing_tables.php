<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('patient_name');
            $table->integer('patient_age')->nullable();
            $table->enum('patient_gender', ['male', 'female', 'other'])->nullable();
            $table->string('patient_phone')->nullable();
            $table->string('prescriber_name');
            $table->string('prescriber_hospital')->nullable();
            $table->string('prescriber_phone')->nullable();
            $table->string('prescriber_reg_no')->nullable();
            $table->date('prescription_date');
            $table->text('diagnosis_summary')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['pending', 'dispensed', 'partially_dispensed', 'cancelled'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['prescription_number', 'patient_name']);
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('dosage_instructions'); // e.g. 1 tab 3x daily for 5 days
            $table->decimal('prescribed_quantity', 12, 2);
            $table->decimal('dispensed_quantity', 12, 2)->default(0.00);
            $table->integer('duration_days')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('dispensing_records', function (Blueprint $table) {
            $table->id();
            $table->string('dispensing_number')->unique();
            $table->foreignId('prescription_id')->nullable()->constrained('prescriptions')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->nullOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->string('patient_name');
            $table->string('instructions')->nullable();
            $table->foreignId('dispensed_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'dispensed_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensing_records');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
    }
};
