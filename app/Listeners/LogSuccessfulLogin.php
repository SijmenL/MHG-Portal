<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Session;
use App\Models\Log;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Authenticated  $event
     * @return void
     */
    public function handle(Authenticated $event)
    {
        // Check if the login event has been logged already in this session
        if (!Session::has('login_logged')) {
            // Log the successful login
            $userId = $event->user->id;
            $log = new Log();
            $log->createLog($userId, 2, 'Logged in', '', '', '');

            // Set the flag to prevent logging on subsequent refreshes
            Session::put('login_logged', true);
        }
    }
}
