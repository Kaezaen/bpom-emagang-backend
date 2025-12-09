<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
    if (!$request->user() || $request->user()->role->name !== $role) {
        return response()->json([
            'status' => false,
            'message' => 'Forbidden'
        ], 403);
    }

    return $next($request);
    }
}