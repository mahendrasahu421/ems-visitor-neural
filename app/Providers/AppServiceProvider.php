<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Menu;
use Illuminate\Support\Str;


use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super Admin gets all permissions; Admin currently gets role and permission access
        Gate::before(function ($user, $ability) {
            if ($user && ($user->role_id == 1 || (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')))) {
                return true;
            }

            // Admin currently has access to roles and permissions
            if ($user && ($user->role_id == 2 || (method_exists($user, 'hasRole') && $user->hasRole('Admin')))) {
                if (str_starts_with($ability, 'role.') || str_starts_with($ability, 'permission.') || in_array($ability, ['role', 'permission'])) {
                    return true;
                }
            }
        });

        view()->composer('*', function ($view) {
            if (!Schema::hasTable('menus')) {
                $view->with('menus', collect());

                return;
            }

            $user = Auth::user();
            $client = Auth::guard('client')->user();

            if ($user || $client) {
                $roleId = $user?->role_id ?? $client?->role_id;
                $isAdminRole = $user
                    && method_exists($user, 'hasAnyRole')
                    && $user->hasAnyRole(['Super Admin', 'Admin', 'super admin', 'superadmin', 'admin']);

                if (in_array((int) $roleId, [1, 2], true) || $isAdminRole) {
                    // Admin and Super Admin => show all menus.
                    $menus = Menu::whereNull('parent_id')
                        ->with(['permission', 'children.permission', 'children.children.permission'])
                        ->orderBy('order', 'asc')
                        ->get();
                } else {
                    // Other roles => show role-wise menus
                    $menus = Menu::whereNull('parent_id')
                        ->whereHas('roles', fn($q) => $q->where('role_id', $roleId))
                        ->with([
                            'permission',
                            'children' => fn($q) => $q
                                ->whereHas('roles', fn($qr) => $qr->where('role_id', $roleId))
                                ->with([
                                    'permission',
                                    'children' => fn($cq) => $cq
                                        ->whereHas('roles', fn($qr) => $qr->where('role_id', $roleId))
                                        ->with('permission'),
                                ]),
                        ])
                        ->orderBy('order', 'asc')
                        ->get();

                    $menus = $this->filterMenusByPermissions($menus, $user ?? $client);
                }

                if (config('company.profile') === 'limited_company') {
                    $menus = $this->filterLimitedMenus($menus);
                }
            } else {
                // Not logged in
                $menus = collect();
            }

            $view->with('menus', $menus);
        });

        Paginator::useBootstrap();

    }

    private function filterLimitedMenus($menus)
    {
        $allowedRoutes = config('company.limited_menu_routes', []);
        $allowedPaths = config('company.limited_path_patterns', []);

        return $menus
            ->map(function ($menu) use ($allowedRoutes, $allowedPaths) {
                $children = $menu->children
                    ->filter(fn ($child) => $this->isLimitedMenuAllowed($child->route, $allowedRoutes, $allowedPaths))
                    ->values();

                $menu->setRelation('children', $children);

                return $menu;
            })
            ->filter(function ($menu) use ($allowedRoutes, $allowedPaths) {
                return $menu->children->isNotEmpty()
                    || $this->isLimitedMenuAllowed($menu->route, $allowedRoutes, $allowedPaths);
            })
            ->values();
    }

    private function filterMenusByPermissions($menus, $actor)
    {
        if (!$actor) {
            return collect();
        }

        return $menus
            ->map(function ($menu) use ($actor) {
                $children = $this->filterMenusByPermissions($menu->children ?? collect(), $actor);
                $menu->setRelation('children', $children);

                return $menu;
            })
            ->filter(function ($menu) use ($actor) {
                $hasChildren = ($menu->children ?? collect())->isNotEmpty();
                $menuPermissionAllowed = $this->isMenuPermissionAllowed($menu, $actor);

                if ($menu->route) {
                    return $menuPermissionAllowed;
                }

                return $hasChildren || ($menuPermissionAllowed && ($menu->children ?? collect())->isEmpty());
            })
            ->values();
    }

    private function isMenuPermissionAllowed(Menu $menu, $actor): bool
    {
        if (!$menu->permission_id) {
            return true;
        }

        $permission = $menu->permission;
        if (!$permission || !method_exists($actor, 'hasPermissionTo')) {
            return false;
        }

        try {
            return $actor->hasPermissionTo($permission);
        } catch (\Throwable) {
            return false;
        }
    }

    private function isLimitedMenuAllowed(?string $route, array $allowedRoutes, array $allowedPaths): bool
    {
        if (!$route) {
            return false;
        }

        return in_array($route, $allowedRoutes, true) || Str::is($allowedPaths, $route);
    }
}
