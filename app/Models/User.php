<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'role',
        'is_active',
        'password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPharmacist(): bool
    {
        return in_array($this->role, ['admin', 'pharmacist']);
    }

    public function isCashier(): bool
    {
        return in_array($this->role, ['admin', 'cashier']);
    }

    public function isInventoryOfficer(): bool
    {
        return in_array($this->role, ['admin', 'pharmacist']);
    }

    public function isAuditor(): bool
    {
        return $this->role === 'auditor';
    }

    public function canAccessFinancials(): bool
    {
        return in_array($this->role, ['admin', 'auditor']);
    }

    public function canManageSettings(): bool
    {
        return $this->role === 'admin';
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'cashier_id');
    }

    public function cashierShifts()
    {
        return $this->hasMany(CashierShift::class, 'cashier_id');
    }

    public function currentShift()
    {
        return $this->hasOne(CashierShift::class, 'cashier_id')->where('status', 'open')->latest();
    }

    public function dispensingRecords()
    {
        return $this->hasMany(DispensingRecord::class, 'dispensed_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
