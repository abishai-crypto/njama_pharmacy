<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_id',
        'sale_item_id',
        'batch_id',
        'quantity',
        'unit_price',
        'subtotal',
        'restock_item',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'restock_item' => 'boolean',
    ];

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class, 'batch_id');
    }
}
