<?php

namespace App\Http\Controllers;

use App\Models\FlunkyDJMusic;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoodsenController extends Controller
{
    public function view()
    {
        $user = Auth::user();

        return view('speltakken.loodsen.home', ['user' => $user]);
    }

    public function leiding()
    {
        $user = Auth::user();

        $stamoudste = User::whereHas('roles', function ($query) {
            $query->where('role', 'Loodsen Stamoudste');
        })->get();

        $penningmeester = User::whereHas('roles', function ($query) {
            $query->where('role', 'Loodsen Penningmeester');
        })->get();

        $leiding = $stamoudste->merge($penningmeester);

        return view('speltakken.loodsen.leiding', ['user' => $user, 'leiding' => $leiding]);
    }

    public function group()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $search = '';

        $users = User::with(['roles' => function ($query) {
            $query->where('role', 'Loods')->orderBy('role', 'asc');
        }])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Loods');
            })
            ->orderBy('last_name')
            ->paginate(25);

        $selected_role = '';

        return view('speltakken.loodsen.group', ['user' => $user, 'roles' => $roles, 'users' => $users, 'search' => $search, 'selected_role' => $selected_role]);
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
                $query->where('role', 'Loods');
            })
            ->orderBy('last_name')
            ->paginate(25);


        $all_roles = Role::orderBy('role')->get();

        return view('speltakken.loodsen.group', ['user' => $user, 'roles' => $roles, 'users' => $users, 'search' => $search, 'all_roles' => $all_roles, 'selected_role' => $selected_role]);
    }

    public function groupDetails($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $account = User::with(['roles' => function ($query) {
            $query->orderBy('role', 'asc');
        }])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Loods');
            })
            ->find($id);


        return view('speltakken.loodsen.group_details', ['user' => $user, 'roles' => $roles, 'account' => $account]);
    }

    public function flunkyball()
    {
        $user = Auth::user();

        return view('speltakken.loodsen.flunkyball.home', ['user' => $user]);
    }

    public function flunkydj()
    {
        $user = Auth::user();

        $music = FlunkyDJMusic::all();

        return view('speltakken.loodsen.flunkyball.flunkydj', ['user' => $user, 'music' => $music]);
    }

    public function music()
    {
        $user = Auth::user();

        $all_music = FlunkyDJMusic::paginate(25);

        return view('speltakken.loodsen.flunkyball.music', ['user' => $user, 'all_music' => $all_music]);
    }

    public function addMusic()
    {
        $user = Auth::user();

        return view('speltakken.loodsen.flunkyball.add_music', ['user' => $user]);
    }

    public function storeMusic(Request $request)
    {
        $validatedData = $request->validate([
            'display_title' => 'required|string',
            'music_title' => 'required|string',
            'image' => 'required|mimes:jpeg,png,jpg,gif,webp',
            'music_file' => 'required|mimes:mp3,wav',
            'fade_in' => 'required|integer',
            'fade_out' => 'required|integer',
            'play_type' => 'required|integer',
        ]);


        $newPictureName = time() . '-' . $request->music_title . '.' . $request->image->extension();
        $destinationPath = 'files/loodsen/flunkyball/music_images';

        $newAudioName = time() . '-' . $request->music_title . '.' . $request->music_file->extension();
        $audioDestinationPath = 'files/loodsen/flunkyball/music_files';

        if ($request->image->move($destinationPath, $newPictureName) && $request->music_file->move($audioDestinationPath, $newAudioName)) {
            // File was moved successfully

            // Add the product_owner field with the current user's ID
            $validatedData['music_owner'] = Auth::id();
            $validatedData['image'] = $newPictureName;
            $validatedData['music_file'] = $newAudioName;

            // Create the music
            $music = FlunkyDJMusic::create($validatedData);


            return redirect()->route('loodsen.flunkyball.flunkydj')->with("success", "Nummer toegevoegd!");
        } else {
            return redirect()->route('loodsen.flunkyball.admin')->with("success", "Er is iets mis gegaan, bestanden zijn niet verplaatst.");
        }
    }

    public function editMusic($id)
    {
        $user = Auth::user();

        $music = FlunkyDJMusic::find($id);

        return view('speltakken.loodsen.flunkyball.edit_music', ['user' => $user, 'music' => $music]);
    }

    public function saveMusic(Request $request, $id)
    {
        $request->validate([
            'display_title' => 'required|string',
            'music_title' => 'required|string',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,webp',
            'music_file' => 'nullable|mimes:mp3,wav',
            'fade_in' => 'required|integer',
            'fade_out' => 'required|integer',
            'play_type' => 'required|integer',
        ]);

        $music = FlunkyDJMusic::find($id);

        if (isset($request->image)) {
            $newPictureName = time() . '-' . $request->music_title . '.' . $request->image->extension();
            $destinationPath = 'files/loodsen/flunkyball/music_images';
            $request->image->move($destinationPath, $newPictureName);

            $music->image = $newPictureName;
        }

        if (isset($request->music_file)) {
            $newAudioName = time() . '-' . $request->music_title . '.' . $request->music_file->extension();
            $audioDestinationPath = 'files/loodsen/flunkyball/music_files';
            $request->music_file->move($audioDestinationPath, $newAudioName);

            $music->music_file = $newAudioName;
        }


        $music->display_title = $request->input('display_title');
        $music->music_title = $request->input('music_title');
        $music->fade_in = $request->input('fade_in');
        $music->fade_out = $request->input('fade_out');
        $music->play_type = $request->input('play_type');

        $music->save();

        return redirect()->route('loodsen.flunkyball.music')->with('success', 'Muziek succesvol bijgewerkt');
    }

    public function deleteMusic($id)
    {
        $music = FlunkyDJMusic::find($id);

        if($music === null) {
            return redirect()->route('loodsen.flunkyball.music')->with('error', 'Geen muziek gevonden om te verwijderen');
        }

        $music->delete();
        return redirect()->route('loodsen.flunkyball.music')->with('success', 'Muziek verwijderd');

    }

    public function rules()
    {
        $user = Auth::user();

        $all_music = FlunkyDJMusic::paginate(25);

        return view('speltakken.loodsen.flunkyball.rules', ['user' => $user, 'all_music' => $all_music]);
    }
}
