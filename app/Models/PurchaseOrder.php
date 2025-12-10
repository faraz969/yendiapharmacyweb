<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'created_by',
        'po_number',
        'order_date',
        'expected_delivery_date',
        'received_date',
        'status',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'received_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    // Business Logic
    public function calculateTotal()
    {
        $total = $this->items()->sum('total_cost');
        $this->update(['total_amount' => $total]);
        return $total;
    }

    public function markAsReceived()
    {
        $this->update([
            'status' => 'received',
            'received_date' => now(),
        ]);
    }

    public function isFullyReceived()
    {
        return $this->items()->get()->every(function ($item) {
            return $item->received_quantity >= $item->quantity;
        });
    }
}
