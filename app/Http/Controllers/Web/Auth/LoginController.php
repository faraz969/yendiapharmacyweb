<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('web.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check if user is trying to access admin panel (has roles or is branch staff)
            if ($user->hasAnyRole(['admin', 'manager', 'staff', 'pharmacist', 'delivery_person']) || $user->isBranchStaff()) {
                // Branch staff should use admin login, but if they login from web, redirect to admin
                return redirect()->intended(route('admin.dashboard'));
            }
            
            return redirect()->intended(route('user.dashboard'))
                ->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}
