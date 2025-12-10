<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'purchase_order_id',
        'batch_number',
        'expiry_date',
        'manufacturing_date',
        'quantity',
        'available_quantity',
        'cost_price',
        'is_expired',
        'expired_at',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'manufacturing_date' => 'date',
        'quantity' => 'integer',
        'available_quantity' => 'integer',
        'cost_price' => 'decimal:2',
        'is_expired' => 'boolean',
        'expired_at' => 'datetime',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Business Logic
    public function checkExpiry()
    {
        if ($this->expiry_date < now() && !$this->is_expired) {
            $this->update([
                'is_expired' => true,
                'expired_at' => now(),
            ]);
            return true;
        }
        return false;
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date->isBefore(now()->addDays($days)) 
            && $this->expiry_date->isAfter(now())
            && !$this->is_expired;
    }

    public function getDaysUntilExpiryAttribute()
    {
        return now()->diffInDays($this->expiry_date, false);
    }

    public function reduceStock($quantity)
    {
        if ($this->available_quantity >= $quantity) {
            $this->decrement('available_quantity', $quantity);
            return true;
        }
        return false;
    }
}
