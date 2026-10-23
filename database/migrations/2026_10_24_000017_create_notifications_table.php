<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // low_stock, expiring_soon, expired_stock, purchase_pending, shift_unreconciled
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // null = all users or role based
            $table->string('target_role')->nullable(); // admin, pharmacist, etc.
            $table->timestamps();

            $table->index(['is_read', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_notifications');
    }
};
