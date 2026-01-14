<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'refund_number',
        'refund_amount',
        'refund_method',
        'mobile_money_provider',
        'mobile_money_number',
        'mobile_money_name',
        'bank_name',
        'account_number',
        'account_name',
        'account_type',
        'branch_name',
        'status',
        'rejection_reason',
        'admin_notes',
        'processed_by',
        'processed_at',
        'refund_reference',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Generate a unique refund number
     */
    public static function generateRefundNumber(): string
    {
        $prefix = 'REF-' . date('Ymd') . '-';
        $counter = 1;
        
        do {
            $number = $prefix . str_pad($counter, 4, '0', STR_PAD_LEFT);
            $exists = self::where('refund_number', $number)->exists();
            $counter++;
            
            if ($counter > 9999) {
                $number = $prefix . time() . '-' . rand(100, 999);
                $exists = self::where('refund_number', $number)->exists();
            }
        } while ($exists);
        
        return $number;
    }

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Business Logic
    public function approve($userId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'processed_by' => $userId,
            'admin_notes' => $notes,
        ]);
    }

    public function reject($userId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'processed_by' => $userId,
            'rejection_reason' => $reason,
        ]);
    }

    public function markAsProcessed($userId, $refundReference = null)
    {
        $this->update([
            'status' => 'processed',
            'processed_by' => $userId,
            'processed_at' => now(),
            'refund_reference' => $refundReference,
        ]);
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
        ]);
    }
}
