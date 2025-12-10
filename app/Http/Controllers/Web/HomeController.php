<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCategories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('is_expired', false)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('web.home', compact('featuredCategories', 'featuredProducts', 'categories'));
    }
}
