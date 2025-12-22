<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
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
}

