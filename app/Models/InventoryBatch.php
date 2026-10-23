<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventoryBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'supplier_id',
        'purchase_id',
        'quantity_received',
        'quantity_remaining',
        'purchase_cost',
        'selling_price',
        'manufacturing_date',
        'expiry_date',
        'date_received',
        'storage_location',
        'status',
        'created_by',
    ];

    protected $casts = [
        'quantity_received' => 'decimal:2',
        'quantity_remaining' => 'decimal:2',
        'purchase_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'date_received' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class, 'batch_id');
    }

    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return Carbon::today()->greaterThan($this->expiry_date);
    }

    public function isExpiringWithin(int $days): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        $today = Carbon::today();
        return $this->expiry_date->greaterThanOrEqualTo($today) &&
               $this->expiry_date->lessThanOrEqualTo($today->copy()->addDays($days));
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        return Carbon::today()->diffInDays($this->expiry_date, false);
    }
}
