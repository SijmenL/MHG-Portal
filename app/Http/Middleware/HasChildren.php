<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasChildren
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->children()->count() > 0) {
            return $next($request);
        }

        $log = new Log();
        $log->createLog(auth()->user()->id, 1, 'Bekijk kinderen', $request->route()->getName(), '', 'Gebruiker heeft geen kinderen');

        return redirect()->route('dashboard')->with('error', 'Je hebt geen kinderen aan je account gelinkt.');
    }
}
