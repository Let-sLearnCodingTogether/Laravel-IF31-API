<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if($request->user()->role != $role) {
            return response()->json([
                'messge' => 'Role anda tidak dapat mengakses halaman ini',
                'data' => null
            ], 403);
        }
        return $next($request);
    }
}
