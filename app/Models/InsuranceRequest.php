<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'insurance_company_id',
        'request_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'insurance_number',
        'card_front_image',
        'card_back_image',
        'prescription_image',
        'status',
        'approved_by',
        'admin_notes',
        'rejection_reason',
        'approved_at',
        'delivery_address_id',
        'delivery_zone_id',
        'delivery_type',
        'order_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function insuranceCompany()
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(DeliveryAddress::class);
    }

    public function deliveryZone()
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InsuranceRequestItem::class);
    }

    public static function generateRequestNumber(): string
    {
        $prefix = 'INS-' . date('Ymd') . '-';
        $counter = 1;
        
        do {
            $number = $prefix . str_pad($counter, 4, '0', STR_PAD_LEFT);
            $exists = self::where('request_number', $number)->exists();
            if (!$exists) {
                return $number;
            }
            $counter++;
        } while ($counter < 10000);
        
        throw new \Exception('Unable to generate unique request number');
    }

    public function approve($userId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
            'admin_notes' => $notes,
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
