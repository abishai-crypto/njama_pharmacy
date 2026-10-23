<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PharmacySettingSeeder::class,
            CategoryAndUnitSeeder::class,
            ExpenseCategorySeeder::class,
            UserSeeder::class,
            RealMedicineInventorySeeder::class,
        ]);
    }
}
