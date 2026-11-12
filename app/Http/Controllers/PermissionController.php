<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Determine module name from permission name.
     */
    public static function getModuleFromPermission(string $permissionName): string
    {
        $name = strtolower($permissionName);

        if (str_starts_with($name, 'dashboard') || str_starts_with($name, 'supervisor.dashboard')) return 'Dashboard';
        if (str_starts_with($name, 'user') || str_starts_with($name, 'admin.user')) return 'User Management';
        if (
            str_starts_with($name, 'employee') || 
            str_starts_with($name, 'admin_employee') || 
            str_starts_with($name, 'employee-report') || 
            str_starts_with($name, 'team') || 
            str_starts_with($name, 'supervisor')
        ) return 'Employee Management';
        if (str_starts_with($name, 'attendance')) return 'Attendance Tracking';
        if (str_starts_with($name, 'leave') || str_starts_with($name, 'holiday')) return 'Leaves & Holidays';
        if (str_starts_with($name, 'payroll') || str_starts_with($name, 'salary') || str_starts_with($name, 'tax')) return 'Payroll & Finance';
        if (str_starts_with($name, 'invoice') || str_starts_with($name, 'payment') || str_starts_with($name, 'expense') || str_starts_with($name, 'ledger')) return 'Invoicing & Payments';
        if (str_starts_with($name, 'client') || str_starts_with($name, 'ticket') || str_starts_with($name, 'admin.client')) return 'Clients & Tickets';
        if (str_starts_with($name, 'project')) return 'Projects & Tasks';
        if (str_starts_with($name, 'recruitment') || str_starts_with($name, 'job') || str_starts_with($name, 'onboarding')) return 'Recruitment & Jobs';
        if (
            str_starts_with($name, 'setting') || 
            str_starts_with($name, 'email') || 
            str_starts_with($name, 'organisation') || 
            str_starts_with($name, 'company') || 
            str_starts_with($name, 'branch') ||
            str_starts_with($name, 'department') ||
            str_starts_with($name, 'designation')
        ) return 'Settings & System';
        if (
            str_starts_with($name, 'role') || 
            str_starts_with($name, 'permission') || 
            str_starts_with($name, 'access') ||
            str_starts_with($name, 'menu')
        ) return 'Access Control';

        if (str_contains($name, '.')) {
            $parts = explode('.', $name);
            return ucwords(str_replace(['_', '-'], ' ', $parts[0]));
        }

        return 'General Permissions';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allPermissions = Permission::all();
        $uniqueModules = $allPermissions->map(fn($p) => self::getModuleFromPermission($p->name))->unique()->count();

        $data['title'] = 'Permissions Management';
        $data['totalPermissions'] = $allPermissions->count();
        $data['totalModules'] = $uniqueModules;
        $data['webPermissionsCount'] = $allPermissions->where('guard_name', 'web')->count();
        $data['clientPermissionsCount'] = $allPermissions->where('guard_name', 'client')->count();
        $data['totalRoles'] = Role::count();

        return view('permissions.list', $data);
    }

    /**
     * AJAX DataTables endpoint for permissions list.
     */
    public function list(Request $request)
    {
        $search = trim((string) $request->input('search.value', ''));
        $limit = (int) $request->input('length', 10);
        $start = max(0, (int) $request->input('start', 0));
        $moduleFilter = $request->input('module_filter');
        $guardFilter = $request->input('guard_filter');

        $query = Permission::with(['roles'])->withCount(['roles']);
        $totalRecords = Permission::count();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('guard_name', 'like', "%{$search}%")
                    ->orWhereHas('roles', function ($roleQuery) use ($search) {
                        $roleQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($guardFilter && $guardFilter !== 'all') {
            $query->where('guard_name', $guardFilter);
        }

        $filteredRecords = (clone $query)->count();

        $orderColumn = (int) $request->input('order.0.column', 0);
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $orderColumns = ['id', 'name', 'guard_name', 'roles_count', 'created_at', null];

        if (isset($orderColumns[$orderColumn]) && $orderColumns[$orderColumn]) {
            $query->reorder()->orderBy($orderColumns[$orderColumn], $orderDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        if ($limit !== -1) {
            $query->skip($start)->take(max(1, $limit));
        }

        $permissions = $query->get();

        $rows = $permissions->map(function ($permission, $index) use ($start) {
            $moduleName = self::getModuleFromPermission($permission->name);
            $rolesCount = $permission->roles->count();

            // Module badge
            $moduleBadge = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-11"><i class="ti ti-folder me-1"></i>' . e($moduleName) . '</span>';

            // Guard badge
            $guardClass = match ($permission->guard_name) {
                'client' => 'bg-warning-subtle text-warning border-warning-subtle',
                'api' => 'bg-info-subtle text-info border-info-subtle',
                default => 'bg-light text-dark border'
            };
            $guardBadge = '<span class="badge ' . $guardClass . ' border px-2 py-0.5 fs-11">' . e($permission->guard_name) . '</span>';

            // Roles preview
            if ($rolesCount > 0) {
                $rolesPreview = $permission->roles->pluck('name')->map(fn($r) => '<span class="badge bg-light text-secondary border fs-10 px-1.5 py-0.5 me-1">' . e(ucfirst($r)) . '</span>')->implode('');
                $rolesHtml = '<div class="d-flex flex-wrap align-items-center"><span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fs-11 me-1">' . $rolesCount . ' ' . ($rolesCount === 1 ? 'Role' : 'Roles') . '</span>' . $rolesPreview . '</div>';
            } else {
                $rolesHtml = '<span class="badge bg-light text-muted border px-2 py-0.5 fs-11"><i class="ti ti-alert-circle me-1"></i>Unassigned</span>';
            }

            $nameHtml = '
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar avatar-xs rounded bg-light text-primary d-inline-flex align-items-center justify-content-center">
                        <i class="ti ti-key fs-13"></i>
                    </span>
                    <span class="fw-bold text-dark font-monospace fs-13">' . e($permission->name) . '</span>
                </div>
            ';

            $actionHtml = '
                <div class="d-flex align-items-center justify-content-end gap-1">
                    <a href="' . route('permissions.edit', $permission->id) . '" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm" title="Edit Permission">
                        <i class="ti ti-edit fs-14"></i> Edit
                    </a>
                    <form action="' . route('permissions.destroy', $permission->id) . '" method="POST" class="d-inline delete-permission-form" style="display:inline-block;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-permission d-inline-flex align-items-center shadow-sm" 
                                data-permission-id="' . $permission->id . '" 
                                data-permission-name="' . e($permission->name) . '" 
                                data-roles-count="' . $rolesCount . '" 
                                title="Delete Permission">
                            <i class="ti ti-trash fs-14"></i>
                        </button>
                    </form>
                </div>
            ';

            return [
                'DT_RowIndex' => $start + $index + 1,
                'id' => $permission->id,
                'name' => $nameHtml,
                'module' => $moduleBadge,
                'guard_name' => $guardBadge,
                'roles' => $rolesHtml,
                'created_at' => optional($permission->created_at)->format('d M, Y') ?? '-',
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
        $data['title'] = 'Create Permissions';
        $data['existingPermissions'] = Permission::pluck('name')->all();

        return view('permissions.create', $data);
    }

    /**
     * Store a newly created resource in storage (Supports Single or Bulk Generation).
     */
    public function store(Request $request)
    {
        $mode = $request->input('creation_mode', 'single');

        if ($mode === 'bulk') {
            // Bulk Module Permission Generator
            $validator = Validator::make($request->all(), [
                'module_prefix' => 'required|string|max:50|regex:/^[a-zA-Z0-9_\-]+$/',
                'actions' => 'required|array|min:1',
                'actions.*' => 'string|max:50',
                'guard_name' => 'nullable|string|in:web,client,api',
            ], [
                'module_prefix.required' => 'Please enter a valid module prefix (e.g. invoice, payroll).',
                'module_prefix.regex' => 'Module prefix can only contain letters, numbers, underscores and hyphens.',
                'actions.required' => 'Please select at least one action permission to generate.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }

            $prefix = strtolower(trim($request->module_prefix));
            $guardName = $request->input('guard_name', 'web') ?: 'web';
            $actions = $request->input('actions', []);
            $createdCount = 0;
            $skippedCount = 0;

            foreach ($actions as $action) {
                $permName = "{$prefix}.{$action}";
                $exists = Permission::where('name', $permName)->where('guard_name', $guardName)->exists();
                if (!$exists) {
                    Permission::create([
                        'name' => $permName,
                        'guard_name' => $guardName,
                    ]);
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }

            $message = "Generated {$createdCount} new permission(s) for module '{$prefix}'.";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} already existed and were skipped).";
            }

            return redirect()->route('permission')->with('success', $message);
        }

        // Single Permission Creation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:permissions,name',
            'guard_name' => 'nullable|string|in:web,client,api',
        ], [
            'name.required' => 'Please enter a permission name.',
            'name.unique' => 'A permission with this name already exists.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $guardName = $request->input('guard_name', 'web') ?: 'web';
        $permission = Permission::create([
            'name' => trim($request->name),
            'guard_name' => $guardName,
        ]);

        return redirect()->route('permission')->with('success', "Permission '{$permission->name}' created successfully!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('permissions.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('permissions.create', [
            'title' => 'Edit Permission',
            'permission' => Permission::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:permissions,name,' . $permission->id,
            'guard_name' => 'nullable|string|in:web,client,api',
        ], [
            'name.required' => 'Please enter a permission name.',
            'name.unique' => 'Another permission already has this name.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $guardName = $request->input('guard_name', $permission->guard_name) ?: 'web';
        $permission->update([
            'name' => trim($request->name),
            'guard_name' => $guardName,
        ]);

        return redirect()->route('permission')->with('success', "Permission '{$permission->name}' updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $rolesCount = $permission->roles()->count();

        $name = $permission->name;
        $permission->delete();

        return redirect()->route('permission')->with('success', "Permission '{$name}' was deleted successfully (removed from {$rolesCount} role(s)).");
    }
}

