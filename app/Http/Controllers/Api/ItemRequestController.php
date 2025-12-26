<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemRequest;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = ItemRequest::with(['branch', 'processedBy']);
        
        if ($user) {
            $query->where('user_id', $user->id);
        }
        
        $requests = $query->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
        ]);

        // Generate request number
        $validated['request_number'] = ItemRequest::generateRequestNumber();
        
        // Set user_id if authenticated, otherwise null for guest
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['quantity'] = $validated['quantity'] ?? 1;

        $itemRequest = ItemRequest::create($validated);
        $itemRequest->load(['branch', 'processedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Item request submitted successfully. We will notify you when the item is available.',
            'data' => $itemRequest,
        ], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $itemRequest = ItemRequest::with(['branch', 'processedBy', 'user'])
            ->where(function($query) use ($user) {
                if ($user) {
                    $query->where('user_id', $user->id);
                }
            })
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $itemRequest,
        ]);
    }
}
