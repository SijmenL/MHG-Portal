<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DolfijnenController extends Controller
{
    public function view()
    {
        $user = Auth::user();

        return view('speltakken.dolfijnen.home', ['user' => $user]);
    }

    public function leiding()
    {
        $user = Auth::user();

        $hoofdleiding = User::whereHas('roles', function ($query) {
            $query->where('role', 'Dolfijnen Hoofdleiding');
        })->get();

        $penningmeester = User::whereHas('roles', function ($query) {
            $query->where('role', 'Dolfijnen Penningmeester');
        })->get();

        $other_leiding = User::whereHas('roles', function ($query) {
            $query->where('role', 'Dolfijnen Leiding');
        })->whereDoesntHave('roles', function ($query) {
            $query->whereIn('role', ['Dolfijnen Hoofdleiding', 'Dolfijnen Penningmeester']);
        })->get();

        $leiding = $hoofdleiding->merge($penningmeester)->merge($other_leiding);

        return view('speltakken.dolfijnen.leiding', ['user' => $user, 'leiding' => $leiding]);
    }
}
