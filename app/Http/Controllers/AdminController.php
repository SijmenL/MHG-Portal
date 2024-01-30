<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function admin()
    {
        $user = Auth::user();
        $roles = $user->roles;

        return view('admin.admin', ['user' => $user, 'roles' => $roles]);
    }

    public function accountManagement()
    {
        $user = Auth::user();
        $roles = $user->roles;

        $search = '';

        $users = User::paginate(25);

        $all_roles = Role::all();

        $selected_role = '';

        return view('admin.account_management.list', ['user' => $user, 'roles' => $roles, 'users' => $users, 'search' => $search, 'all_roles' => $all_roles, 'selected_role' => $selected_role]);
    }

    public function accountManagementSearch(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles;

        $search = $request->input('search');
        $selected_role = $request->input('role');

        if ($selected_role !== 'none') {
            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('sex', 'like', '%' . $search . '%')
                    ->orWhere('infix', 'like', '%' . $search . '%')
                    ->orWhere('birth_date', 'like', '%' . $search . '%')
                    ->orWhere('street', 'like', '%' . $search . '%')
                    ->orWhere('postal_code', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            })
                ->whereHas('roles', function ($query) use ($selected_role) {
                    $query->where('role', $selected_role);
                })
                ->paginate(25);
        } else {
            $users = User::where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('sex', 'like', '%' . $search . '%')
                    ->orWhere('infix', 'like', '%' . $search . '%')
                    ->orWhere('birth_date', 'like', '%' . $search . '%')
                    ->orWhere('street', 'like', '%' . $search . '%')
                    ->orWhere('postal_code', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            })->paginate(25);
        }


        $all_roles = Role::all();

        return view('admin.account_management.list', ['user' => $user, 'roles' => $roles, 'users' => $users, 'search' => $search, 'all_roles' => $all_roles, 'selected_role' => $selected_role]);
    }

    public function accountDetails($id)
    {
        $user = Auth::user();
        $roles = $user->roles;

        $account = User::find($id);

        return view('admin.account_management.details', ['user' => $user, 'roles' => $roles, 'account' => $account]);
    }

    public function editAccount($id)
    {
        $user = Auth::user();
        $roles = $user->roles;

        $account = User::find($id);

        return view('admin.account_management.edit', ['user' => $user, 'roles' => $roles, 'account' => $account]);
    }

    public function storeAccount(Request $request, $id)
    {
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
            'member_date' => 'nullable|date',
            'profile_picture' => 'nullable|mimes:jpeg,png,jpg,gif,webp',
        ]);

        if (isset($request->profile_picture)) {
            // Process and save the uploaded image
            $newPictureName = time() . '-' . $request->name . '.' . $request->profile_picture->extension();
            $destinationPath = 'profile_pictures';
            $request->profile_picture->move($destinationPath, $newPictureName);
        }

        $user = User::find($id);;


        if (!$user) {
            return redirect()->back()->with('error', 'Gebruiker niet gevonden');
        }

        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()->withErrors(['email' => 'Dit emailadres is al in gebruik.']);
        } else {
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
            $user->member_date = $request->input('member_date');

            if (isset($request->profile_picture)) {
                $user->profile_picture = $newPictureName;
            }

            $user->save();

            return redirect()->route('admin.account-management.details', ['id' => $user->id])->with('success', 'Account succesvol bijgewerkt');
        }
    }

    public function deleteAccount($id)
    {
        $user = User::find($id);

        if ($id === (string) Auth::id()) {
            return redirect()->back()->with('error', 'Je kunt jezelf niet verwijderen.');
        } else {
            $user->delete();
            return redirect()->route('admin.account-management')->with('success', 'Gebruiker verwijderd');
        }
    }

    public function createAccount()
    {
        $user = Auth::user();
        $roles = $user->roles;

        return view('admin.account_management.create_account', ['user' => $user, 'roles' => $roles]);
    }

    public function createAccountStore(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles;

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255',
            'password' => 'string|min:8',
            'sex' => 'nullable|string',
            'infix' => 'nullable|string',
            'last_name' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'street' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'avg' => 'nullable|bool',
            'member_date' => 'nullable|date',
            'profile_picture' => 'nullable|mimes:jpeg,png,jpg,gif,webp',
        ]);

        if (isset($request->profile_picture)) {
            // Process and save the uploaded image
            $newPictureName = time() . '-' . $request->name . '.' . $request->profile_picture->extension();
            $destinationPath = 'profile_pictures';
            $request->profile_picture->move($destinationPath, $newPictureName);
        }

        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()->withErrors(['email' => 'Dit emailadres is al in gebruik.']);
        } else {
            $user = User::create([
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'sex' => $request->input('sex'),
                'name' => $request->input('name'),
                'infix' => $request->input('infix'),
                'last_name' => $request->input('last_name'),
                'birth_date' => $request->input('birth_date'),
                'street' => $request->input('street'),
                'postal_code' => $request->input('postal_code'),
                'city' => $request->input('city'),
                'phone' => $request->input('phone'),
                'member_date' => $request->input('member_date'),
            ]);

            if (isset($request->profile_picture)) {
                $user->profile_picture = $newPictureName;
                $user->save();
            }

            return redirect()->route('admin.create-account', ['user' => $user, 'roles' => $roles])->with('success', 'Gebruiker succesvol aangemaakt');

        }
    }
}
