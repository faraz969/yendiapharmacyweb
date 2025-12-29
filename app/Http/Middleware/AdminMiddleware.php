<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        \Log::info('AdminMiddleware: Request received', [
            'path' => $request->path(),
            'method' => $request->method(),
            'route' => $request->route() ? $request->route()->getName() : 'no route',
        ]);

        if (!Auth::check()) {
            \Log::warning('AdminMiddleware: User not authenticated');
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        \Log::info('AdminMiddleware: User authenticated', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'has_roles' => $user->hasAnyRole(['admin', 'manager', 'staff']),
            'is_branch_staff' => $user->isBranchStaff(),
        ]);

        // Allow access if user has admin role OR is branch staff
        if (!$user->hasAnyRole(['admin', 'manager', 'staff']) && !$user->isBranchStaff()) {
            \Log::warning('AdminMiddleware: Access denied', [
                'user_id' => $user->id,
                'path' => $request->path(),
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access.'], 403);
            }
            abort(403, 'Unauthorized access');
        }

        \Log::info('AdminMiddleware: Access granted, passing to next middleware');
        return $next($request);
    }
}
