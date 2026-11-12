<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdminAccess
{
    /**
     * Allow only Super Admin users to access user management.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $allowedRoleIds = [1, 2];
        $allowedRoleNames = ['super admin', 'superadmin', 'admin'];

        $hasAllowedRoleId = $user && in_array((int) $user->role_id, $allowedRoleIds, true);
        $hasAllowedRoleName = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole($allowedRoleNames);

        if (!$hasAllowedRoleId && !$hasAllowedRoleName) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only Super Admin can manage users.',
                ], 403);
            }

            return redirect()->route('home')->with('error', 'Only Super Admin can manage users.');
        }

        return $next($request);
    }
}
