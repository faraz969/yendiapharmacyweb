<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all active public notifications and user-specific notifications
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get public notifications (no user_id) and user-specific notifications
        $notifications = Notification::where(function($query) use ($user) {
            $query->whereNull('user_id')
                  ->orWhere('user_id', $user->id);
        })
        ->where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->get();
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Get user-specific notifications only
     */
    public function userNotifications()
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        
        $count = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        
        $notification = Notification::where('user_id', $user->id)
            ->findOrFail($id);
        
        $notification->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Clear (delete) a notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        $notification = Notification::where('user_id', $user->id)
            ->findOrFail($id);
        
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Clear all notifications for user
     */
    public function clearAll()
    {
        $user = Auth::user();
        
        Notification::where('user_id', $user->id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared',
        ]);
    }

    /**
     * Get a specific notification
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $notification = Notification::where(function($query) use ($user) {
            $query->whereNull('user_id')
                  ->orWhere('user_id', $user->id);
        })
        ->where('is_active', true)
        ->findOrFail($id);
        
        // Mark as read if it's a user notification
        if ($notification->user_id == $user->id && !$notification->is_read) {
            $notification->update(['is_read' => true]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $notification,
        ]);
    }
}
