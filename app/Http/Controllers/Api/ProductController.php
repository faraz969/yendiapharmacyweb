<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('is_active', true)
            ->where('is_expired', false);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate($request->get('per_page', 15));

        // Ensure total_stock is included for each product
        $products->getCollection()->transform(function ($product) {
            $productArray = $product->toArray();
            $productArray['total_stock'] = $product->total_stock;
            return $productArray;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'batches' => function ($query) {
            $query->where('is_expired', false)
                  ->where('available_quantity', '>', 0)
                  ->orderBy('expiry_date', 'asc');
        }])
        ->where('is_active', true)
        ->findOrFail($id);

        // Ensure total_stock is included in the response
        $productData = $product->toArray();
        $productData['total_stock'] = $product->total_stock;

        return response()->json([
            'success' => true,
            'data' => $productData,
        ]);
    }

    public function categories()
    {
        $categories = Category::where('is_active', true)
            ->withCount('activeProducts')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
