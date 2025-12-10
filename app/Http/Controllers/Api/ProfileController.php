<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                ],
                'profile' => $profile ? [
                    'phone' => $profile->phone,
                    'date_of_birth' => $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : null,
                    'gender' => $profile->gender,
                    'address' => $profile->address,
                    'city' => $profile->city,
                    'state' => $profile->state,
                    'postal_code' => $profile->postal_code,
                    'country' => $profile->country,
                ] : null,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update or create profile
        $profile = $user->profile;
        if (!$profile) {
            $profile = new UserProfile(['user_id' => $user->id]);
        }

        $profile->fill([
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'country' => $validated['country'] ?? null,
        ]);
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? null,
                ],
                'profile' => [
                    'phone' => $profile->phone,
                    'date_of_birth' => $profile->date_of_birth ? $profile->date_of_birth->format('Y-m-d') : null,
                    'gender' => $profile->gender,
                    'address' => $profile->address,
                    'city' => $profile->city,
                    'state' => $profile->state,
                    'postal_code' => $profile->postal_code,
                    'country' => $profile->country,
                ],
            ],
        ]);
    }
}
