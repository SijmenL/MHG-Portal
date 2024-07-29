<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Log;
use App\Models\Notification;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;


class NonLoggedInController extends Controller
{
    public function contact() {

        return view('forms.contact.contact');
    }

    public function contactSubmit(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'string|max:20|nullable',
            'message' => 'required|string'
        ]);

        $contact = Contact::create([
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'message' => $request->input('message'),
            'done' => false,
        ]);

        $log = new Log();
        $log->createLog(null, 2, 'Contact', 'Admin', $contact->id, 'Contact formulier opgeslagen');

        return view('forms.contact.succes');
    }

    public function inschrijven() {

        return view('forms.inschrijven.inschrijven');
    }

    public function inschrijvenSubmit(Request $request)
    {
        $request->validate([
            'name' => 'string|max:255|required',
            'email' => 'string|email|max:255|unique:users,email|required',
            'sex' => 'string|required',
            'infix' => 'nullable|string',
            'last_name' => 'string|required',
            'birth_date' => 'date|required',
            'street' => 'string|required',
            'postal_code' => 'string|required',
            'city' => 'string|required',
            'phone' => 'string|required',
            'avg' => 'bool',
            'new_password' => 'required|confirmed|min:8',
            'voorwaarden' => 'required',
            'speltak' => 'required'
        ]);

        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()->withErrors(['email' => 'Dit emailadres is al in gebruik.']);
        } else {
            if (!$request->input('avg')) {
                $avg = false;
            } else {
                $avg = true;
            }

            $account = User::create([
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('new_password')),
                'sex' => $request->input('sex'),
                'name' => $request->input('name'),
                'infix' => $request->input('infix'),
                'last_name' => $request->input('last_name'),
                'birth_date' => $request->input('birth_date'),
                'street' => $request->input('street'),
                'postal_code' => $request->input('postal_code'),
                'city' => $request->input('city'),
                'phone' => $request->input('phone'),
                'avg' => $avg,
                'accepted' => false,
                'member_date' => Date::now(),
            ]);

            if ($request->input('speltak') === 'dolfijnen') {
                $role = Role::where('role', 'Dolfijn')->first();
                $account->roles()->syncWithoutDetaching([$role->id]);
            }
            if ($request->input('speltak') === 'zeeverkenners') {
                $role = Role::where('role', 'Zeeverkenner')->first();
                $account->roles()->syncWithoutDetaching([$role->id]);
            }
            if ($request->input('speltak') === 'loodsen') {
                $role = Role::where('role', 'Loods')->first();
                $account->roles()->syncWithoutDetaching([$role->id]);
            }
            if ($request->input('speltak') === 'afterloodsen') {
                $role = Role::where('role', 'Afterloods')->first();
                $account->roles()->syncWithoutDetaching([$role->id]);
            }

            $log = new Log();
            $log->createLog(null, 2, 'Inschrijven', 'Inschrijven', $account->name.' '.$account->infix.' '.$account->last_name, 'Nieuw account aangemaakt en rol toegevoegd');

            $notification = new Notification();
            $notification->sendNotification(null, [$account->id], 'Welkom bij de MHG! Je account is succesvol aangemaakt!.', '', '');


            return view('forms.inschrijven.succes');

        }
    }
}
