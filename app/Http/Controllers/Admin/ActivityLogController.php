<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // Only allow full admins/managers/staff to view activity logs
        if (!Auth::user()->hasAnyRole(['admin', 'manager', 'staff'])) {
            abort(403, 'You do not have permission to view activity logs.');
        }

        $query = ActivityLog::with('user')->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(50);

        // Get unique actions for filter
        $actions = ActivityLog::distinct()->pluck('action')->sort();

        // Get users for filter
        $users = \App\Models\User::whereHas('activityLogs')->orderBy('name')->get();

        return view('admin.activity-logs.index', compact('logs', 'actions', 'users'));
    }

    public function show(ActivityLog $activityLog)
    {
        // Only allow full admins/managers/staff to view activity log details
        if (!Auth::user()->hasAnyRole(['admin', 'manager', 'staff'])) {
            abort(403, 'You do not have permission to view activity log details.');
        }

        $activityLog->load('user');

        return view('admin.activity-logs.show', compact('activityLog'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        // Only allow full admins/managers/staff to delete activity logs
        if (!Auth::user()->hasAnyRole(['admin', 'manager', 'staff'])) {
            abort(403, 'You do not have permission to delete activity logs.');
        }

        $activityLog->delete();

        return redirect()->route('admin.activity-logs.index')
            ->with('success', 'Activity log deleted successfully.');
    }
}
