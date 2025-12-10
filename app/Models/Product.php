<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'sku',
        'barcode',
        'images',
        'video',
        'selling_price',
        'cost_price',
        'purchase_unit',
        'selling_unit',
        'conversion_factor',
        'requires_prescription',
        'prescription_notes',
        'min_stock_level',
        'max_stock_level',
        'track_expiry',
        'track_batch',
        'is_active',
        'is_expired',
    ];

    protected $casts = [
        'images' => 'array',
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'conversion_factor' => 'integer',
        'requires_prescription' => 'boolean',
        'track_expiry' => 'boolean',
        'track_batch' => 'boolean',
        'is_active' => 'boolean',
        'is_expired' => 'boolean',
    ];

    // Accessor to ensure images is always an array
    public function getImagesAttribute($value)
    {
        // If already cast to array, return it
        if (is_array($value)) {
            return $value;
        }
        // If null or empty, return empty array
        if (is_null($value) || $value === '') {
            return [];
        }
        // If string, try to decode it
        if (is_string($value)) {
            // Remove any extra quotes that might be around the JSON
            $value = trim($value, '"');
            
            // Try to decode as JSON
            $decoded = json_decode($value, true);
            
            // If decoded successfully and is array, return it
            if (is_array($decoded)) {
                return $decoded;
            }
            
            // If it's a single string path (not JSON array), wrap it in array
            if (is_string($decoded) && !empty($decoded)) {
                return [$decoded];
            }
            
            // If decode failed but value is not empty, it might be a single path string
            if (!empty($value) && !json_decode($value)) {
                // Check if it looks like a file path
                if (strpos($value, '/') !== false || strpos($value, '\\') !== false) {
                    return [$value];
                }
            }
        }
        return [];
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Business Logic Methods
    public function getTotalStockAttribute()
    {
        if (!$this->track_batch) {
            return 0; // For non-batch tracked items, implement separate stock tracking
        }

        return $this->batches()
            ->where('is_expired', false)
            ->sum('available_quantity') * $this->conversion_factor;
    }

    public function getTotalStockInSellingUnitAttribute()
    {
        return $this->total_stock;
    }

    public function convertToSellingUnit($purchaseQuantity)
    {
        return $purchaseQuantity * $this->conversion_factor;
    }

    public function convertToPurchaseUnit($sellingQuantity)
    {
        return ceil($sellingQuantity / $this->conversion_factor);
    }

    public function isInStock($quantity)
    {
        return $this->total_stock >= $quantity;
    }

    public function getAvailableBatches()
    {
        return $this->batches()
            ->where('is_expired', false)
            ->where('available_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    public function checkAndUpdateExpiryStatus()
    {
        $expiredBatches = $this->batches()
            ->where('expiry_date', '<', now())
            ->where('is_expired', false)
            ->get();

        foreach ($expiredBatches as $batch) {
            $batch->update([
                'is_expired' => true,
                'expired_at' => now(),
            ]);
        }

        $this->update([
            'is_expired' => $this->batches()->where('is_expired', false)->count() === 0
        ]);
    }
}
