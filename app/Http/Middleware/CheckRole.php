<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Check if the user is authenticated and has any of the specified roles
        if (!$request->user() || !$request->user()->roles->whereIn('role', $roles)->count()) {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze pagina.');
        }

        return $next($request);
    }


}
