<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('is_expired', true);
            }
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimes:mp4,avi,mov|max:10240',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'purchase_unit' => 'required|string|in:box,pack,bottle,piece',
            'selling_unit' => 'required|string|in:tablet,capsule,ml,piece',
            'conversion_factor' => 'required|integer|min:1',
            'prescription_notes' => 'nullable|string',
            'min_stock_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
        ]);

        // Handle images
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        // Handle video
        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }

        $validated['requires_prescription'] = $request->has('requires_prescription');
        $validated['track_expiry'] = $request->has('track_expiry');
        $validated['track_batch'] = $request->has('track_batch');
        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'batches']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimes:mp4,avi,mov|max:10240',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'purchase_unit' => 'required|string|in:box,pack,bottle,piece',
            'selling_unit' => 'required|string|in:tablet,capsule,ml,piece',
            'conversion_factor' => 'required|integer|min:1',
            'prescription_notes' => 'nullable|string',
            'min_stock_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
        ]);

        // Handle images
        if ($request->hasFile('images')) {
            // Delete old images
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
            $validated['images'] = $imagePaths;
        } elseif ($request->has('remove_images')) {
            // Remove specific images
            $imagesToRemove = $request->remove_images;
            $currentImages = $product->images ?? [];
            $remainingImages = array_diff($currentImages, $imagesToRemove);
            foreach ($imagesToRemove as $imageToRemove) {
                Storage::disk('public')->delete($imageToRemove);
            }
            $validated['images'] = array_values($remainingImages);
        }

        // Handle video
        if ($request->hasFile('video')) {
            if ($product->video) {
                Storage::disk('public')->delete($product->video);
            }
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }

        $validated['requires_prescription'] = $request->has('requires_prescription');
        $validated['track_expiry'] = $request->has('track_expiry');
        $validated['track_batch'] = $request->has('track_batch');
        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // Delete video
        if ($product->video) {
            Storage::disk('public')->delete($product->video);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
