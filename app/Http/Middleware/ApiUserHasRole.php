<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiUserHasRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->role_id === 1) {
            return $next($request);
        }

        abort(403, 'У вас нет доступа к этой странице.');
    }
}
