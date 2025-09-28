<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        if (!$user) abort(401);

        $userRole = strtolower($user->role ?? '');
        $roles    = array_map('strtolower', $roles);

        abort_unless(in_array($userRole, $roles, true), 403);

        return $next($request);
    }
}
