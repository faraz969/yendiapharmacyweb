<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use App\Models\MarketingBanner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get active banners - shows banners that are active and within their date range
        $banners = Banner::where('is_active', true)
            ->where(function ($q) {
                // Show if no start_date OR start_date is today or in the past
                $q->whereNull('start_date')
                    ->orWhereDate('start_date', '<=', now()->toDateString());
            })
            ->where(function ($q) {
                // Show if no end_date OR end_date is today or in the future
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', now()->toDateString());
            })
            ->orderBy('order', 'asc')
            ->get();
        
        $featuredCategories = Category::where('is_active', true)
            ->withCount(['products' => function($query) {
                $query->where('is_active', true)->where('is_expired', false);
            }])
            ->orderBy('sort_order')
            ->take(10)
            ->get();

        $showOutOfStock = \App\Models\Setting::shouldShowOutOfStockProducts();

        $featuredCategoryIds = $featuredCategories->pluck('id')->filter()->values()->all();

        // Mix products across featured categories so each filter tab can show matching items
        $perCategoryLimit = 3;
        $maxTotal = 24;

        if (!empty($featuredCategoryIds)) {
            $candidates = Product::where('is_active', true)
                ->where('is_expired', false)
                ->whereIn('category_id', $featuredCategoryIds)
                ->with('category')
                ->latest()
                ->limit(200)
                ->get();

            if (!$showOutOfStock) {
                $candidates = $candidates->filter(function ($product) {
                    return $product->total_stock > 0;
                })->values();
            }

            $byCategory = $candidates->groupBy('category_id');
            $featuredProducts = collect();

            foreach ($featuredCategoryIds as $catId) {
                $slice = ($byCategory[$catId] ?? collect())->take($perCategoryLimit);
                $featuredProducts = $featuredProducts->merge($slice);
                if ($featuredProducts->count() >= $maxTotal) {
                    break;
                }
            }

            $featuredProducts = $featuredProducts->unique('id')->take($maxTotal)->values();
        } else {
            $featuredProducts = collect();
        }

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::where('is_active', true)
                ->where('is_expired', false)
                ->with('category')
                ->latest()
                ->limit(50)
                ->get();

            if (!$showOutOfStock) {
                $featuredProducts = $featuredProducts->filter(function ($product) {
                    return $product->total_stock > 0;
                })->values();
            }

            $featuredProducts = $featuredProducts->take(8)->values();
        }

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Get marketing banners (limit to 3 for perfect 3-per-row layout)
        $marketingBanners = MarketingBanner::where('is_active', true)
            ->orderBy('order', 'asc')
            ->take(3)
            ->get();

        return view('web.home', compact('banners', 'featuredCategories', 'featuredProducts', 'categories', 'marketingBanners'));
    }
}
