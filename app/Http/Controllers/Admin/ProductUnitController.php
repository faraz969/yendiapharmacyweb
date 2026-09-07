<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Http\Request;

class ProductUnitController extends Controller
{
    public function index()
    {
        $units = ProductUnit::ordered()->paginate(30);
        return view('admin.product-units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.product-units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:purchase,selling,both',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Keep name & value the same for admins — value is just a stored key derived from name
        $name = trim($validated['name']);
        $value = ProductUnit::makeValueFromName($name);

        $baseValue = $value;
        $counter = 1;
        while (ProductUnit::where('value', $value)->exists()) {
            $value = $baseValue . '_' . $counter;
            $counter++;
        }

        ProductUnit::create([
            'name' => $name,
            'value' => $value,
            'type' => $validated['type'],
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.product-units.index')
            ->with('success', 'Unit created successfully.');
    }

    public function edit(ProductUnit $productUnit)
    {
        return view('admin.product-units.edit', compact('productUnit'));
    }

    public function update(Request $request, ProductUnit $productUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:purchase,selling,both',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $name = trim($validated['name']);
        $oldValue = $productUnit->value;
        $newValue = ProductUnit::makeValueFromName($name);

        // Keep unique value if name maps to a slug already used by another unit
        if ($newValue !== $oldValue) {
            $baseValue = $newValue;
            $counter = 1;
            while (ProductUnit::where('value', $newValue)->where('id', '!=', $productUnit->id)->exists()) {
                $newValue = $baseValue . '_' . $counter;
                $counter++;
            }
        }

        $productUnit->update([
            'name' => $name,
            'value' => $newValue,
            'type' => $validated['type'],
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->has('is_active'),
        ]);

        if ($oldValue !== $newValue) {
            Product::where('purchase_unit', $oldValue)->update(['purchase_unit' => $newValue]);
            Product::where('selling_unit', $oldValue)->update(['selling_unit' => $newValue]);
        }

        return redirect()->route('admin.product-units.index')
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(ProductUnit $productUnit)
    {
        $inUse = Product::where('purchase_unit', $productUnit->value)
            ->orWhere('selling_unit', $productUnit->value)
            ->exists();

        if ($inUse) {
            return redirect()->route('admin.product-units.index')
                ->with('error', 'Cannot delete unit that is used by existing products. Deactivate it instead.');
        }

        $productUnit->delete();

        return redirect()->route('admin.product-units.index')
            ->with('success', 'Unit deleted successfully.');
    }
}
