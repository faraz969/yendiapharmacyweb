<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::latest()->paginate(20);
        return view('admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('admin.vendors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('vendors', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        Vendor::create($validated);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    public function show(Vendor $vendor)
    {
        $vendor->load('purchaseOrders');
        return view('admin.vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($vendor->logo && Storage::disk('public')->exists($vendor->logo)) {
                Storage::disk('public')->delete($vendor->logo);
            }
            $validated['logo'] = $request->file('logo')->store('vendors', 'public');
        }

        // Handle remove logo option
        if ($request->has('remove_logo')) {
            if ($vendor->logo && Storage::disk('public')->exists($vendor->logo)) {
                Storage::disk('public')->delete($vendor->logo);
            }
            $validated['logo'] = null;
        }

        $validated['is_active'] = $request->has('is_active');
        $vendor->update($validated);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        if ($vendor->purchaseOrders()->count() > 0) {
            return redirect()->route('admin.vendors.index')
                ->with('error', 'Cannot delete vendor with existing purchase orders.');
        }

        // Delete logo if exists
        if ($vendor->logo && Storage::disk('public')->exists($vendor->logo)) {
            Storage::disk('public')->delete($vendor->logo);
        }

        $vendor->delete();

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
