<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    /**
     * Get all active delivery zones
     */
    public function index()
    {
        $zones = DeliveryZone::where('is_active', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $zones,
        ]);
    }
}
