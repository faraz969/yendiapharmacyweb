<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->deliveryAddresses()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean',
        ]);

        $user = Auth::user();

        // If this is set as default, unset other defaults
        if ($request->has('is_default') && $request->is_default) {
            DeliveryAddress::where('user_id', $user->id)
                ->update(['is_default' => false]);
        }

        $address = DeliveryAddress::create([
            'user_id' => $user->id,
            'label' => $validated['label'] ?? null,
            'contact_name' => $validated['contact_name'],
            'contact_phone' => $validated['contact_phone'],
            'contact_email' => $validated['contact_email'] ?? null,
            'address' => $validated['address'],
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'country' => $validated['country'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'is_default' => $request->has('is_default') && $request->is_default,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'data' => $address,
        ], 201);
    }

    public function show($id)
    {
        $address = DeliveryAddress::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $address,
        ]);
    }

    public function update(Request $request, $id)
    {
        $address = DeliveryAddress::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'label' => 'nullable|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->has('is_default') && $request->is_default) {
            DeliveryAddress::where('user_id', Auth::id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $address,
        ]);
    }

    public function destroy($id)
    {
        $address = DeliveryAddress::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
        ]);
    }

    public function setDefault($id)
    {
        $address = DeliveryAddress::where('user_id', Auth::id())->findOrFail($id);
        $address->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Default address updated',
            'data' => $address,
        ]);
    }
}
