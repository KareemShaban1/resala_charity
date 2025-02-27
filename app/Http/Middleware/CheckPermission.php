<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();

        // Bypass permission check if the user is a Super Admin
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        // Normal permission check
        if (!$user->hasPermissionTo($permission)) {
            abort(403, __('You do not have permission to perform this action.'));
        }

        return $next($request);
    }
}
