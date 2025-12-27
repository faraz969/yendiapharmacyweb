<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Use Bootstrap 5 pagination view
        Paginator::defaultView('vendor.pagination.bootstrap-5');
        Paginator::defaultSimpleView('vendor.pagination.bootstrap-5');
        
        // Share categories with all web views
        View::composer('web.layouts.app', function ($view) {
            $categories = Category::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            
            // Get navbar categories from settings
            $navbarCategories = \App\Models\Setting::getNavbarCategories();
            
            $view->with('categories', $categories);
            $view->with('navbarCategories', $navbarCategories);
        });
    }
}

// Helper function for currency formatting (available globally)
if (!function_exists('currency')) {
    function currency($amount, $decimals = 2) {
        return \App\Models\Setting::formatPrice($amount, $decimals);
    }
}
