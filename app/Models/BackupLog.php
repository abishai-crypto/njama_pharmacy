<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'disk',
        'size_bytes',
        'status',
        'created_by',
        'notes',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
