<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    /**
     * Get all delivery zones
     */
    public function index()
    {
        $zones = DeliveryZone::all();
        
        return response()->json([
            'success' => true,
            'data' => $zones,
        ]);
    }
}
