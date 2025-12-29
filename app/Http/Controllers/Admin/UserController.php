<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Services\ActivityLogService;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        // Only allow full admins/managers/staff to view users list
        if (!Auth::user()->hasAnyRole(['admin', 'manager', 'staff'])) {
            abort(403, 'You do not have permission to view users.');
        }

        $users = User::with(['roles', 'branch'])->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        // Only allow full admins/managers/staff to create users
        if (!Auth::user()->hasAnyRole(['admin', 'manager', 'staff'])) {
            abort(403, 'You do not have permission to create users.');
        }

        $roles = Role::all();
        $branches = Branch::active()->ordered()->get();
        return view('admin.users.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        // Only allow full admins/managers/staff to create users
        if (!Auth::user()->hasAnyRole(['admin', 'manager', 'staff'])) {
            abort(403, 'You do not have permission to create users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'branch_id' => $validated['branch_id'] ?? null,
        ]);

        if ($request->has('roles')) {
            $roleNames = Role::whereIn('id', $validated['roles'])->pluck('name');
            $user->assignRole($roleNames);
        }

        // Log the activity
        ActivityLogService::logCreate($user, ['roles' => $roleNames->toArray()], $request);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show($id)
    {
        // Redirect to edit page if someone tries to view a user
        return redirect()->route('admin.users.edit', $id);
    }

    public function edit($id)
    {
        // Log immediately to see if method is called
        Log::info('UserController@edit called - METHOD REACHED!', [
            'id' => $id,
            'auth_user_id' => Auth::id(),
            'auth_user_email' => Auth::user()->email ?? 'not logged in',
            'request_path' => request()->path(),
            'request_url' => request()->url(),
            'route_name' => request()->route()->getName() ?? 'no route name',
        ]);
        
        // Find user manually to avoid route model binding issues
        $user = User::findOrFail($id);
        $currentUser = Auth::user();
        
        // Ensure roles are loaded and refresh from database
        $currentUser->load('roles');
        
        // Log roles for debugging
        Log::info('Current user roles check', [
            'user_id' => $currentUser->id,
            'roles' => $currentUser->roles->pluck('name')->toArray(),
            'has_admin' => $currentUser->hasRole('admin'),
            'has_manager' => $currentUser->hasRole('manager'),
            'has_staff' => $currentUser->hasRole('staff'),
            'has_any' => $currentUser->hasAnyRole(['admin', 'manager', 'staff']),
        ]);
        
        // Check if user has required role - try multiple methods for compatibility
        $hasPermission = $currentUser->hasRole('admin') 
            || $currentUser->hasRole('manager') 
            || $currentUser->hasRole('staff')
            || $currentUser->hasAnyRole(['admin', 'manager', 'staff']);
        
        // Only allow full admins/managers/staff to edit users
        if (!$hasPermission) {
            $userRoles = $currentUser->roles->pluck('name')->implode(', ') ?: 'none';
            Log::warning('User edit blocked', [
                'user_id' => $currentUser->id,
                'user_email' => $currentUser->email,
                'user_roles' => $currentUser->roles->pluck('name')->toArray(),
            ]);
            abort(403, "You do not have permission to edit users. Required roles: admin, manager, or staff. Your current roles: {$userRoles}");
        }

        $roles = Role::all();
        $branches = Branch::active()->ordered()->get();
        $user->load('roles', 'branch');
        return view('admin.users.edit', compact('user', 'roles', 'branches'));
    }

    public function update(Request $request, $id)
    {
        // Find user manually
        $user = User::findOrFail($id);
        
        // Only allow full admins/managers/staff to update users
        if (!Auth::user()->hasAnyRole(['admin', 'manager', 'staff'])) {
            abort(403, 'You do not have permission to update users.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        if ($request->has('roles')) {
            $roleNames = Role::whereIn('id', $validated['roles'])->pluck('name');
            $user->syncRoles($roleNames);
        } else {
            $user->syncRoles([]);
        }

        // Log the activity
        ActivityLogService::logUpdate($user, ['roles' => $user->roles->pluck('name')->toArray()], $request);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Only allow full admins/managers/staff to delete users
        if (!Auth::user()->hasAnyRole(['admin', 'manager', 'staff'])) {
            abort(403, 'You do not have permission to delete users.');
        }

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Log the activity before deletion
        ActivityLogService::logDelete($user, ['user_name' => $user->name, 'user_email' => $user->email], request());

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
