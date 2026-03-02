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


        if (!$user) {
            $log = new Log();
            $log->createLog(null, 1, 'Bekijk pagina', $request->route()->getName(), '', 'Gebruiker had geen toegang tot de pagina');

            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze pagina.');
        }


        if ($user->roles->whereIn('role', $roles)->count()) {
            return $next($request);
        }

        $targetRoles = ['Zeeverkenner', 'Dolfijn', 'Loods', 'Afterloods'];


        $childHasRole = $user->children()
            ->whereHas('roles', function ($query) use ($targetRoles) {
                $query->whereIn('role', $targetRoles);
            })
            ->where('accepted', true)
            ->whereNull('member_date_end')
            ->exists();

        if (!$childHasRole) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Bekijk pagina', $request->route()->getName(), '', 'Gebruiker had geen toegang tot de pagina');

            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze pagina.');
        }

        return $next($request);
    }


}
