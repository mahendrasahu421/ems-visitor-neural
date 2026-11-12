<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAccess
{
    /**
     * Allow only Admin and Super Admin users to manage users, roles, and permissions.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $allowedRoleIds = [1, 2];
        $allowedRoleNames = ['admin', 'super admin', 'superadmin'];

        $hasAllowedRoleId = $user && in_array((int) $user->role_id, $allowedRoleIds, true);
        $hasAllowedRoleName = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole($allowedRoleNames);

        if (!$hasAllowedRoleId && !$hasAllowedRoleName) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only Admin can manage roles and permissions.',
                ], 403);
            }

            return redirect()->route('home')->with('error', 'Only Admin can manage roles and permissions.');
        }

        return $next($request);
    }
}
