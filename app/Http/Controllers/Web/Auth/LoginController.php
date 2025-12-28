<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('web.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required|string',
            'password' => 'required',
        ]);

        $emailOrPhone = $request->email_or_phone;
        $password = $request->password;

        // Determine if input is email or phone
        $isEmail = filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL);
        
        // Try to find user by email or phone
        $user = User::where($isEmail ? 'email' : 'phone', $emailOrPhone)->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();
            
            // Check if user is trying to access admin panel (has roles or is branch staff)
            if ($user->hasAnyRole(['admin', 'manager', 'staff', 'pharmacist', 'delivery_person']) || $user->isBranchStaff()) {
                // Branch staff should use admin login, but if they login from web, redirect to admin
                return redirect()->intended(route('admin.dashboard'));
            }
            
            return redirect()->intended(route('user.dashboard'))
                ->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email_or_phone' => 'The provided credentials do not match our records.',
        ])->onlyInput('email_or_phone');
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
