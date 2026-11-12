<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Helper to group permissions into intuitive modules/categories.
     */
    public static function groupPermissions($permissions)
    {
        return $permissions->groupBy(function ($perm) {
            $name = strtolower($perm->name);

            if (str_starts_with($name, 'dashboard')) {
                return 'Dashboard';
            }
            if (str_starts_with($name, 'user') || str_starts_with($name, 'admin.user')) {
                return 'User Management';
            }
            if (
                str_starts_with($name, 'employee') || 
                str_starts_with($name, 'admin_employee') || 
                str_starts_with($name, 'employee-report') ||
                str_starts_with($name, 'team') ||
                str_starts_with($name, 'supervisor')
            ) {
                return 'Employee Management';
            }
            if (str_starts_with($name, 'attendance')) {
                return 'Attendance Tracking';
            }
            if (str_starts_with($name, 'leave') || str_starts_with($name, 'holiday')) {
                return 'Leaves & Holidays';
            }
            if (str_starts_with($name, 'payroll') || str_starts_with($name, 'salary') || str_starts_with($name, 'tax')) {
                return 'Payroll & Finance';
            }
            if (str_starts_with($name, 'invoice') || str_starts_with($name, 'payment') || str_starts_with($name, 'expense') || str_starts_with($name, 'ledger')) {
                return 'Invoicing & Payments';
            }
            if (str_starts_with($name, 'client') || str_starts_with($name, 'ticket') || str_starts_with($name, 'admin.client')) {
                return null;
            }
            if (str_starts_with($name, 'project')) {
                return null;
            }
            if (str_starts_with($name, 'recruitment') || str_starts_with($name, 'job') || str_starts_with($name, 'onboarding')) {
                return 'Recruitment & Jobs';
            }
            if (
                str_starts_with($name, 'setting') || 
                str_starts_with($name, 'email') || 
                str_starts_with($name, 'organisation') || 
                str_starts_with($name, 'company') || 
                str_starts_with($name, 'branch') ||
                str_starts_with($name, 'department') ||
                str_starts_with($name, 'designation')
            ) {
                return 'Settings & System';
            }
            if (
                str_starts_with($name, 'role') || 
                str_starts_with($name, 'permission') || 
                str_starts_with($name, 'access') ||
                str_starts_with($name, 'menu')
            ) {
                return 'Access Control';
            }

            if (str_contains($name, '.')) {
                $parts = explode('.', $name);
                return ucwords(str_replace(['_', '-'], ' ', $parts[0]));
            }

            return 'General Permissions';
        })->filter(function ($group, $key) {
            return !empty($key);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] = 'Roles & Access Management';
        $data['totalRoles'] = Role::count();
        $data['totalPermissions'] = Permission::count();
        $data['totalUsers'] = User::count();
        $data['webRolesCount'] = Role::where('guard_name', 'web')->count();

        return view('role.list', $data);
    }

    /**
     * AJAX DataTables endpoint for roles list.
     */
    public function list(Request $request)
    {
        $search = trim((string) $request->input('search.value', ''));
        $limit = (int) $request->input('length', 10);
        $start = max(0, (int) $request->input('start', 0));

        $query = Role::with(['permissions'])->withCount(['permissions']);
        $totalRecords = Role::count();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('guard_name', 'like', "%{$search}%")
                    ->orWhereHas('permissions', function ($permissionQuery) use ($search) {
                        $permissionQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $filteredRecords = (clone $query)->count();

        $orderColumn = (int) $request->input('order.0.column', 0);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderColumns = ['id', 'name', 'guard_name', 'permissions_count', 'created_at', null];

        if (isset($orderColumns[$orderColumn]) && $orderColumns[$orderColumn]) {
            $query->reorder()->orderBy($orderColumns[$orderColumn], $orderDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        if ($limit !== -1) {
            $query->skip($start)->take(max(1, $limit));
        }

        $roles = $query->get();

        // Calculate users count per role
        $roleIds = $roles->pluck('id')->all();
        $userCounts = User::whereIn('role_id', $roleIds)
            ->selectRaw('role_id, count(*) as total')
            ->groupBy('role_id')
            ->pluck('total', 'role_id');

        $rows = $roles->map(function ($role, $index) use ($start, $userCounts) {
            $permCount = $role->permissions->count();
            $assignedUsers = $userCounts[$role->id] ?? 0;

            // Generate pretty permissions badge preview
            if ($permCount > 0) {
                $firstThree = $role->permissions->take(3)->map(function ($p) {
                    return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 px-2 py-0.5 me-1 mb-1">' . e($p->name) . '</span>';
                })->implode('');

                $remaining = $permCount - 3;
                $moreBadge = $remaining > 0
                    ? '<span class="badge bg-light text-muted border fs-11 px-2 py-0.5 mb-1" title="' . e($role->permissions->skip(3)->pluck('name')->join(', ')) . '">+' . $remaining . ' more</span>'
                    : '';
                $permissionsHtml = '<div class="d-flex flex-wrap align-items-center">' . $firstThree . $moreBadge . '</div>';
            } else {
                $permissionsHtml = '<span class="badge bg-secondary-subtle text-secondary border fs-11 px-2 py-0.5"><i class="ti ti-lock me-1"></i>No Permissions</span>';
            }

            // Role icon by name
            $roleIcon = match (strtolower($role->name)) {
                'super admin', 'superadmin' => 'ti-shield-lock text-danger',
                'admin' => 'ti-user-shield text-primary',
                'hr' => 'ti-user-heart text-info',
                'employee' => 'ti-user-check text-success',
                'client' => 'ti-briefcase text-warning',
                default => 'ti-shield text-secondary'
            };

            $nameHtml = '
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm rounded-circle bg-light border d-inline-flex align-items-center justify-content-center">
                        <i class="ti ' . $roleIcon . ' fs-16"></i>
                    </span>
                    <div>
                        <a href="' . route('roles.edit', $role->id) . '" class="fw-bold text-dark text-decoration-none hover-primary fs-13">' . e(ucfirst($role->name)) . '</a>
                        <div class="text-muted fs-11 mt-0.5"><span class="badge bg-light text-muted border px-1.5 py-0.5 fs-10">' . e($role->guard_name) . '</span></div>
                    </div>
                </div>
            ';

            $usersBadge = '
                <span class="badge ' . ($assignedUsers > 0 ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-light text-muted border') . ' rounded-pill px-2.5 py-1 fs-12">
                    <i class="ti ti-users me-1"></i>' . $assignedUsers . ' ' . ($assignedUsers === 1 ? 'User' : 'Users') . '
                </span>
            ';

            $actionHtml = '
                <div class="d-flex align-items-center justify-content-end gap-1">
                    <a href="' . route('roles.edit', $role->id) . '" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm" title="Edit Role & Permissions">
                        <i class="ti ti-edit fs-14"></i> Edit
                    </a>
                    <form action="' . route('roles.destroy', $role->id) . '" method="POST" class="d-inline delete-role-form" style="display:inline-block;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-role d-inline-flex align-items-center shadow-sm" 
                                data-role-id="' . $role->id . '" 
                                data-role-name="' . e($role->name) . '" 
                                data-users-count="' . $assignedUsers . '" 
                                title="Delete Role">
                            <i class="ti ti-trash fs-14"></i>
                        </button>
                    </form>
                </div>
            ';

            return [
                'DT_RowIndex' => $start + $index + 1,
                'id' => $role->id,
                'name' => $nameHtml,
                'permissions' => $permissionsHtml,
                'permissions_count' => $permCount,
                'users' => $usersBadge,
                'created_at' => optional($role->created_at)->format('d M, Y') ?? '-',
                'action' => $actionHtml,
            ];
        })->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $rows,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = 'Create New Role';
        $allPermissions = Permission::where('guard_name', 'web')->orderBy('name', 'ASC')->get();
        if ($allPermissions->isEmpty()) {
            $allPermissions = Permission::orderBy('name', 'ASC')->get();
        }
        $data['groupedPermissions'] = self::groupPermissions($allPermissions);
        $data['totalPermissionsCount'] = $allPermissions->count();
        $data['selectedPermissions'] = [];

        return view('role.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:roles,name',
            'guard_name' => 'nullable|string|in:web,client,api',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ], [
            'name.required' => 'Please enter a valid role name.',
            'name.unique' => 'A role with this name already exists.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $guardName = $request->input('guard_name', 'web') ?: 'web';
        $role = Role::create([
            'name' => trim($request->name),
            'guard_name' => $guardName,
        ]);

        if ($request->has('permissions')) {
            $submittedPermissions = (array) $request->input('permissions', []);
            $permissionModels = collect();
            foreach ($submittedPermissions as $permName) {
                $perm = Permission::firstOrCreate([
                    'name' => trim($permName),
                    'guard_name' => $role->guard_name,
                ]);
                $permissionModels->push($perm);
            }
            $role->syncPermissions($permissionModels);
        }

        return redirect()->route('role')->with('success', "Role '{$role->name}' created successfully with " . count($request->input('permissions', [])) . " permissions.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('roles.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $roleGuard = $role->guard_name ?: 'web';
        
        $allPermissions = Permission::where('guard_name', $roleGuard)->orderBy('name', 'ASC')->get();
        if ($allPermissions->isEmpty()) {
            $allPermissions = Permission::orderBy('name', 'ASC')->get();
        }
        $groupedPermissions = self::groupPermissions($allPermissions);

        return view('role.create', [
            'title' => 'Edit Role & Permissions',
            'role' => $role,
            'groupedPermissions' => $groupedPermissions,
            'totalPermissionsCount' => $allPermissions->count(),
            'selectedPermissions' => $role->permissions->pluck('name')->all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'guard_name' => 'nullable|string|in:web,client,api',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ], [
            'name.required' => 'Please enter a valid role name.',
            'name.unique' => 'Another role already has this name.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $guardName = $request->input('guard_name', $role->guard_name) ?: 'web';
        $role->update([
            'name' => trim($request->name),
            'guard_name' => $guardName,
        ]);

        $submittedPermissions = (array) $request->input('permissions', []);
        $permissionModels = collect();
        foreach ($submittedPermissions as $permName) {
            $perm = Permission::firstOrCreate([
                'name' => trim($permName),
                'guard_name' => $role->guard_name,
            ]);
            $permissionModels->push($perm);
        }
        $role->syncPermissions($permissionModels);

        return redirect()->route('role')->with('success', "Role '{$role->name}' updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        // Check if users are assigned
        $usersCount = User::where('role_id', $role->id)->count();
        if ($usersCount > 0) {
            return redirect()->route('role')->with('error', "Cannot delete role '{$role->name}' because {$usersCount} user(s) are currently assigned to it. Please reassign those users first.");
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('role')->with('success', "Role '{$roleName}' was deleted successfully.");
    }
}

