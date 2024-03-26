<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Notification;
use App\Models\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $timezone = new DateTimeZone('Europe/Amsterdam');
        $date = new DateTime('now', $timezone);

        $formattedDate = $date->format('d-m-Y H:i:s');

        $notifications = Notification::where('receiver_id', Auth::id())
            ->where('seen', false)
            ->count();

        if ($notifications > 100) {
            $notifications = "99+";
        }

        return view('home.dashboard', ['user' => $user, 'roles' => $roles, 'date' => $formattedDate, 'notifications' => $notifications]);
    }

    public function changelog() {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        return view('home.changelog', ['user' => $user, 'roles' => $roles]);
    }

    public function credits() {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        return view('home.credits', ['user' => $user, 'roles' => $roles]);
    }

    public function notifications() {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $notifications = Notification::where('receiver_id', Auth::id())
            ->orderBy('seen', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $notificationsUnseen = Notification::where('receiver_id', Auth::id())
            ->where('seen', false)
            ->get();

        foreach ($notificationsUnseen as $notification) {
            $notification->seen = true;
            $notification->save();
        }

        return view('home.notifications', ['user' => $user, 'roles' => $roles, 'notifications' => $notifications, 'notificationsUnseen' => $notificationsUnseen]);
    }
}
