<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeApiAccess
{
    /**
     * Ensure protected API routes are accessible only to authenticated employees.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $employee = $request->user();

        if (!$employee instanceof Employee) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. APIs are available to employees only.',
            ], 403);
        }

        if (!$employee->isActive()) {
            // Revoke the current access token so the app cannot reuse it
            if (method_exists($employee, 'currentAccessToken') && $employee->currentAccessToken()) {
                $employee->currentAccessToken()->delete();
            }

            return response()->json([
                'success' => false,
                'message' => 'Your employee account is currently inactive. Please contact HR or Administrator.',
            ], 403);
        }

        return $next($request);
    }
}
