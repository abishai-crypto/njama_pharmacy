<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pharmacy Rent (Kodi ya Chumba)', 'slug' => 'pharmacy-rent', 'description' => 'Monthly / quarterly pharmacy premises rent'],
            ['name' => 'Electricity & LUKU (Umeme)', 'slug' => 'electricity-luku', 'description' => 'Electricity power and backup bills'],
            ['name' => 'Water Bills (Maji ya DAWASA)', 'slug' => 'water-bills', 'description' => 'Water utility expenses'],
            ['name' => 'Internet & Communications (Mtandao na Mawasiliano)', 'slug' => 'internet-communications', 'description' => 'Pharmacy Wi-Fi, airtime, router subscriptions'],
            ['name' => 'Staff Salaries & Allowances (Mishahara na Posho)', 'slug' => 'staff-salaries-allowances', 'description' => 'Staff wages, commissions, lunch and transport allowances'],
            ['name' => 'Transport & Freight (Usafirishaji wa Dawa)', 'slug' => 'transport-freight', 'description' => 'Medicine delivery, courier, and local transit costs'],
            ['name' => 'Stationery & Receipt Rolls (Vifaa vya Ofisi na Risiti)', 'slug' => 'stationery-receipt-rolls', 'description' => 'Thermal paper rolls, dispensing envelopes, pens, paper'],
            ['name' => 'Licenses, Permits & Pharmacy Council Fees', 'slug' => 'licenses-permits-fees', 'description' => 'TMDA, Pharmacy Council of Tanzania, municipal business licenses'],
            ['name' => 'Equipment Maintenance & Cleaning (Ukarabati na Usafi)', 'slug' => 'maintenance-cleaning', 'description' => 'Air conditioning, fridge servicing, cleaning supplies'],
            ['name' => 'General Operating Expenses (Matumizi Mengineyo)', 'slug' => 'general-operating-expenses', 'description' => 'Miscellaneous daily pharmacy operational costs'],
        ];

        foreach ($categories as $cat) {
            ExpenseCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
