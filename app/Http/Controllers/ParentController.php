<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function myChildren()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();
        $children = $user->children;

        return view('parent.children', ['user' => $user, 'roles' => $roles, 'children' => $children]);
    }

    public function editChild($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $account = User::findOrFail($id);

        if (!$account) {
            return redirect()->route('dashboard')->with('error', 'Kind bestaat niet.');
        }

        if (!$user->children->contains($account)) {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze pagina.');
        }

        return view('parent.edit_child', ['user' => $user, 'roles' => $roles, 'account' => $account]);
    }

    public function editChildSave(Request $request, $id)
    {
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $id,
            'sex' => 'string',
            'infix' => 'nullable|string',
            'last_name' => 'string',
            'birth_date' => 'date',
            'street' => 'string',
            'postal_code' => 'string',
            'city' => 'string',
            'phone' => 'string',
            'avg' => 'bool',
            'profile_picture' => 'nullable|mimes:jpeg,png,jpg,gif,webp',
        ]);

        $user = User::findOrFail($id);

        if (!$user) {
            return redirect()->route('dashboard')->with('error', 'Kind bestaat niet.');
        }

        if (!Auth::user()->children->contains($user)) {
            return redirect()->route('dashboard')->with('error', 'Je hebt geen toegang tot deze pagina.');
        }

        if (isset($request->profile_picture)) {
            // Process and save the uploaded image
            $newPictureName = time() . '-' . $request->name . '.' . $request->profile_picture->extension();
            $destinationPath = 'profile_pictures';
            $request->profile_picture->move($destinationPath, $newPictureName);
            $user->profile_picture = $newPictureName;
        }

        // Update user fields
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
        $user->avg = $request->input('avg');

        $user->save();

        return redirect()->route('children.edit', ['id' => $id])->with('success', 'Kind succesvol bijgewerkt');
    }

}