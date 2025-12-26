<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'branch_id',
        'request_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'item_name',
        'description',
        'quantity',
        'status',
        'admin_notes',
        'rejection_reason',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public static function generateRequestNumber(): string
    {
        $prefix = 'REQ-' . date('Ymd') . '-';
        $counter = 1;
        
        do {
            $number = $prefix . str_pad($counter, 4, '0', STR_PAD_LEFT);
            $exists = self::withTrashed()->where('request_number', $number)->exists();
            if (!$exists) {
                return $number;
            }
            $counter++;
        } while ($counter < 10000);
        
        throw new \Exception('Unable to generate unique request number');
    }
}
