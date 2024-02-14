<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AfterloodsenController extends Controller
{
    public function view()
    {
        $user = Auth::user();

        return view('speltakken.afterloodsen.home', ['user' => $user]);
    }

    public function leiding()
    {
        $user = Auth::user();

        $hoofdleiding = User::whereHas('roles', function ($query) {
            $query->where('role', 'Afterloodsen Voorzitter');
        })->get();

        $penningmeester = User::whereHas('roles', function ($query) {
            $query->where('role', 'Afterloodsen Penningmeester');
        })->get();

        $other_leiding = User::whereHas('roles', function ($query) {
            $query->where('role', 'Afterloodsen Organisator');
        })->whereDoesntHave('roles', function ($query) {
            $query->whereIn('role', ['Afterloodsen Voorzitter', 'Afterloodsen Penningmeester']);
        })->get();

        $leiding = $hoofdleiding->merge($penningmeester)->merge($other_leiding);

        return view('speltakken.afterloodsen.leiding', ['user' => $user, 'leiding' => $leiding]);
    }

    public function group()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $search = '';

        $users = User::with(['roles' => function ($query) {
            $query->where('role', 'Afterloods')->orderBy('role', 'asc');
        }])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Afterloods');
            })
            ->orderBy('last_name')
            ->paginate(25);

        $selected_role = '';

        return view('speltakken.afterloodsen.group', ['user' => $user, 'roles' => $roles, 'users' => $users, 'search' => $search, 'selected_role' => $selected_role]);
    }

    public function groupSearch(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $search = $request->input('search');
        $selected_role = $request->input('role');

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
                ->orWhere('id', 'like', '%' . $search . '%')
                ->orWhere('dolfijnen_name', 'like', '%' . $search . '%');
        })
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Afterloods');
            })
            ->orderBy('last_name')
            ->paginate(25);


        $all_roles = Role::orderBy('role')->get();

        return view('speltakken.afterloodsen.group', ['user' => $user, 'roles' => $roles, 'users' => $users, 'search' => $search, 'all_roles' => $all_roles, 'selected_role' => $selected_role]);
    }

    public function groupDetails($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $account = User::with(['roles' => function ($query) {
            $query->orderBy('role', 'asc');
        }])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Afterloods');
            })
            ->find($id);



        return view('speltakken.afterloodsen.group_details', ['user' => $user, 'roles' => $roles, 'account' => $account]);
    }
}
