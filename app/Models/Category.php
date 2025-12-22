<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active',
        'sort_order',
        'margin_type',
        'margin_value',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'margin_value' => 'decimal:2',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true);
    }

    /**
     * Calculate selling price based on cost price and category margin
     * 
     * @param float $costPrice
     * @return float|null Returns calculated selling price or null if margin is not set
     */
    public function calculateSellingPrice(float $costPrice): ?float
    {
        if (!$this->margin_type || !$this->margin_value) {
            return null;
        }

        if ($this->margin_type === 'fixed') {
            // Fixed amount: cost_price + margin_value
            return $costPrice + $this->margin_value;
        } else {
            // Percentage: cost_price + (cost_price * margin_value / 100)
            return $costPrice + ($costPrice * $this->margin_value / 100);
        }
    }

    /**
     * Get margin display text
     */
    public function getMarginDisplayAttribute(): ?string
    {
        if (!$this->margin_type || !$this->margin_value) {
            return null;
        }

        if ($this->margin_type === 'fixed') {
            return '$' . number_format($this->margin_value, 2);
        } else {
            return number_format($this->margin_value, 2) . '%';
        }
    }
}
