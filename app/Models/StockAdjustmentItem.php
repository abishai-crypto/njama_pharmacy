<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_id',
        'product_id',
        'batch_id',
        'current_quantity',
        'adjusted_quantity',
        'difference_quantity',
        'unit_cost',
        'notes',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'adjusted_quantity' => 'decimal:2',
        'difference_quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function adjustment()
    {
        return $this->belongsTo(StockAdjustment::class, 'adjustment_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batch_id');
    }
}
