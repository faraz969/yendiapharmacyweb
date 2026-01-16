<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'for_admin',
        'order_id',
        'insurance_request_id',
        'refund_request_id',
        'title',
        'message',
        'type',
        'image',
        'link',
        'is_active',
        'start_date',
        'end_date',
        'priority',
        'is_read',
    ];

    protected $casts = [
        'for_admin' => 'boolean',
        'is_active' => 'boolean',
        'is_read' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the notification
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the insurance request associated with the notification
     */
    public function insuranceRequest()
    {
        return $this->belongsTo(InsuranceRequest::class);
    }

    /**
     * Get the refund request associated with the notification
     */
    public function refundRequest()
    {
        return $this->belongsTo(RefundRequest::class);
    }

    /**
     * Scope to get active notifications
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }
}
