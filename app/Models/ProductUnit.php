<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeForPurchase($query)
    {
        return $query->whereIn('type', ['purchase', 'both']);
    }

    public function scopeForSelling($query)
    {
        return $query->whereIn('type', ['selling', 'both']);
    }

    public static function purchaseOptions()
    {
        return static::active()->forPurchase()->ordered()->get();
    }

    public static function sellingOptions()
    {
        return static::active()->forSelling()->ordered()->get();
    }

    public static function purchaseValues(): array
    {
        return static::purchaseOptions()->pluck('value')->all();
    }

    public static function sellingValues(): array
    {
        return static::sellingOptions()->pluck('value')->all();
    }

    public static function makeValueFromName(string $name): string
    {
        $value = Str::slug(trim($name), '_');
        return $value !== '' ? $value : 'unit';
    }
}
