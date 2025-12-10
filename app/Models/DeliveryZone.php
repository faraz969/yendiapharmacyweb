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

    public function calculateDeliveryFee($orderAmount)
    {
        if ($orderAmount >= $this->min_order_amount) {
            return 0; // Free delivery
        }
        return $this->delivery_fee;
    }

    public function isPointInZone($latitude, $longitude)
    {
        // Simple implementation - can be enhanced with proper polygon checking
        // For now, return true if zone is active
        return $this->is_active;
    }
}
