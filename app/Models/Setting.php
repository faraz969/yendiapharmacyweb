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

    /**
     * Get contact phone
     */
    public static function getContactPhone()
    {
        return self::get('contact_phone', '+1 800 900');
    }

    /**
     * Get contact email
     */
    public static function getContactEmail()
    {
        return self::get('contact_email', 'info@pharmacystore.com');
    }

    /**
     * Get topbar tagline
     */
    public static function getTopbarTagline()
    {
        return self::get('topbar_tagline', 'Super Value Deals - Save more with coupons');
    }

    /**
     * Get currency code
     */
    public static function getCurrency()
    {
        return self::get('currency', 'USD');
    }

    /**
     * Get currency symbol
     */
    public static function getCurrencySymbol()
    {
        return self::get('currency_symbol', '$');
    }

    /**
     * Format price with currency symbol
     */
    public static function formatPrice($amount, $decimals = 2)
    {
        $symbol = self::getCurrencySymbol();
        return $symbol . number_format($amount, $decimals);
    }
}
