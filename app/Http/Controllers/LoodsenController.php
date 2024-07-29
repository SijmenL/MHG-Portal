<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\Comment;
use App\Models\FlunkyDJMusic;
use App\Models\Log;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use DOMDocument;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoodsenController extends Controller
{
    public function view()
    {
        $user = Auth::user();

        $posts = Post::where('location', 2)
            ->orderBy('created_at', 'desc')
            ->paginate(5);


        return view('speltakken.loodsen.home', ['user' => $user, 'posts' => $posts]);
    }

    /*
     * Forum section, including posts, comments and like handling.
     */

    public function postMessage(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'content' => 'string|max:65535',
        ]);

        if (ForumController::validatePostData($request->input('content'))) {
            $post = Post::create([
                'content' => $request->input('content'),
                'user_id' => Auth::id(),
                'location' => 2,
            ]);

            $users = User::whereHas('roles', function ($query) {
                $query->whereIn('role', ['Loods', 'Loodsen Stamoudste']);
            })->where('id', '!=', $user->id)->pluck('id');

            $notification = new Notification();
            $notification->sendNotification($user->id, $users, 'Heeft een post geplaatst!', '/loodsen/post/' . $post->id, 'loodsen');


            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create post', 'Loodsen', 'Post id: ' . $post->id, '');

            return redirect()->route('loodsen', ['#' . $post->id]);
        } else {
            throw ValidationException::withMessages(['content' => 'Je post kan niet geplaatst worden.']);
        }

    }

    public function viewPost($id)
    {
        $user = Auth::user();

        try {
            $post = Post::with(['comments' => function ($query) {
                $query->withCount('likes')
                    ->orderByDesc('likes_count')
                    ->with(['comments' => function ($query) {
                        $query->orderBy('created_at', 'asc');
                    }]);
            }])->findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'View post', 'Loodsen', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('loodsen')->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->location !== 2) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'View post', 'Loodsen', 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');

            return redirect()->route('loodsen')->with('error', 'Je mag deze post niet bekijken.');
        }

        return view('speltakken.loodsen.post', ['user' => $user, 'post' => $post]);
    }

    public function postComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'string|max:65535',
        ]);

        if (ForumController::validatePostData($request->input('content'))) {

            try {
                $post = Post::findOrFail($id);
            } catch (ModelNotFoundException $exception) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'Post comment', 'Loodsen', 'Comment id: ' . $id, 'Post bestaat niet');

                return redirect()->route('loodsen')->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
            }

            $comment = Comment::create([
                'content' => $request->input('content'),
                'user_id' => Auth::id(),
                'post_id' => $id,
            ]);

            $displayText = trim(mb_substr(strip_tags(html_entity_decode($request->input('content'))), 0, 100));

            $notification = new Notification();
            $notification->sendNotification(Auth::id(), [$post->user_id], 'Heeft een reactie geplaatst: ' . $displayText, '/loodsen/post/' . $post->id . '#' . $comment->id, 'loodsen');


            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create comment', 'Loodsen', 'Comment id: ' . $comment->id, '');

            return redirect()->route('loodsen.post', [$id, '#comments']);
        } else {
            throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden.']);
        }
    }

    public function postReaction(Request $request, $id, $commentId)
    {
        try {
            $post = Post::findOrFail($id);
            $originalComment = Comment::findOrFail($commentId);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Post comment', 'Loodsen', 'Comment id: ' . $id, 'Post bestaat niet');

            return redirect()->route('loodsen')->with('error', 'We hebben deze post of reactie niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }
        $validator = Validator::make($request->all(), [
            'content' => 'required|max:65535',
        ]);

        if (ForumController::validatePostData($request->input('content'))) {
            $comment = Comment::create([
                'content' => $request->input('content'),
                'user_id' => Auth::id(),
                'post_id' => $id,
                'comment_id' => $commentId,
            ]);


            $displayText = trim(mb_substr(strip_tags(html_entity_decode($request->input('content'))), 0, 100));

            $notification = new Notification();
            $notification->sendNotification(Auth::id(), [$originalComment->user_id], 'Heeft op je gereageerd: ' . $displayText, '/loodsen/post/' . $post->id . '#comment-' . $comment->id, 'loodsen');


            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create comment', 'Loodsen', 'Comment id: ' . $comment->id, '');

            return redirect()->route('loodsen.post', [$id, '#comment-' . $comment->id]);
        } else {
            throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden.']);
        }
    }

    public function editPost($id)
    {
        $user = Auth::user();

        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'Loodsen', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('loodsen')->with('error', 'We hebben je post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->location !== 2) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'Loodsen', 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');
            return redirect()->route('loodsen')->with('error', 'Je mag deze post niet bekijken.');
        }

        if ($post->user_id === Auth::id()) {
            return view('speltakken.loodsen.post_edit', ['user' => $user, 'post' => $post]);
        } else {
            return redirect()->route('loodsen')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function storePost(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'Loodsen', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('loodsen')->with('error', 'We hebben je post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->user_id === Auth::id()) {
            $validatedData = $request->validate([
                'content' => 'string|max:65535',
            ]);

            if (ForumController::validatePostData($request->input('content'))) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Edit post', 'Loodsen', 'Post id: ' . $id, '');
                $post->update($validatedData);
            } else {
                $log = new Log();
                $log->createLog(auth()->user()->id, 0, 'Edit post', 'Loodsen', 'Post id: ' . $id, 'Post kon niet bewerkt worden');
                throw ValidationException::withMessages(['content' => 'Je post kon niet bewerkt worden.']);
            }

            return redirect()->route('loodsen.post', $id);
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'Loodsen', 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');
            return redirect()->route('loodsen')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function deletePost($id)
    {
        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete post', 'Loodsen', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('loodsen')->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Loodsen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

            foreach ($post->comments as $comment) {
                $comment->delete();
            }

            foreach ($post->likes as $like) {
                $like->delete();
            }

            $post->delete();
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete post', 'Loodsen', 'Post id: ' . $id, '');

            return redirect()->route('loodsen', ['#posts']);

        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete post', 'Loodsen', 'Post id: ' . $id, 'Gebruiker mag post niet verwijderen.');
            return redirect()->route('loodsen')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    public function deleteComment($id, $postId)
    {
        try {
            $comment = Comment::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete comment', 'Loodsen', 'Comment id: ' . $id, 'Reactie bestaat niet');

            return redirect()->route('loodsen')->with('error', 'We hebben deze reactie niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($comment->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Loodsen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

            $comment->delete();
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete comment', 'Loodsen', 'Comment id: ' . $id, '');

            return redirect()->route('loodsen.post', [$postId, '#comments']);
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete comment', 'Loodsen', 'Comment id: ' . $id, 'Gebruiker mag reactie niet verwijderen.');
            return redirect()->route('loodsen')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    /*
     * End of forum section
     */

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
            ->where('accepted', true)
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Loods');
            })
            ->orderBy('last_name')
            ->paginate(25);

        $user_ids = User::with(['roles' => function ($query) {
            $query->where('role', 'Loods')->orderBy('role', 'asc');
        }])
            ->where('accepted', true)
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Loods');
            })
            ->orderBy('last_name')
            ->pluck('id');

        $selected_role = '';

        return view('speltakken.loodsen.group', ['user' => $user, 'user_ids' => $user_ids, 'roles' => $roles, 'users' => $users, 'search' => $search, 'selected_role' => $selected_role]);
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
            ->where('accepted', true)
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Loods');
            })
            ->orderBy('last_name')
            ->paginate(25);

        $user_ids = User::where(function ($query) use ($search) {
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
            ->where('accepted', true)
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Loods');
            })
            ->orderBy('last_name')
            ->pluck('id');


        $all_roles = Role::orderBy('role')->get();

        return view('speltakken.loodsen.group', ['user' => $user, 'user_ids' => $user_ids, 'roles' => $roles, 'users' => $users, 'search' => $search, 'all_roles' => $all_roles, 'selected_role' => $selected_role]);
    }

    public function groupDetails($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        try {
            $account = User::with(['roles' => function ($query) {
                $query->orderBy('role', 'asc');
            }])
                ->whereHas('roles', function ($query) {
                    $query->where('role', 'Loods');
                })
                ->find($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'View user', 'loodsen', 'Account id: ' . $id, 'Gebruiker bestaat niet');
            return redirect()->route('loodsen')->with('error', 'Dit account bestaat niet.');
        }
        if ($account === null) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'View user', 'loodsen', 'Account id: ' . $id, 'Gebruiker bestaat niet');
            return redirect()->route('loodsen')->with('error', 'Dit account bestaat niet.');
        }

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'View account', 'Loodsen', $account->name . ' ' . $account->infix . ' ' . $account->last_name, '');


        return view('speltakken.loodsen.group_details', ['user' => $user, 'roles' => $roles, 'account' => $account]);
    }

    public function exportData(Request $request)
    {
        // Retrieve the filtered user data from the request
        $users = json_decode($request->input('user_ids'));

        $type = 'loodsen';

        // Export data to Excel
        $export = new UsersExport($users, $type);
        return $export->export();
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
            return redirect()->route('loodsen.flunkyball.admin')->with("error", "Er is iets mis gegaan, bestanden zijn niet verplaatst.");
        }
    }

    public function editMusic($id)
    {
        $user = Auth::user();

        try {
            $music = FlunkyDJMusic::find($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Save music', 'Loodsen', 'Music id: ' . $id, 'Muziek bestaat niet');

            return redirect()->route('loodsen')->with('error', 'Deze muziek bestaat niet.');
        }
        if ($music === null) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Save music', 'Loodsen', 'Music id: ' . $id, 'Muziek bestaat niet');

            return redirect()->route('loodsen')->with('error', 'Deze muziek bestaat niet.');
        }

        return view('speltakken.loodsen.flunkyball.edit_music', ['user' => $user, 'music' => $music]);
    }

    public function saveMusic(Request $request, $id)
    {
        try {
            $music = FlunkyDJMusic::find($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Save music', 'Loodsen', 'Music id: ' . $id, 'Muziek bestaat niet');

            return redirect()->route('loodsen')->with('error', 'Deze muziek bestaat niet.');
        }
        if ($music === null) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Save music', 'Loodsen', 'Music id: ' . $id, 'Muziek bestaat niet');

            return redirect()->route('dashboard')->with('error', 'Deze muziek bestaat niet.');
        }

        $request->validate([
            'display_title' => 'required|string',
            'music_title' => 'required|string',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,webp',
            'music_file' => 'nullable|mimes:mp3,wav',
            'fade_in' => 'required|integer',
            'fade_out' => 'required|integer',
            'play_type' => 'required|integer',
        ]);


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
        try {
            $music = FlunkyDJMusic::find($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete music', 'Loodsen', 'Music id: ' . $id, 'Muziek bestaat niet');

            return redirect()->route('loodsen')->with('error', 'Deze muziek bestaat niet.');
        }

        if ($music === null) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete music', 'Loodsen', 'Music id: ' . $id, 'Muziek bestaat niet');

            return redirect()->route('loodsen')->with('error', 'Deze muziek bestaat niet.');
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
