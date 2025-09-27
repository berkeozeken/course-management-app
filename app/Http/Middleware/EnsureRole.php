<?php

namespace App\Http\Middleware;

use Closure;

class EnsureRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        $u = $request->user();
        abort_unless($u && in_array($u->role, $roles), 403);
        return $next($request);
    }
}
