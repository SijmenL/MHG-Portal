<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function myAccount()
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        // Check if the user is authenticated
        // Retrieve the user's roles
        $roles = $user->roles;

        return view('account.account', ['user' => $user, 'roles' => $roles]);
    }

    public function updateAccount(Request $request) {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'sex' => 'nullable|string',
            'infix' => 'nullable|string',
            'last_name' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'street' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'avg' => 'nullable|bool',
            'profile_picture' => 'nullable|mimes:jpeg,png,jpg,gif,webp',
        ]);


        // Process and save the uploaded image
        $newPictureName = time() . '-' . $request->name . '.' . $request->profile_picture->extension();
        $destinationPath = 'profile_pictures';

        if ($request->profile_picture->move($destinationPath, $newPictureName)) {

            $user = Auth::user();

            if ($user['id'] !== Auth::id()) {
                return redirect('/home')->with('error|Geen toestemming!');
            }

            if (!$user) {
                return redirect()->back()->with('error|Gebruiker niet gevonden.');
            }

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->sex = $request->input('sex');
            $user->infix = $request->input('infix');
            $user->last_name = $request->input('last_name');
            $user->birth_date = $request->input('birth_date');
            $user->street = $request->input('street');
            $user->postal_code = $request->input('postal_code');
            $user->city = $request->input('city');
            $user->phone = $request->input('phone');
            $user->profile_picture = $newPictureName;

//            if ($request->input('password') !== "") {
//                $user->password = Hash::make($request->input('password'));
//            }

            $user->save();

            return redirect()->route('dashboard')->with('success', 'Account succesvol bijgewerkt');
        } else {
            return redirect()->route('account.update')->with('error', 'Hier gaat iets mis.');
        }
    }
}
