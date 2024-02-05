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
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        return view('admin.admin', ['user' => $user, 'roles' => $roles]);
    }

    // Account management

    public function accountManagement()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $search = '';

        $users = User::with(['roles' => function ($query) {
            $query->orderBy('role', 'asc');
        }])
            ->orderBy('last_name')
            ->paginate(25);

        $all_roles = Role::orderBy('role')->get();

        $selected_role = '';

        return view('admin.account_management.list', ['user' => $user, 'roles' => $roles, 'users' => $users, 'search' => $search, 'all_roles' => $all_roles, 'selected_role' => $selected_role]);
    }

    public function accountManagementSearch(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


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
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('dolfijnen_name', 'like', '%' . $search . '%');
            })
                ->whereHas('roles', function ($query) use ($selected_role) {
                    $query->where('role', $selected_role)->orderBy('role');
                })
                ->orderBy('last_name')
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
                    ->orWhere('id', 'like', '%' . $search . '%')
                    ->orWhere('dolfijnen_name', 'like', '%' . $search . '%');
            })
                ->orderBy('last_name')
                ->with(['roles' => function ($query) {
                    $query->orderBy('role');
                }])
                ->paginate(25);
        }
            $all_roles = Role::orderBy('role')->get();

        return view('admin.account_management.list', ['user' => $user, 'roles' => $roles, 'users' => $users, 'search' => $search, 'all_roles' => $all_roles, 'selected_role' => $selected_role]);
    }

    public function accountDetails($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $account = User::with(['roles' => function ($query) {
            $query->orderBy('role', 'asc');
        }])->find($id);


        return view('admin.account_management.details', ['user' => $user, 'roles' => $roles, 'account' => $account]);
    }

    public function editAccount($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $all_users = User::all();

        $all_roles = Role::all();

        $account = User::find($id);

        if($account !== null) {
            $selectedRoles = $account->roles->pluck('id')->toArray();
        } else {
            $selectedRoles = '';
        }

        $child_ids = $account->children()->pluck('users.id')->implode(', ');

        $parent_ids = $account->parents()->pluck('users.id')->implode(', ');

        return view('admin.account_management.edit', ['user' => $user, 'roles' => $roles, 'all_roles' => $all_roles, 'account' => $account, 'selectedRoles' => $selectedRoles, 'all_users' => $all_users, 'child_ids' => $child_ids, 'parent_ids' => $parent_ids]);
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
            'dolfijnen_name' => 'nullable|string',
            'children' => 'nullable|string',
            'parents' => 'nullable|string',
        ]);

        if (isset($request->children)) {
            $parent = User::findOrFail($id);
            $childIdsArray = array_map('intval', explode(',', $request->children));

            // Detach existing relationships
            $parent->children()->detach();

            // Attach the new relationships
            $children = User::find($childIdsArray);
            $parent->children()->attach($children);
        } else {
            $parent = User::findOrFail($id);
            $parent->children()->detach();
        }

        if (isset($request->parents)) {
            $child = User::findOrFail($id);
            $parentIdsArray = array_map('intval', explode(',', $request->parents));

            // Detach existing relationships
            $child->parents()->detach();

            // Attach the new relationships
            $parents = User::find($parentIdsArray);
            $parent->parents()->attach($parents);
        } else {
            $child = User::findOrFail($id);
            $child->parents()->detach();
        }


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

        $accountWithSameEmail = User::where('email', $request->email)->get();

        foreach ($accountWithSameEmail as $account) {
            if ($account->id !== $user->id) {
                return redirect()->back()->withErrors(['email' => 'Dit emailadres is al in gebruik door een andere gebruiker.']);
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
                $user->dolfijnen_name = $request->input('dolfijnen_name');
                $user->avg = $request->input('avg');

                if (isset($request->profile_picture)) {
                    $user->profile_picture = $newPictureName;
                }

                $user->save();

                $user->roles()->sync($request->input('roles'));

                return redirect()->route('admin.account-management.details', ['id' => $user->id])->with('success', 'Account succesvol bijgewerkt');
            }
        }
    }

    public function deleteAccount($id)
    {
        $user = User::find($id);

        if($user === null) {
            return redirect()->route('admin.account-management')->with('error', 'Geen gebruiker gevonden om te verwijderen');
        }
        if ($id === (string)Auth::id()) {
            return redirect()->back()->with('error', 'Je kunt jezelf niet verwijderen.');
        } else {
            $user->delete();
            return redirect()->route('admin.account-management')->with('success', 'Gebruiker verwijderd');
        }
    }

    public function createAccount()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $all_roles = Role::all();

        return view('admin.account_management.create_account', ['user' => $user, 'roles' => $roles, 'all_roles' => $all_roles]);
    }

    // Make account
    public function createAccountStore(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


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
            'dolfijnen_name' => 'nullable|string',
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
                'dolfijnen_name' => $request->input('dolfijnen_name'),
            ]);

            if (isset($request->profile_picture)) {
                $user->profile_picture = $newPictureName;
                $user->save();
            }

            if (!empty($request->roles)) {
                $user->roles()->attach($request->roles);
            }

            return redirect()->route('admin.create-account', ['user' => $user, 'roles' => $roles])->with('success', 'Gebruiker succesvol aangemaakt');

        }
    }


    // Rollen

    public function roleManagement()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $search = '';

        $all_roles = Role::orderBy('role')->paginate(25);


        return view('admin.role_management.list', ['user' => $user, 'roles' => $roles, 'all_roles' => $all_roles, 'search' => $search]);
    }

    public function roleManagementSearch(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $search = $request->input('search');

        $all_roles = Role::where(function ($query) use ($search) {
            $query->where('role', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        })->orderBy('role')->paginate(25);


        return view('admin.role_management.list', ['user' => $user, 'roles' => $roles, 'all_roles' => $all_roles, 'search' => $search]);
    }

    public function editRole($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $role = Role::find($id);

        return view('admin.role_management.edit', ['user' => $user, 'roles' => $roles, 'role' => $role]);
    }

    public function storeRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'string|max:255',
            'description' => 'string',
        ]);

        $role = Role::find($id);;

        if (!$role) {
            return redirect()->back()->with('error', 'Rol niet gevonden');
        }

        $role->role = $request->input('role');
        $role->description = $request->input('description');

        $role->save();

        return redirect()->route('admin.role-management')->with('success', 'Rol succesvol bijgewerkt');
    }

    public function deleteRole($id)
    {
        $role = Role::find($id);

        if($role === null) {
            return redirect()->route('admin.role-management')->with('error', 'Geen rol gevonden om te verwijderen');
        }

        $role->delete();
        return redirect()->route('admin.role-management')->with('success', 'Rol verwijderd');

    }

    public function createRole()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        return view('admin.role_management.create_role', ['user' => $user, 'roles' => $roles]);
    }

    // Make account
    public function createRoleStore(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $request->validate([
            'role' => 'string|max:255',
            'description' => 'string',
        ]);

        $role = Role::create([
            'role' => $request->input('role'),
            'description' => $request->input('description')
        ]);

        $role->save();

        return redirect()->route('admin.role-management', ['user' => $user, 'roles' => $roles])->with('success', 'Rol succesvol aangemaakt');

    }
}
