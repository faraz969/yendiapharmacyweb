<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return view('web.user.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        
        $notification = Notification::where('user_id', $user->id)
            ->findOrFail($id);
        
        $notification->update(['is_read' => true]);
        
        return back()->with('success', 'Notification marked as read');
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return back()->with('success', 'All notifications marked as read');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        
        $notification = Notification::where('user_id', $user->id)
            ->findOrFail($id);
        
        $notification->delete();
        
        return back()->with('success', 'Notification deleted');
    }

    public function clearAll()
    {
        $user = Auth::user();
        
        Notification::where('user_id', $user->id)->delete();
        
        return back()->with('success', 'All notifications cleared');
    }
}

