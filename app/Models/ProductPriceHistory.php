<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'old_purchase_cost',
        'new_purchase_cost',
        'old_selling_price',
        'new_selling_price',
        'changed_by',
        'reason',
    ];

    protected $casts = [
        'old_purchase_cost' => 'decimal:2',
        'new_purchase_cost' => 'decimal:2',
        'old_selling_price' => 'decimal:2',
        'new_selling_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
