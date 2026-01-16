<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Get admin notifications (for dropdown/panel)
     */
    public function getAdminNotifications()
    {
        $notifications = Notification::where('for_admin', true)
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $unreadCount = Notification::where('for_admin', true)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->for_admin) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    /**
     * Mark all admin notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('for_admin', true)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Clear (delete) notification
     */
    public function clear(Notification $notification)
    {
        if ($notification->for_admin) {
            $notification->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    /**
     * Clear all read admin notifications
     */
    public function clearAllRead()
    {
        Notification::where('for_admin', true)
            ->where('is_read', true)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error,promotion',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link' => 'nullable|url|max:500',
            'priority' => 'nullable|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('notifications', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['priority'] = $request->input('priority', 0);

        Notification::create($validated);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification created successfully.');
    }

    public function show(Notification $notification)
    {
        return view('admin.notifications.show', compact('notification'));
    }

    public function edit(Notification $notification)
    {
        return view('admin.notifications.edit', compact('notification'));
    }

    public function update(Request $request, Notification $notification)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error,promotion',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link' => 'nullable|url|max:500',
            'priority' => 'nullable|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        if ($request->hasFile('image')) {
            if ($notification->image) {
                Storage::disk('public')->delete($notification->image);
            }
            $validated['image'] = $request->file('image')->store('notifications', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['priority'] = $request->input('priority', 0);

        $notification->update($validated);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification updated successfully.');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->image) {
            Storage::disk('public')->delete($notification->image);
        }

        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }
}
