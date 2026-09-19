<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role?->name;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Redirect to appropriate dashboard based on role
        return redirect()->route('dashboard');
    }
}
