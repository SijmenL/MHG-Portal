<?php

namespace App\Http\Controllers;

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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        // Check if the user is authenticated
        // Retrieve the user's roles
        $roles = $user->roles()->orderBy('role', 'asc')->get();



// Specify the desired timezone
        $timezone = new DateTimeZone('Europe/Amsterdam'); // Replace with your desired timezone

// Create a DateTime object with the current date and time in the specified timezone
        $date = new DateTime('now', $timezone);

// Format the date as a string
        $formattedDate = $date->format('d-m-Y H:i:s');

        return view('dashboard', ['user' => $user, 'roles' => $roles, 'date' => $formattedDate]);
    }
}
