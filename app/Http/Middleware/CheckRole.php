<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akaunti yako imezimwa. Wasiliana na Uongozi wa Famasi.');
        }

        // Admin always has access
        if ($user->isAdmin()) {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Huna idhini ya kufikia ukurasa huu (Unauthorized Access - Role Restriction).');
        }

        return $next($request);
    }
}
