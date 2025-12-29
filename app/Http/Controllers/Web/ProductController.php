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

        $products = $query->paginate(12);
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

        // Related products
        $relatedProducts = Product::where('is_active', true)
            ->where('is_expired', false)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

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
            ->paginate(12);

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('web.products.category', compact('category', 'products', 'categories'));
    }
}
