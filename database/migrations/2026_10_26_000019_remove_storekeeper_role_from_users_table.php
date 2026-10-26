<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Remove any user with inventory_officer role or 'inventory' username
        DB::table('users')
            ->where('role', 'inventory_officer')
            ->orWhere('username', 'inventory')
            ->delete();

        // 2. Alter the enum column on the users table (MySQL / MariaDB)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pharmacist', 'cashier', 'auditor') NOT NULL DEFAULT 'cashier'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pharmacist', 'cashier', 'inventory_officer', 'auditor') NOT NULL DEFAULT 'cashier'");
        }
    }
};
