<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Dr. Njama',
                'username' => 'dr.njama',
                'email' => 'admin@njamapharmacy.co.tz',
                'phone' => '0715362428',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('Admin@12345'),
            ],
            [
                'name' => 'Abishai Evaristo (Technician)',
                'username' => 'abishai',
                'email' => 'abishai@njamapharmacy.co.tz',
                'phone' => '0656392400',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('Admin@12345'),
            ],
            [
                'name' => 'Staff Pharmacist',
                'username' => 'pharmacist',
                'email' => 'pharmacist@njamapharmacy.co.tz',
                'phone' => '0712000001',
                'role' => 'pharmacist',
                'is_active' => true,
                'password' => Hash::make('Pharm@12345'),
            ],
            [
                'name' => 'Sales Cashier',
                'username' => 'cashier',
                'email' => 'cashier@njamapharmacy.co.tz',
                'phone' => '0712000002',
                'role' => 'cashier',
                'is_active' => true,
                'password' => Hash::make('Cash@12345'),
            ],
            [
                'name' => 'Financial Auditor',
                'username' => 'auditor',
                'email' => 'auditor@njamapharmacy.co.tz',
                'phone' => '0712000004',
                'role' => 'auditor',
                'is_active' => true,
                'password' => Hash::make('Audit@12345'),
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['username' => $u['username']], $u);
        }
    }
}
