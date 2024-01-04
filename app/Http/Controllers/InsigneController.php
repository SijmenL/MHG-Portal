<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Insigne;

class InsigneController extends Controller
{
    public function myInsignes()
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        // Check if the user is authenticated
        // Retrieve the user's roles
        $roles = $user->roles;

        $user_insignes = $user->insignes()->withPivot('date')->get();

        // Pluck the 'id' values from the $user_insignes collection
        $insigneIds = $user_insignes->pluck('id')->toArray();


        // Select only the Insigne items that are not in the $insigneIds array
        $insignes = Insigne::whereNotIn('id', $insigneIds)->get();

        return view('inisgnes.insignes', ['user' => $user, 'roles' => $roles, 'insignes' => $insignes, 'user_insignes' => $user_insignes]);
    }
}
