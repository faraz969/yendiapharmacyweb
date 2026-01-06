<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\MarketingBanner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Get all active banners
     */
    public function index()
    {
        $banners = Banner::active()->get()->map(function ($banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'description' => $banner->description,
                'image' => $banner->image_url,
                'link' => $banner->link,
                'order' => $banner->order,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $banners,
        ]);
    }

    /**
     * Get all active marketing banners
     */
    public function marketingBanners()
    {
        $marketingBanners = MarketingBanner::where('is_active', true)
            ->orderBy('order', 'asc')
            ->take(3)
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'description' => $banner->description,
                    'image' => $banner->image ? asset('storage/' . $banner->image) : null,
                    'background_color' => $banner->background_color ?? '#f5f5f5',
                    'button_text' => $banner->button_text ?? 'Shop Now',
                    'link' => $banner->link,
                    'order' => $banner->order,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $marketingBanners,
        ]);
    }
}

