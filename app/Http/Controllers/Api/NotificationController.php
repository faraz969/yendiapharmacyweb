<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all active notifications
     */
    public function index()
    {
        $notifications = Notification::active()->get();
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Get a specific notification
     */
    public function show($id)
    {
        $notification = Notification::active()->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $notification,
        ]);
    }
}
