<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Check if we should hide out of stock products
        $showOutOfStock = \App\Models\Setting::shouldShowOutOfStockProducts();

        // Ensure total_stock is included and filter out-of-stock products if setting is disabled
        $products->getCollection()->transform(function ($product) {
            $productArray = $product->toArray();
            $productArray['total_stock'] = $product->total_stock;
            return $productArray;
        });

        // Filter out products with zero stock if setting is disabled
        if (!$showOutOfStock) {
            $products->setCollection(
                $products->getCollection()->filter(function ($product) {
                    return ($product['total_stock'] ?? 0) > 0;
                })
            );
        }

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function show($id, Request $request)
    {
        $product = Product::with(['category', 'batches' => function ($query) {
            $query->where('is_expired', false)
                  ->where('available_quantity', '>', 0)
                  ->orderBy('expiry_date', 'asc');
        }])
        ->where('is_active', true)
        ->findOrFail($id);

        // Check if we should hide out of stock products
        $showOutOfStock = \App\Models\Setting::shouldShowOutOfStockProducts();
        
        // If setting is disabled and product is out of stock, return 404
        if (!$showOutOfStock && $product->total_stock <= 0) {
            abort(404, 'Product not found');
        }

        // Ensure total_stock is included in the response
        $productData = $product->toArray();
        $productData['total_stock'] = $product->total_stock;

        // Log product view activity (only if user is authenticated)
        if (Auth::check()) {
            ActivityLogService::logAction(
                'view_product',
                "Viewed product: {$product->name}",
                $product,
                [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'category_id' => $product->category_id,
                    'source' => 'mobile_app',
                ],
                $request
            );
        }

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
