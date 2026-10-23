<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_number',
        'customer_id',
        'patient_name',
        'patient_age',
        'patient_gender',
        'patient_phone',
        'prescriber_name',
        'prescriber_hospital',
        'prescriber_phone',
        'prescriber_reg_no',
        'prescription_date',
        'diagnosis_summary',
        'attachment_path',
        'status',
        'verified_by',
        'notes',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'patient_age' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function dispensingRecords()
    {
        return $this->hasMany(DispensingRecord::class);
    }
}
