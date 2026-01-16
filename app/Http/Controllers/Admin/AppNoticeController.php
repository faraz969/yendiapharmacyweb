<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppNoticeController extends Controller
{
    public function index()
    {
        $notices = AppNotice::orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.app-notices.index', compact('notices'));
    }

    public function create()
    {
        return view('admin.app-notices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'priority' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('app_notices', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['priority'] = $request->input('priority', 0);

        AppNotice::create($validated);

        return redirect()->route('admin.app-notices.index')
            ->with('success', 'App notice created successfully.');
    }

    public function show(AppNotice $appNotice)
    {
        // Redirect to edit page
        return redirect()->route('admin.app-notices.edit', $appNotice);
    }

    public function edit(AppNotice $appNotice)
    {
        return view('admin.app-notices.edit', compact('appNotice'));
    }

    public function update(Request $request, AppNotice $appNotice)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'priority' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        if ($request->hasFile('image')) {
            if ($appNotice->image) {
                Storage::disk('public')->delete($appNotice->image);
            }
            $validated['image'] = $request->file('image')->store('app_notices', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['priority'] = $request->input('priority', 0);

        $appNotice->update($validated);

        return redirect()->route('admin.app-notices.index')
            ->with('success', 'App notice updated successfully.');
    }

    public function destroy(AppNotice $appNotice)
    {
        if ($appNotice->image) {
            Storage::disk('public')->delete($appNotice->image);
        }

        $appNotice->delete();

        return redirect()->route('admin.app-notices.index')
            ->with('success', 'App notice deleted successfully.');
    }
}
