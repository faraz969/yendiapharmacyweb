<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'boundaries',
        'delivery_fee',
        'min_order_amount',
        'estimated_delivery_hours',
        'is_active',
    ];

    protected $casts = [
        'boundaries' => 'array',
        'delivery_fee' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'estimated_delivery_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function calculateDeliveryFee($orderAmount = null)
    {
        // Always return the delivery fee - min_order_amount is a minimum order requirement,
        // not a free delivery threshold
        return $this->delivery_fee;
    }
    
    /**
     * Check if order amount meets minimum order requirement
     * 
     * @param float $orderAmount
     * @return bool
     */
    public function meetsMinimumOrderAmount($orderAmount)
    {
        if ($this->min_order_amount == null || $this->min_order_amount <= 0) {
            return true; // No minimum requirement
        }
        return $orderAmount >= $this->min_order_amount;
    }

    public function isPointInZone($latitude, $longitude)
    {
        // Simple implementation - can be enhanced with proper polygon checking
        // For now, return true if zone is active
        return $this->is_active;
    }
}
