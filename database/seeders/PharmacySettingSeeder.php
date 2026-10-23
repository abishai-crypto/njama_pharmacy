<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PharmacySetting;

class PharmacySettingSeeder extends Seeder
{
    public function run(): void
    {
        PharmacySetting::updateOrCreate(
            ['id' => 1],
            [
                'pharmacy_name' => 'NJAMA PHARMACY',
                'registration_number' => 'PCT/RET/2026/8942',
                'tin_number' => '142-985-301',
                'vrn_number' => '40-029182-Z',
                'owner_doctor_name' => 'Dr. Njama',
                'owner_phone' => '0715362428',
                'technician_name' => 'Abishai Evaristo',
                'technician_phone' => '0656392400',
                'email' => 'info@njamapharmacy.co.tz',
                'address' => 'Dar es Salaam, Tanzania',
                'currency' => 'TZS',
                'currency_symbol' => 'Tsh',
                'default_tax_percentage' => 0.00,
                'expiry_warning_days_near' => 30,
                'expiry_warning_days_medium' => 60,
                'expiry_warning_days_far' => 90,
                'receipt_footer' => 'Asante kwa kuja NJAMA PHARMACY. Afya Yako, Wajibu Wetu! Dawa hazirudishwi baada ya siku 3.',
                'receipt_printer_type' => 'thermal_80',
                'enforce_fefo' => true,
                'block_expired_sales' => true,
                'require_prescription_for_rx_drugs' => true,
            ]
        );
    }
}
