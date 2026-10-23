<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_name',
        'registration_number',
        'tin_number',
        'vrn_number',
        'owner_doctor_name',
        'owner_phone',
        'technician_name',
        'technician_phone',
        'email',
        'address',
        'currency',
        'currency_symbol',
        'default_tax_percentage',
        'expiry_warning_days_near',
        'expiry_warning_days_medium',
        'expiry_warning_days_far',
        'receipt_footer',
        'receipt_printer_type',
        'enforce_fefo',
        'block_expired_sales',
        'require_prescription_for_rx_drugs',
    ];

    protected $casts = [
        'default_tax_percentage' => 'decimal:2',
        'expiry_warning_days_near' => 'integer',
        'expiry_warning_days_medium' => 'integer',
        'expiry_warning_days_far' => 'integer',
        'enforce_fefo' => 'boolean',
        'block_expired_sales' => 'boolean',
        'require_prescription_for_rx_drugs' => 'boolean',
    ];

    public static function getSettings(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'pharmacy_name' => 'NJAMA PHARMACY',
                'owner_doctor_name' => 'Dr. Njama',
                'owner_phone' => '0715362428',
                'technician_name' => 'Abishai Evaristo',
                'technician_phone' => '0656392400',
                'address' => 'Dar es Salaam, Tanzania',
                'currency' => 'TZS',
                'currency_symbol' => 'Tsh',
                'receipt_footer' => 'Asante kwa kuja NJAMA PHARMACY. Afya Yako, Wajibu Wetu!',
                'expiry_warning_days_near' => 30,
                'expiry_warning_days_medium' => 60,
                'expiry_warning_days_far' => 90,
            ]
        );
    }
}
