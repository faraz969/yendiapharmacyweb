<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        // Get selected navbar categories
        $navbarCategoryIds = [];
        if ($settings->has('navbar_categories') && $settings['navbar_categories']->value) {
            $navbarCategoryIds = json_decode($settings['navbar_categories']->value, true) ?? [];
        }

        return view('admin.settings.index', compact('settings', 'categories', 'navbarCategoryIds'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,ico|max:512',
            'header_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'copyright_year' => 'required|string|max:10',
            'navbar_categories' => 'nullable|array',
            'navbar_categories.*' => 'exists:categories,id',
            'app_store_url' => 'nullable|url|max:500',
            'play_store_url' => 'nullable|url|max:500',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'topbar_tagline' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'show_out_of_stock_products' => 'nullable|boolean',
        ]);

        // Handle favicon
        if ($request->hasFile('favicon')) {
            // Delete old favicon if exists
            $oldFavicon = Setting::get('favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('settings', 'public');
            Setting::set('favicon', $validated['favicon']);
        }

        // Handle remove favicon option
        if ($request->has('remove_favicon')) {
            $oldFavicon = Setting::get('favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            Setting::set('favicon', null);
        }

        // Handle header logo
        if ($request->hasFile('header_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('header_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $validated['header_logo'] = $request->file('header_logo')->store('settings', 'public');
            Setting::set('header_logo', $validated['header_logo']);
        }

        // Handle footer logo
        if ($request->hasFile('footer_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('footer_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $validated['footer_logo'] = $request->file('footer_logo')->store('settings', 'public');
            Setting::set('footer_logo', $validated['footer_logo']);
        }

        // Handle remove logo options
        if ($request->has('remove_header_logo')) {
            $oldLogo = Setting::get('header_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('header_logo', null);
        }

        if ($request->has('remove_footer_logo')) {
            $oldLogo = Setting::get('footer_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            Setting::set('footer_logo', null);
        }

        // Update copyright year
        Setting::set('copyright_year', $validated['copyright_year']);

        // Update navbar categories
        $navbarCategories = $validated['navbar_categories'] ?? [];
        Setting::set('navbar_categories', json_encode($navbarCategories));

        // Update app store URLs
        Setting::set('app_store_url', $validated['app_store_url'] ?? null);
        Setting::set('play_store_url', $validated['play_store_url'] ?? null);

        // Update contact information
        Setting::set('contact_phone', $validated['contact_phone'] ?? null);
        Setting::set('contact_email', $validated['contact_email'] ?? null);
        Setting::set('topbar_tagline', $validated['topbar_tagline'] ?? null);

        // Update currency
        Setting::set('currency', $validated['currency']);
        Setting::set('currency_symbol', $validated['currency_symbol']);

        // Update show out of stock products setting
        Setting::set('show_out_of_stock_products', $request->has('show_out_of_stock_products') ? '1' : '0');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
