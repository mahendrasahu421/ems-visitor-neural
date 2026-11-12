<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class MapRolePermission extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Role-wise Menu Access Management';

        // Fetch hierarchical menu structure
        $data['menus'] = Menu::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('order', 'asc')->with(['children' => function ($subQuery) {
                    $subQuery->orderBy('order', 'asc');
                }]);
            }])
            ->orderBy('order', 'asc')
            ->get();

        // Fetch all roles with their assigned menus & permissions
        $data['roles'] = Role::with(['menus', 'permissions'])->orderBy('name', 'asc')->get();

        // Calculate assigned user counts per role
        $roleIds = $data['roles']->pluck('id')->all();
        $data['userCounts'] = User::whereIn('role_id', $roleIds)
            ->selectRaw('role_id, count(*) as total')
            ->groupBy('role_id')
            ->pluck('total', 'role_id');

        // Role-to-Menu mapping dictionary for quick JS switching
        $data['roleMenuMap'] = $data['roles']->mapWithKeys(function ($role) {
            return [$role->id => $role->menus->pluck('id')->values()];
        });

        // Summary Statistics
        $totalAllMenus = Menu::count();
        $totalTopMenus = $data['menus']->count();
        $totalSubMenus = $totalAllMenus - $totalTopMenus;
        $mappedRolesCount = $data['roles']->filter(fn($r) => $r->menus->count() > 0)->count();

        $data['stats'] = [
            'totalRoles' => $data['roles']->count(),
            'totalMenus' => $totalAllMenus,
            'totalTopMenus' => $totalTopMenus,
            'totalSubMenus' => $totalSubMenus,
            'mappedRoles' => $mappedRolesCount,
            'totalPermissions' => Permission::count(),
        ];

        // Currently selected role (from query or first available)
        $selectedRoleId = $request->query('role_id', optional($data['roles']->first())->id);
        $data['selectedRoleId'] = (int) $selectedRoleId;
        $data['selectedRole'] = $data['roles']->firstWhere('id', $data['selectedRoleId']) ?? $data['roles']->first();

        return view('map-role-permission.index', $data);
    }

    /**
     * Store or update menu mapping for a role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'menu_ids' => 'nullable|array',
            'menu_ids.*' => 'integer|exists:menus,id'
        ]);

        $role = Role::findOrFail($request->role_id);
        $menuIds = collect($request->input('menu_ids', []))->map(fn($id) => (int)$id)->all();

        // Automatically resolve and include parent menu IDs so parent navigations render correctly
        if (!empty($menuIds)) {
            $selectedMenus = Menu::whereIn('id', $menuIds)->get();
            $resolvedIds = $menuIds;

            foreach ($selectedMenus as $menu) {
                if ($menu->parent_id && !in_array($menu->parent_id, $resolvedIds)) {
                    $resolvedIds[] = $menu->parent_id;
                    $parent = Menu::find($menu->parent_id);
                    if ($parent && $parent->parent_id && !in_array($parent->parent_id, $resolvedIds)) {
                        $resolvedIds[] = $parent->parent_id;
                    }
                }
            }
            $menuIds = array_values(array_unique($resolvedIds));
        }

        $role->menus()->sync($menuIds);

        $msg = "Menu permissions for role '" . ucfirst($role->name) . "' updated successfully (" . count($menuIds) . " menus configured).";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => $msg,
                'role_id' => $role->id,
                'assigned_count' => count($menuIds),
                'menu_ids' => $menuIds
            ]);
        }

        return redirect()->route('map-role-permission', ['role_id' => $role->id])
            ->with('success', $msg);
    }
}
