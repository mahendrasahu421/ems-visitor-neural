<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeActiveWeb
{
    /**
     * Ensure that the authenticated web employee is currently active.
     * If inactive, immediately invalidate their session, log them out, and redirect to login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $employee = Auth::guard('employee')->user();

        if ($employee && !$employee->isActive()) {
            Auth::guard('employee')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your employee account is inactive. Please contact HR or Administrator.',
                ], 403);
            }

            return redirect('/employee/login')
                ->with('error', 'Your employee account has been deactivated. Please contact HR or Administrator.');
        }

        return $next($request);
    }
}
