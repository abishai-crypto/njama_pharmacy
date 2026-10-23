<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'generic_name',
        'brand_name',
        'category_id',
        'unit_id',
        'dosage_form',
        'strength',
        'pack_size',
        'barcode',
        'requires_prescription',
        'is_restricted',
        'purchase_cost',
        'selling_price',
        'minimum_stock_alert',
        'storage_conditions',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'requires_prescription' => 'boolean',
        'is_restricted' => 'boolean',
        'purchase_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'minimum_stock_alert' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(UnitOfMeasure::class, 'unit_id');
    }

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(ProductPriceHistory::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Active batches with stock, ordered by First Expiry First Out (FEFO)
    public function eligibleBatchesFefo()
    {
        $today = Carbon::today()->toDateString();
        return $this->batches()
            ->where('status', 'active')
            ->where('quantity_remaining', '>', 0)
            ->where(function ($query) use ($today) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', $today);
            })
            ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END, expiry_date ASC');
    }

    // Total available sellable stock
    public function getTotalStockAttribute()
    {
        $today = Carbon::today()->toDateString();
        return (float) $this->batches()
            ->where('status', 'active')
            ->where(function ($query) use ($today) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', $today);
            })
            ->sum('quantity_remaining');
    }

    // Total expired stock that requires quarantine or write-off
    public function getExpiredStockAttribute()
    {
        $today = Carbon::today()->toDateString();
        return (float) $this->batches()
            ->where('quantity_remaining', '>', 0)
            ->where('expiry_date', '<', $today)
            ->sum('quantity_remaining');
    }

    // Total quarantined stock
    public function getQuarantinedStockAttribute()
    {
        return (float) $this->batches()
            ->where('status', 'quarantined')
            ->sum('quantity_remaining');
    }

    public function getDisplayNameAttribute(): string
    {
        $name = $this->generic_name;
        if ($this->brand_name && $this->brand_name !== $this->generic_name) {
            $name .= ' (' . $this->brand_name . ')';
        }
        if ($this->strength) {
            $name .= ' ' . $this->strength;
        }
        return $name;
    }
}
