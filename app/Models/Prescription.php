<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'approved_by',
        'prescription_number',
        'doctor_name',
        'patient_name',
        'prescription_date',
        'status',
        'file_path',
        'notes',
        'rejection_reason',
        'approved_at',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function approve($userId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function reject($userId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $userId,
            'rejection_reason' => $reason,
        ]);
    }
}
