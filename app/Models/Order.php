<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'prescription_id',
        'delivery_address_id',
        'approved_by',
        'packed_by',
        'delivered_by',
        'delivery_zone_id',
        'delivery_type',
        'order_number',
        'status',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_address',
        'latitude',
        'longitude',
        'subtotal',
        'delivery_fee',
        'discount',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_reference',
        'paid_at',
        'approved_at',
        'packed_at',
        'delivered_at',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'packed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function packedBy()
    {
        return $this->belongsTo(User::class, 'packed_by');
    }

    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function deliveryZone()
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(DeliveryAddress::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Business Logic
    public function calculateTotal()
    {
        $subtotal = $this->items()->sum('total_price');
        $total = $subtotal + $this->delivery_fee - $this->discount;
        
        $this->update([
            'subtotal' => $subtotal,
            'total_amount' => $total,
        ]);
        
        return $total;
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

    public function markAsPacked($userId)
    {
        $this->update([
            'status' => 'packed',
            'packed_by' => $userId,
            'packed_at' => now(),
        ]);
    }

    public function assignForDelivery($deliveryPersonId)
    {
        $this->update([
            'status' => 'out_for_delivery',
            'delivered_by' => $deliveryPersonId,
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function requiresPrescription()
    {
        return $this->items()->whereHas('product', function ($query) {
            $query->where('requires_prescription', true);
        })->exists();
    }

    /**
     * Create a notification for the user about order status change
     */
    public function notifyStatusChange($oldStatus = null)
    {
        if (!$this->user_id) {
            return; // No user to notify for guest orders
        }

        $isPickup = ($this->delivery_type ?? 'delivery') === 'pickup';
        
        $statusMessages = [
            'pending' => 'Your order is pending approval',
            'approved' => 'Your order has been approved and is being processed',
            'rejected' => 'Your order has been rejected',
            'packing' => 'Your order is being packed',
            'packed' => $isPickup ? 'Your order has been packed and is ready for pickup' : 'Your order has been packed and is ready for delivery',
            'out_for_delivery' => $isPickup ? 'Your order is ready for pickup' : 'Your order is out for delivery',
            'delivered' => $isPickup ? 'Your order has been collected' : 'Your order has been delivered',
            'cancelled' => 'Your order has been cancelled',
        ];

        $statusTitles = [
            'pending' => 'Order Pending',
            'approved' => 'Order Approved',
            'rejected' => 'Order Rejected',
            'packing' => 'Order Being Packed',
            'packed' => 'Order Packed',
            'out_for_delivery' => $isPickup ? 'Order Ready for Pickup' : 'Order Out for Delivery',
            'delivered' => $isPickup ? 'Order Collected' : 'Order Delivered',
            'cancelled' => 'Order Cancelled',
        ];

        $typeMap = [
            'pending' => 'info',
            'approved' => 'success',
            'rejected' => 'error',
            'packing' => 'info',
            'packed' => 'success',
            'out_for_delivery' => 'info',
            'delivered' => 'success',
            'cancelled' => 'warning',
        ];

        $message = $statusMessages[$this->status] ?? 'Your order status has been updated';
        $title = $statusTitles[$this->status] ?? 'Order Status Updated';
        $type = $typeMap[$this->status] ?? 'info';

        // Only create notification if status actually changed
        if ($oldStatus && $oldStatus === $this->status) {
            return;
        }

        // Create notification only for authenticated users (not guests)
        if ($this->user_id) {
            Notification::create([
                'user_id' => $this->user_id,
                'order_id' => $this->id,
                'title' => $title,
                'message' => "Order #{$this->order_number}: {$message}",
                'type' => $type,
                'link' => '/orders/' . $this->id, // Use relative path for mobile app compatibility
                'is_active' => true,
                'is_read' => false,
            ]);
        }

        // Send SMS notification to both authenticated users and guests
        // Use customer_phone from order (works for both authenticated and guest users)
        $this->sendSmsNotification($message);
    }

    /**
     * Send SMS notification for order status change
     */
    private function sendSmsNotification($message)
    {
        try {
            $smsService = app(\App\Services\SmsService::class);
            
            // For guest users, use customer_phone from order
            // For authenticated users, prefer customer_phone, fallback to user->phone
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
                $smsMessage = "Order #{$this->order_number}: {$message}";
                
                // If order is out for delivery and delivery person is assigned, include their details
                if ($this->status === 'out_for_delivery' && $this->delivered_by) {
                    // Load delivery person relationship if not already loaded
                    if (!$this->relationLoaded('deliveredBy')) {
                        $this->load('deliveredBy');
                    }
                    
                    if ($this->deliveredBy) {
                        $deliveryPersonName = $this->deliveredBy->name;
                        $deliveryPersonPhone = $this->deliveredBy->phone;
                        
                        if ($deliveryPersonPhone) {
                            $smsMessage .= ". Delivery person: {$deliveryPersonName}, Phone: {$deliveryPersonPhone}";
                        } else {
                            $smsMessage .= ". Delivery person: {$deliveryPersonName}";
                        }
                    }
                }
                
                $smsService->sendSms($phoneNumber, $smsMessage);
            }
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Illuminate\Support\Facades\Log::error('Failed to send SMS notification', [
                'order_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
