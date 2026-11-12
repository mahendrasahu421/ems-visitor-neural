<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\RoleController;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest('id')->paginate(15)->withQueryString();

        $roles = Role::orderBy('name')->get();
        $totalUsers = User::count();
        $activeUsers = User::where('status', 1)->count();
        $inactiveUsers = $totalUsers - $activeUsers;

        return view('admin-users.index', [
            'title' => 'Admin Users Management',
            'users' => $users,
            'roles' => $roles,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
        ]);
    }

    public function create()
    {
        $allPermissions = Permission::orderBy('name')->get();
        $roles = Role::with('permissions')->orderBy('name')->get();
        $groupedPermissions = RoleController::groupPermissions($allPermissions);

        // Map role ID to its permission names
        $rolePermissionsMap = $roles->mapWithKeys(function ($role) {
            return [$role->id => $role->permissions->pluck('name')->values()];
        });

        $selectedRoleId = old('role_id', null);
        $selectedPermissions = old('permissions', []);
        if (empty($selectedPermissions) && $selectedRoleId) {
            $role = Role::with('permissions')->find($selectedRoleId);
            if ($role) {
                $selectedPermissions = $role->permissions->pluck('name')->all();
            }
        }

        return view('admin-users.form', [
            'title' => 'Create New User',
            'user' => new User(),
            'roles' => $roles,
            'permissions' => $allPermissions,
            'groupedPermissions' => $groupedPermissions,
            'rolePermissionsMap' => $rolePermissionsMap,
            'selectedRoleId' => $selectedRoleId,
            'selectedPermissions' => $selectedPermissions,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:0,1',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ], [
            'name.required' => 'Please enter the user full name.',
            'email.required' => 'A valid email address is required.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Please set a secure password for this user.',
            'password.min' => 'Password must be at least 6 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role_id.required' => 'Please select a role to assign to this user.',
        ]);

        $role = Role::findOrFail($data['role_id']);

        $user = User::create([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => $data['phone'] ? trim($data['phone']) : null,
            'password' => Hash::make($data['password']),
            'role_id' => $role->id,
            'status' => (int) $data['status'],
        ]);

        $user->assignRole($role->name);
        $user->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.users.index')->with('success', "User '{$user->name}' created successfully with assigned role '{$role->name}'.");
    }

    public function edit(User $user)
    {
        $allPermissions = Permission::orderBy('name')->get();
        $roles = Role::with('permissions')->orderBy('name')->get();
        $groupedPermissions = RoleController::groupPermissions($allPermissions);

        $rolePermissionsMap = $roles->mapWithKeys(function ($r) {
            return [$r->id => $r->permissions->pluck('name')->values()];
        });

        $userRole = $user->roles->first() ?? ($user->role_id ? Role::find($user->role_id) : null);
        $userRolePerms = $userRole ? $userRole->permissions->pluck('name') : collect();
        $directPerms = $user->permissions->pluck('name');
        $allUserPerms = $userRolePerms->merge($directPerms)->unique()->values()->all();

        return view('admin-users.form', [
            'title' => 'Edit User Account',
            'user' => $user->load(['roles', 'permissions']),
            'roles' => $roles,
            'permissions' => $allPermissions,
            'groupedPermissions' => $groupedPermissions,
            'rolePermissionsMap' => $rolePermissionsMap,
            'selectedRoleId' => $user->role_id ?: $user->roles->first()?->id,
            'selectedPermissions' => old('permissions', $allUserPerms),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:0,1',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ], [
            'name.required' => 'Please enter the user full name.',
            'email.required' => 'A valid email address is required.',
            'email.unique' => 'Another user already uses this email address.',
            'password.min' => 'Password must be at least 6 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role_id.required' => 'Please select a role to assign to this user.',
        ]);

        $role = Role::findOrFail($data['role_id']);

        $payload = [
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => $data['phone'] ? trim($data['phone']) : null,
            'role_id' => $role->id,
            'status' => (int) $data['status'],
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);
        $user->syncRoles([$role->name]);
        $user->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.users.index')->with('success', "User '{$user->name}' updated successfully.");
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own active account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "User '{$userName}' deleted successfully.");
    }
}
