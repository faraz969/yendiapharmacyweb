<?php

namespace App\Helpers;

use App\Models\Setting;

class CurrencyHelper
{
    /**
     * Format price with currency symbol
     */
    public static function format($amount, $decimals = 2)
    {
        $symbol = Setting::getCurrencySymbol();
        return $symbol . number_format($amount, $decimals);
    }

    /**
     * Get currency symbol
     */
    public static function symbol()
    {
        return Setting::getCurrencySymbol();
    }

    /**
     * Get currency code
     */
    public static function code()
    {
        return Setting::getCurrency();
    }
}

