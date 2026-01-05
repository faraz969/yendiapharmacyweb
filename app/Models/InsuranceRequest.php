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
        
        // Send SMS notification
        $this->sendApprovalSms();
    }

    public function reject($userId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $userId,
            'rejection_reason' => $reason,
        ]);
        
        // Send SMS notification
        $this->sendRejectionSms($reason);
    }

    /**
     * Send SMS notification when insurance request is approved
     */
    private function sendApprovalSms()
    {
        try {
            $smsService = app(\App\Services\SmsService::class);
            
            // Use customer_phone from the request
            $phoneNumber = $this->customer_phone;
            
            // If no customer_phone and user exists, try user's phone
            if (!$phoneNumber && $this->user_id) {
                // Load user relationship if not already loaded
                if (!$this->relationLoaded('user')) {
                    $this->load('user');
                }
                $phoneNumber = $this->user->phone ?? null;
            }
            
            if ($phoneNumber) {
                $message = "Your insurance request #{$this->request_number} has been approved. ";
                $message .= "Please go to your insurance request details to select delivery location and pay for delivery fee.";
                
                $smsService->sendSms($phoneNumber, $message);
            }
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Illuminate\Support\Facades\Log::error('Failed to send approval SMS notification', [
                'insurance_request_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send SMS notification when insurance request is rejected
     */
    private function sendRejectionSms($reason)
    {
        try {
            $smsService = app(\App\Services\SmsService::class);
            
            // Use customer_phone from the request
            $phoneNumber = $this->customer_phone;
            
            // If no customer_phone and user exists, try user's phone
            if (!$phoneNumber && $this->user_id) {
                // Load user relationship if not already loaded
                if (!$this->relationLoaded('user')) {
                    $this->load('user');
                }
                $phoneNumber = $this->user->phone ?? null;
            }
            
            if ($phoneNumber) {
                $message = "Your insurance request #{$this->request_number} has been rejected. ";
                $message .= "Reason: {$reason}";
                
                $smsService->sendSms($phoneNumber, $message);
            }
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Illuminate\Support\Facades\Log::error('Failed to send rejection SMS notification', [
                'insurance_request_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
