<?php

namespace App\Http\Controllers\Web;

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
        $query = Product::where('is_active', true)
            ->where('is_expired', false)
            ->with('category');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by category (support both category_id and category for compatibility)
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        } elseif ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by prescription requirement
        if ($request->has('prescription')) {
            if ($request->prescription === 'required') {
                $query->where('requires_prescription', true);
            } elseif ($request->prescription === 'not_required') {
                $query->where('requires_prescription', false);
            }
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->get();
        
        // Check if we should hide out of stock products
        $showOutOfStock = \App\Models\Setting::shouldShowOutOfStockProducts();
        
        // Filter out products with zero stock if setting is disabled
        if (!$showOutOfStock) {
            $products = $products->filter(function ($product) {
                return $product->total_stock > 0;
            });
        }
        
        // Paginate manually after filtering
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 12;
        $currentItems = $products->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $products->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('web.products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::where('is_active', true)
            ->where('is_expired', false)
            ->with(['category', 'batches' => function($query) {
                $query->where('is_expired', false)
                      ->where('available_quantity', '>', 0)
                      ->orderBy('expiry_date', 'asc');
            }])
            ->findOrFail($id);

        // Check if we should hide out of stock products
        $showOutOfStock = \App\Models\Setting::shouldShowOutOfStockProducts();
        
        // If setting is disabled and product is out of stock, return 404
        if (!$showOutOfStock && $product->total_stock <= 0) {
            abort(404, 'Product not found');
        }

        // Related products
        $relatedProducts = Product::where('is_active', true)
            ->where('is_expired', false)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->get();
        
        // Filter out-of-stock related products if setting is disabled
        if (!$showOutOfStock) {
            $relatedProducts = $relatedProducts->filter(function ($relatedProduct) {
                return $relatedProduct->total_stock > 0;
            });
        }
        
        $relatedProducts = $relatedProducts->take(4);

        // Log product view activity
        ActivityLogService::logAction(
            'view_product',
            "Viewed product: {$product->name}",
            $product,
            [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category_id' => $product->category_id,
                'source' => 'web',
            ],
            request()
        );

        return view('web.products.show', compact('product', 'relatedProducts'));
    }

    public function category($id)
    {
        $category = Category::where('is_active', true)->findOrFail($id);
        
        $products = Product::where('is_active', true)
            ->where('is_expired', false)
            ->where('category_id', $id)
            ->with('category')
            ->latest()
            ->get();
        
        // Check if we should hide out of stock products
        $showOutOfStock = \App\Models\Setting::shouldShowOutOfStockProducts();
        
        // Filter out products with zero stock if setting is disabled
        if (!$showOutOfStock) {
            $products = $products->filter(function ($product) {
                return $product->total_stock > 0;
            });
        }
        
        // Paginate manually after filtering
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 12;
        $currentItems = $products->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $products->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('web.products.category', compact('category', 'products', 'categories'));
    }
}
