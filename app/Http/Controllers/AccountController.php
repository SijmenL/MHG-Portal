<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function myAccount()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


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

        if (isset($request->profile_picture)) {
            // Process and save the uploaded image
            $newPictureName = time() . '-' . $request->name . '.' . $request->profile_picture->extension();
            $destinationPath = 'profile_pictures';
            $request->profile_picture->move($destinationPath, $newPictureName);
        }

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


            $user->save();

            return redirect()->route('dashboard')->with('success', 'Account succesvol bijgewerkt');
    }
}
