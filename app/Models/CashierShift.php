<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashier_id',
        'opened_at',
        'closed_at',
        'opening_cash',
        'counted_cash',
        'expected_cash',
        'cash_difference',
        'discrepancy_reason',
        'total_sales_amount',
        'total_transactions_count',
        'status',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_cash' => 'decimal:2',
        'counted_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
        'total_sales_amount' => 'decimal:2',
        'total_transactions_count' => 'integer',
    ];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'shift_id');
    }
}
