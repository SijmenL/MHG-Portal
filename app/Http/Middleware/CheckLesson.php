<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLesson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Roles that should always allow access
        $allowedRoles = [
            'Zeeverkenners Leiding',
            'Praktijkbegeleider',
            'Ouderraad',
            'Administratie',
            'Bestuur',
        ];

        // Allow access if the user has any of the allowed roles
        if ($user && $user->roles->pluck('role')->intersect($allowedRoles)->isNotEmpty()) {
            return $next($request);
        }

        // Allow access if the user is associated with any lessons
        if ($user && $user->lessons()->exists()) {
            return $next($request);
        }
        // Deny access if the user has no associated lessons
        return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze pagina.');
    }
}
