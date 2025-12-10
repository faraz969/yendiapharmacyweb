<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->deliveryAddresses()->latest()->get();
        return view('web.user.addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('web.user.addresses.create');
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

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address added successfully!');
    }

    public function edit(DeliveryAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        return view('web.user.addresses.edit', compact('address'));
    }

    public function update(Request $request, DeliveryAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

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

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address updated successfully!');
    }

    public function destroy(DeliveryAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('user.addresses.index')
            ->with('success', 'Address deleted successfully!');
    }

    public function setDefault(DeliveryAddress $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->setAsDefault();

        return redirect()->route('user.addresses.index')
            ->with('success', 'Default address updated!');
    }
}
