<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNotificationSettings;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SettingsController extends Controller
{

    public function account()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        return view('settings.settings', ['user' => $user, 'roles' => $roles]);
    }

    public function editAccount()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        return view('settings.account', ['user' => $user, 'roles' => $roles]);
    }

    public function editAccountSave(Request $request)
    {
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . Auth::user()->id,
            'sex' => 'string',
            'infix' => 'nullable|string',
            'last_name' => 'string',
            'birth_date' => 'date',
            'street' => 'string',
            'postal_code' => 'string',
            'city' => 'string',
            'phone' => 'string',
            'avg' => 'bool',
            'profile_picture' => 'nullable|mimes:jpeg,png,jpg,gif,webp|max:6000',
        ]);

        $user = Auth::user();

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

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Edit account', 'Settings', '', '');


        return redirect()->route('settings.account.edit')->with('success', 'Account succesvol bijgewerkt');
    }


    public function changePassword()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        return view('settings.change_password', ['user' => $user, 'roles' => $roles]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);


        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return redirect()->back()->withErrors(['old_password' => 'Wachtwoord klopt niet']);
        }


        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Edit password', 'Settings', '', '');


        return back()->with("success", "Wachtwoord succesvol opgeslagen!");
    }


    public function notifications()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $notification_settings = UserNotificationSettings::all()->where('user_id', $user->id)->pluck('on_status', 'type')->toArray();

        return view('settings.edit_notifications', compact('user', 'roles', 'notification_settings'));
    }

    public function notificationsSave(Request $request)
    {
        $request->validate([
            'hidden_form_field' => 'required',
        ]);

        // check if the notification setting already exists
        $notification_setting = UserNotificationSettings::where('user_id', Auth::user()->id)->where('type', $request->hidden_form_field)->first();

        // if exists, delete
        if ($notification_setting) {
            $notification_setting->delete();
            return redirect()->route('settings.edit-notifications')->with("success", "Notificatie instelling succesvol opgeslagen.");
        }

        UserNotificationSettings::create([
            'user_id' => Auth::user()->id,
            'type' => $request->hidden_form_field,
            'on_status' => 0
        ]);

        return redirect()->route('settings.edit-notifications')->with("success", "Notificatie instelling succesvol opgeslagen.");
    }

    public function parent()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        return view('settings.settings_parent', ['user' => $user, 'roles' => $roles]);
    }

    public function linkParent()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        return view('settings.link_parent', ['user' => $user, 'roles' => $roles]);
    }

    public function linkParentStore(Request $request)
    {
        $user = Auth::user();
        $parent = null;

        $validator = Validator::make($request->all(), [
            'parent_email' => ['required', 'email', function ($attribute, $value, $fail) use ($request, $user, &$parent) {
                $parent = User::where('email', $value)->first();

                if (!$parent) {
                    $fail('Er bestaat geen account met dit email adres.');
                } elseif ($user->id === $parent->id) {
                    $fail('Je kan niet gekoppeld zijn aan jezelf.');
                } elseif ($user->parents->contains($parent->id)) {
                    $fail('Er is al een ouder koppeling met dit account.');
                }
            }],
        ]);

        if ($validator->fails()) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Link parent', 'settings', '', 'Ouder niet gelinkt');

            return redirect()->route('settings.link-parent')->withErrors($validator)->withInput();
        }

        return redirect()->route('settings.link-parent')->with([
            'continue' => [
                'id' => $parent->id,
                'name' => $parent->name,
                'infix' => $parent->infix,
                'last_name' => $parent->last_name,
            ],
        ]);
    }


    public function confirmParent($id)
    {
        $user = Auth::user();

        try {
            $parent = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Link parent', 'settings', 'Account id: ' . $id, 'Ouder bestaat niet');

            return redirect()->route('settings')->with('error', 'We kunnen deze ouder niet vinden.');
        }

        $user->parents()->attach($parent);

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Link parent', 'Settings', $parent->name . ' ' . $parent->infix . ' ' . $parent->last_name, 'Bestaand ouder account gekoppeld');

        $notification = new Notification();
        $notification->sendNotification(auth()->user()->id, [$id], 'Heeft je als ouder toegevoegd.', '', '', 'add_parent');

        return redirect()->route('settings.parent')->with("success", "Ouderkoppeling succesvol!");
    }


    public function createAccount()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        return view('settings.create_parent', ['user' => $user, 'roles' => $roles]);
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
            'sex' => 'string',
            'infix' => 'nullable|string',
            'last_name' => 'string',
            'birth_date' => 'date',
            'street' => 'string',
            'postal_code' => 'string',
            'city' => 'string',
            'phone' => 'string',
            'avg' => 'bool',
            'profile_picture' => 'nullable|mimes:jpeg,png,jpg,gif,webp|max:6000',
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
            $parent = User::create([
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
            ]);

            if (isset($request->profile_picture)) {
                $parent->profile_picture = $newPictureName;
            }

            $parent->accepted = true;
            $parent->save();

            $user->parents()->attach($parent);

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Link parent', 'Settings', $parent->name . ' ' . $parent->infix . ' ' . $parent->last_name, 'Nieuw ouder account aangemaakt');

            $notification = new Notification();
            $notification->sendNotification(auth()->user()->id, [$parent->id], 'Heeft je als ouder toegevoegd.', '', '', 'add_parent');

            return redirect()->route('settings.parent')->with("success", "Ouder koppeling succesvol!");

        }
    }

    public function removeParentLink()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        if ($user->parents()->count() > 0) {
            return view('settings.delete_parent', ['user' => $user, 'roles' => $roles]);
        } else {
            return redirect()->route('settings')->with("error", "Geen ouders");
        }
    }

    public function removeParentLinkId($id)
    {
        try {
            $parent = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Link parent', 'settings', 'Account id: ' . $id, 'Ouder bestaat niet');

            return redirect()->route('settings')->with('error', 'We kunnen deze ouder niet vinden.');
        }

        return redirect()->route('settings.remove-parent-link', ['id' => $parent->id])->with([
            'continue' => [
                'id' => $parent->id,
                'name' => $parent->name,
                'infix' => $parent->infix,
                'last_name' => $parent->last_name,
            ],
        ]);
    }

    public function removeParentLinkConfirm($id)
    {
        $user = Auth::user();

        try {
            $parent = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Link parent', 'settings', 'Account id: ' . $id, 'Ouder bestaat niet');

            return redirect()->route('settings')->with('error', 'We kunnen deze ouder niet vinden.');
        }

        $user->parents()->detach($parent->id);

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Remove parent', 'Settings', $parent->name . ' ' . $parent->infix . ' ' . $parent->last_name, '');

        $notification = new Notification();
        $notification->sendNotification(auth()->user()->id, [$id], 'heeft de ouder-kind koppeling verbroken.', '', null, 'delete_parent');

        return redirect()->route('settings.remove-parent-link')->with("success", "Ouder-kind koppeling succesvol verbroken");
    }

    public function removeChildLink()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        if ($user->children()->count() > 0) {
            return view('settings.delete_child', ['user' => $user, 'roles' => $roles]);
        } else {
            return redirect()->route('settings')->with("error", "Geen kinderen");
        }
    }

    public function removeChildLinkId($id)
    {
        try {
            $child = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Link child', 'settings', 'Account id: ' . $id, 'Kind bestaat niet');

            return redirect()->route('settings')->with('error', 'We kunnen dit kind niet vinden.');
        }

        return redirect()->route('settings.remove-child-link', ['id' => $child->id])->with([
            'continue' => [
                'id' => $child->id,
                'name' => $child->name,
                'infix' => $child->infix,
                'last_name' => $child->last_name,
            ],
        ]);
    }

    public function removeChildLinkConfirm($id)
    {
        $user = Auth::user();

        try {
            $child = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Link child', 'settings', 'Account id: ' . $id, 'Kind bestaat niet');

            return redirect()->route('settings')->with('error', 'We kunnen dit kind niet vinden.');
        }

        $user->children()->detach($child->id);

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'Remove child', 'Settings', $child->name . ' ' . $child->infix . ' ' . $child->last_name, '');

        $notification = new Notification();
        $notification->sendNotification(auth()->user()->id, [$id], 'heeft de ouder-kind koppeling verbroken.', '', null, 'delete_child');

        return redirect()->route('settings.remove-child-link')->with("success", "Kind succesvol ontkoppeld!");
    }
}

