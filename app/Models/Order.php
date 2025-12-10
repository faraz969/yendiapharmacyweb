<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prescription_id',
        'delivery_address_id',
        'approved_by',
        'packed_by',
        'delivered_by',
        'delivery_zone_id',
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
}
