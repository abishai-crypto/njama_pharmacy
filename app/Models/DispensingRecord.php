<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispensingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispensing_number',
        'prescription_id',
        'sale_id',
        'product_id',
        'batch_id',
        'quantity',
        'patient_name',
        'instructions',
        'dispensed_by',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batch_id');
    }

    public function dispenser()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }
}
