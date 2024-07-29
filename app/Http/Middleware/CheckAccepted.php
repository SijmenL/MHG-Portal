<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;

class CheckAccepted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if the user has been accepted
        if (!auth()->user()->accepted) {
            // If not accepted, redirect or return an error response
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Bekijk pagina', $request->route()->getName(), '', 'Gebruiker is nog niet geaccepteerd');

            return redirect()->route('dashboard')->with('error', 'Je hebt nog geen toegang tot deze pagina.');
        }

        // If the user has been accepted, proceed with the request
        return $next($request);
    }
}
