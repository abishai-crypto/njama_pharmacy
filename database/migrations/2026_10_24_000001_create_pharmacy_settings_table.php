<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_settings', function (Blueprint $table) {
            $table->id();
            $table->string('pharmacy_name')->default('NJAMA PHARMACY');
            $table->string('registration_number')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('vrn_number')->nullable();
            $table->string('owner_doctor_name')->default('Dr. Njama');
            $table->string('owner_phone')->default('0715362428');
            $table->string('technician_name')->default('Abishai Evaristo');
            $table->string('technician_phone')->default('0656392400');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('currency')->default('TZS');
            $table->string('currency_symbol')->default('Tsh');
            $table->decimal('default_tax_percentage', 5, 2)->default(0.00);
            $table->integer('expiry_warning_days_near')->default(30);
            $table->integer('expiry_warning_days_medium')->default(60);
            $table->integer('expiry_warning_days_far')->default(90);
            $table->text('receipt_footer')->nullable();
            $table->string('receipt_printer_type')->default('browser'); // browser, thermal_58, thermal_80
            $table->boolean('enforce_fefo')->default(true);
            $table->boolean('block_expired_sales')->default(true);
            $table->boolean('require_prescription_for_rx_drugs')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_settings');
    }
};
