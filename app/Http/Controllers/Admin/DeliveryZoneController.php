<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::latest()->paginate(20);
        return view('admin.delivery-zones.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.delivery-zones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'delivery_fee' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'estimated_delivery_hours' => 'nullable|integer|min:1',
        ]);

        $validated['is_active'] = $request->has('is_active');
        DeliveryZone::create($validated);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Delivery zone created successfully.');
    }

    public function show(DeliveryZone $deliveryZone)
    {
        $deliveryZone->load('orders');
        return view('admin.delivery-zones.show', compact('deliveryZone'));
    }

    public function edit(DeliveryZone $deliveryZone)
    {
        return view('admin.delivery-zones.edit', compact('deliveryZone'));
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'delivery_fee' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'estimated_delivery_hours' => 'nullable|integer|min:1',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $deliveryZone->update($validated);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Delivery zone updated successfully.');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        if ($deliveryZone->orders()->count() > 0) {
            return redirect()->route('admin.delivery-zones.index')
                ->with('error', 'Cannot delete delivery zone with existing orders.');
        }

        $deliveryZone->delete();

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'Delivery zone deleted successfully.');
    }
}
