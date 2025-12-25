<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get currency settings
     */
    public function currency()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'currency' => Setting::getCurrency(),
                'currency_symbol' => Setting::getCurrencySymbol(),
            ],
        ]);
    }
}
