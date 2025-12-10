<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "=== Authentication Test ===\n\n";

// Check if user exists
$user = User::where('email', 'admin@pharmacy.com')->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found: {$user->email}\n";
echo "   Name: {$user->name}\n";
echo "   Roles: " . $user->roles->pluck('name')->implode(', ') . "\n\n";

// Check password
$passwordCheck = Hash::check('password', $user->password);
echo $passwordCheck ? "✅ Password hash is correct\n" : "❌ Password hash mismatch\n";
echo "\n";

// Test authentication
$attempt = Auth::guard('web')->attempt([
    'email' => 'admin@pharmacy.com',
    'password' => 'password'
]);

echo $attempt ? "✅ Authentication successful with 'web' guard\n" : "❌ Authentication failed with 'web' guard\n";
echo "\n";

// Check Filament config
$filamentGuard = config('filament.auth.guard');
echo "Filament guard: {$filamentGuard}\n";
echo $filamentGuard === 'web' ? "✅ Filament is using 'web' guard\n" : "⚠️  Filament is using '{$filamentGuard}' guard\n";
echo "\n";

echo "=== Test Complete ===\n";

