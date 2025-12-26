<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketingBannerController extends Controller
{
    public function index()
    {
        $banners = MarketingBanner::orderBy('order')->paginate(20);
        return view('admin.marketing-banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.marketing-banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'background_color' => 'nullable|string|max:20',
            'button_text' => 'nullable|string|max:50',
            'link' => 'nullable|url|max:500',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('marketing-banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['order'] = $request->input('order', 0);
        $validated['background_color'] = $request->input('background_color', '#f5f5f5');
        $validated['button_text'] = $request->input('button_text', 'Shop Now');

        MarketingBanner::create($validated);

        return redirect()->route('admin.marketing-banners.index')
            ->with('success', 'Marketing banner created successfully.');
    }

    public function show(MarketingBanner $marketingBanner)
    {
        return view('admin.marketing-banners.show', compact('marketingBanner'));
    }

    public function edit(MarketingBanner $marketingBanner)
    {
        return view('admin.marketing-banners.edit', compact('marketingBanner'));
    }

    public function update(Request $request, MarketingBanner $marketingBanner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'background_color' => 'nullable|string|max:20',
            'button_text' => 'nullable|string|max:50',
            'link' => 'nullable|url|max:500',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($marketingBanner->image) {
                Storage::disk('public')->delete($marketingBanner->image);
            }
            $validated['image'] = $request->file('image')->store('marketing-banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['order'] = $request->input('order', 0);
        $validated['background_color'] = $request->input('background_color', '#f5f5f5');
        $validated['button_text'] = $request->input('button_text', 'Shop Now');

        $marketingBanner->update($validated);

        return redirect()->route('admin.marketing-banners.index')
            ->with('success', 'Marketing banner updated successfully.');
    }

    public function destroy(MarketingBanner $marketingBanner)
    {
        if ($marketingBanner->image) {
            Storage::disk('public')->delete($marketingBanner->image);
        }

        $marketingBanner->delete();

        return redirect()->route('admin.marketing-banners.index')
            ->with('success', 'Marketing banner deleted successfully.');
    }
}
