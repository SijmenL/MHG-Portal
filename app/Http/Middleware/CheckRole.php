<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!auth()->user()){
            return redirect()->route('login')->with('error', 'Je moet ingelogd zijn om deze pagina te bekijken.');
        }

        if (!$user) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Bekijk pagina', $request->route()->getName(), '', 'Gebruiker had geen toegang tot de pagina');

            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze pagina.');
        }


        if ($user->roles->whereIn('role', $roles)->count()) {
            return $next($request);
        }

        $childrenRoles = $user->children()
            ->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('role', ['Zeeverkenner', 'Dolfijn', 'Loods', 'After Loods']);
            })
            ->get();

        $childHasRole = false;

        foreach ($childrenRoles as $child) {
            foreach ($roles as $role) {
                if (in_array($role, $child->roles->pluck('role')->toArray()) &&
                    ($role === 'Dolfijn' || $role === 'Zeeverkenner' || $role === 'Loods' || $role === 'After Loods')) {
                    $childHasRole = true;
                }
            }
        }


        if (!$childHasRole) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Bekijk pagina', $request->route()->getName(), '', 'Gebruiker had geen toegang tot de pagina');

            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze pagina.');
        }

        return $next($request);
    }


}
