<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
// Check if the user has the 'admin' role
        if (!$request->user() || !$request->user()->roles->contains('role', 'Administratie')) {
            abort(403, 'Dit is niet de bedoeling');
        }

        return $next($request);
    }
}
