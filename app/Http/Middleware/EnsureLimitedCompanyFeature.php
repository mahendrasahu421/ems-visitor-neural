<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureLimitedCompanyFeature
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('company.profile') !== 'limited_company') {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        $allowedRoutePatterns = config('company.limited_route_patterns', []);

        if ($routeName && Str::is($allowedRoutePatterns, $routeName)) {
            return $next($request);
        }

        if (Str::is(config('company.limited_path_patterns', []), $request->path())) {
            return $next($request);
        }

        abort(404);
    }
}
