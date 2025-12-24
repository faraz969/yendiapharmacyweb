<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get header logo URL
     */
    public static function getHeaderLogo()
    {
        $logo = self::get('header_logo');
        if ($logo) {
            return asset('storage/' . $logo);
        }
        return asset('logo.png'); // Default logo
    }

    /**
     * Get footer logo URL
     */
    public static function getFooterLogo()
    {
        $logo = self::get('footer_logo');
        if ($logo) {
            return asset('storage/' . $logo);
        }
        return asset('logo.png'); // Default logo
    }

    /**
     * Get copyright year
     */
    public static function getCopyrightYear()
    {
        return self::get('copyright_year', date('Y'));
    }

    /**
     * Get navbar categories
     */
    public static function getNavbarCategories()
    {
        $categoryIds = self::get('navbar_categories', '[]');
        $ids = json_decode($categoryIds, true);
        
        if (empty($ids) || !is_array($ids)) {
            return collect([]);
        }

        return Category::whereIn('id', $ids)
            ->where('is_active', true)
            ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
            ->get();
    }

    /**
     * Get app store URLs
     */
    public static function getAppStoreUrl()
    {
        return self::get('app_store_url');
    }

    public static function getPlayStoreUrl()
    {
        return self::get('play_store_url');
    }
}
