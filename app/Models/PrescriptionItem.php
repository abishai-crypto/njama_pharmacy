<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'product_id',
        'dosage_instructions',
        'prescribed_quantity',
        'dispensed_quantity',
        'duration_days',
        'notes',
    ];

    protected $casts = [
        'prescribed_quantity' => 'decimal:2',
        'dispensed_quantity' => 'decimal:2',
        'duration_days' => 'integer',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
