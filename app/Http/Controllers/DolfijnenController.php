<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Models\Comment;
use App\Models\Like;
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

class DolfijnenController extends Controller
{
    public function view()
    {
        $user = Auth::user();

        $posts = Post::where('location', 0)
            ->orderBy('created_at', 'desc') // or 'updated_at' if you prefer
            ->paginate(5);


        return view('speltakken.dolfijnen.home', ['user' => $user, 'posts' => $posts]);
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
                'location' => 0,
            ]);

            $users = User::whereHas('roles', function ($query) {
                $query->whereIn('role', ['Dolfijn', 'Dolfijnen Leiding']);
            })->where('id', '!=', $user->id)
                ->with('parents') // Eager load parents relationship
                ->get();

            $userIds = $users->pluck('id');

            $parentIds = $users->filter(function ($user) {

                return $user->roles->contains('role', 'Dolfijn');
            })->flatMap(function ($user) {
                return $user->parents->pluck('id');
            });

            $notificationRecipients = $userIds->merge($parentIds)->unique();


            $notification = new Notification();
            $notification->sendNotification($user->id, $notificationRecipients, 'heeft een post geplaatst!', '/dolfijnen/post/' . $post->id, 'dolfijnen', 'new_post', $post->id);


            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create post', 'dolfijnen', 'Post id: ' . $post->id, '');

            return redirect()->route('dolfijnen', ['#' . $post->id]);
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
            $log->createLog(auth()->user()->id, 1, 'View post', 'dolfijnen', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('dolfijnen')->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->location !== 0) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'View post', 'dolfijnen', 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');

            return redirect()->route('dolfijnen')->with('error', 'Je mag deze post niet bekijken.');
        }

        return view('speltakken.dolfijnen.post', ['user' => $user, 'post' => $post]);
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
                $log->createLog(auth()->user()->id, 1, 'Post comment', 'dolfijnen', 'Comment id: ' . $id, 'Post bestaat niet');

                return redirect()->route('dolfijnen')->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
            }

            $comment = Comment::create([
                'content' => $request->input('content'),
                'user_id' => Auth::id(),
                'post_id' => $id,
            ]);

            $displayText = trim(mb_substr(strip_tags(html_entity_decode($request->input('content'))), 0, 100));

            $notification = new Notification();
            $notification->sendNotification(Auth::id(), [$post->user_id], 'Heeft een reactie geplaatst: ' . $displayText, '/dolfijnen/post/' . $post->id . '#' . $comment->id, 'dolfijnen', 'new_comment', $comment->id);


            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create comment', 'dolfijnen', 'Comment id: ' . $comment->id, '');

            return redirect()->route('dolfijnen.post', [$id, '#comments']);
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
            $log->createLog(auth()->user()->id, 1, 'Post comment', 'dolfijnen', 'Comment id: ' . $id, 'Post bestaat niet');

            return redirect()->route('dolfijnen')->with('error', 'We hebben deze post of reactie niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
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
            $notification->sendNotification(Auth::id(), [$originalComment->user_id], 'Heeft op je gereageerd: ' . $displayText, '/dolfijnen/post/' . $post->id . '#comment-' . $comment->id, 'dolfijnen', 'new_reaction_comment', $comment->id);


            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create comment', 'dolfijnen', 'Comment id: ' . $comment->id, '');

            return redirect()->route('dolfijnen.post', [$id, '#comment-' . $comment->id]);
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
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'dolfijnen', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('dolfijnen')->with('error', 'We hebben je post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->location !== 0) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'dolfijnen', 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');
            return redirect()->route('dolfijnen')->with('error', 'Je mag deze post niet bekijken.');
        }

        if ($post->user_id === Auth::id()) {
            return view('speltakken.dolfijnen.post_edit', ['user' => $user, 'post' => $post]);
        } else {
            return redirect()->route('dolfijnen')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function storePost(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'dolfijnen', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('dolfijnen')->with('error', 'We hebben je post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->user_id === Auth::id()) {
            $validatedData = $request->validate([
                'content' => 'string|max:65535',
            ]);

            if (ForumController::validatePostData($request->input('content'))) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Edit post', 'dolfijnen', 'Post id: ' . $id, '');
                $post->update($validatedData);
            } else {
                $log = new Log();
                $log->createLog(auth()->user()->id, 0, 'Edit post', 'dolfijnen', 'Post id: ' . $id, 'Post kon niet bewerkt worden');
                throw ValidationException::withMessages(['content' => 'Je post kon niet bewerkt worden.']);
            }

            return redirect()->route('dolfijnen.post', $id);
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'dolfijnen', 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');
            return redirect()->route('dolfijnen')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function deletePost($id)
    {
        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete post', 'dolfijnen', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('dolfijnen')->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->user_id === Auth::id() || auth()->user()->roles->contains('role', 'dolfijnen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

            foreach ($post->comments as $comment) {
                $comment->delete();
            }

            foreach ($post->likes as $like) {
                $like->delete();
            }

            $post->delete();
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete post', 'dolfijnen', 'Post id: ' . $id, '');

            return redirect()->route('dolfijnen', ['#posts']);

        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete post', 'dolfijnen', 'Post id: ' . $id, 'Gebruiker mag post niet verwijderen.');
            return redirect()->route('dolfijnen')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    public function deleteComment($id, $postId)
    {
        try {
            $comment = Comment::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete comment', 'dolfijnen', 'Comment id: ' . $id, 'Reactie bestaat niet');

            return redirect()->route('dolfijnen')->with('error', 'We hebben deze reactie niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($comment->user_id === Auth::id() || auth()->user()->roles->contains('role', 'dolfijnen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

            $comment->delete();
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete comment', 'dolfijnen', 'Comment id: ' . $id, '');

            return redirect()->route('dolfijnen.post', [$postId, '#comments']);
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete comment', 'dolfijnen', 'Comment id: ' . $id, 'Gebruiker mag reactie niet verwijderen.');
            return redirect()->route('dolfijnen')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    /*
     * End of forum section
     */

    public function leiding()
    {
        $user = Auth::user();

        $hoofdleiding = User::whereHas('roles', function ($query) {
            $query->where('role', 'Dolfijnen Hoofdleiding');
        })->get();

        $penningmeester = User::whereHas('roles', function ($query) {
            $query->where('role', 'Dolfijnen Penningmeester');
        })->get();

        $other_leiding = User::whereHas('roles', function ($query) {
            $query->where('role', 'Dolfijnen Leiding');
        })->whereDoesntHave('roles', function ($query) {
            $query->whereIn('role', ['Dolfijnen Hoofdleiding', 'Dolfijnen Penningmeester']);
        })->get();

        $leiding = $hoofdleiding->merge($penningmeester)->merge($other_leiding);

        return view('speltakken.dolfijnen.leiding', ['user' => $user, 'leiding' => $leiding]);
    }

    public function group()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        // Retrieve search and selected role from the GET request
        $search = request('search');
        $selected_role = request('role', 'Dolfijn'); // Default to 'Dolfijn'

        // Query for users based on selected role dynamically
        $usersQuery = User::with('roles')
            ->where('accepted', true);

        // If the selected role is 'Ouders', find users who are parents of users with the 'Dolfijn' role
        if ($selected_role === 'Ouders') {
            $usersQuery->whereHas('children', function ($query) {
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('role', 'Dolfijn');
                });
            });
        } else {
            $selected_role = 'Dolfijn';
            // Otherwise, find users based on their selected role
            $usersQuery->whereHas('roles', function ($query) use ($selected_role) {
                $query->where('role', $selected_role); // Flexible match for roles
            });
        }

        // Apply search filters if there's a search query
        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
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
            });
        }

        // Get paginated users
        $users = $usersQuery->orderBy('last_name')->paginate(25);

        // Collect user IDs
        $user_ids = $users->pluck('id');

        // Pass data to the view
        return view('speltakken.dolfijnen.group', [
            'user' => $user,
            'roles' => $roles,
            'users' => $users,
            'user_ids' => $user_ids,
            'search' => $search,
            'selected_role' => $selected_role,
        ]);
    }

    public function exportData(Request $request)
    {
        // Retrieve the filtered user data from the request
        $users = json_decode($request->input('user_ids'));
        $export_type = $request->input('type');

        if ($export_type === 'Dolfijnen') {
            $type = 'dolfijnen';
        } else {
            $type = 'dolfijnen-ouders';
        }

        // Export data to Excel
        $export = new UsersExport($users, $type);
        return $export->export();
    }

    public function groupDetails($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        try {
            // Find the user by their ID, along with their roles, children, and parents
            $account = User::with([
                'roles' => function ($query) {
                    $query->orderBy('role', 'asc');
                },
                'children.roles', // Load roles for children
                'parents.roles'   // Load roles for parents
            ])->findOrFail($id);  // Ensure we always find the user or fail

        } catch (ModelNotFoundException $exception) {
            // Log and return if the user does not exist
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'View user', 'dolfijnen', 'Account id: ' . $id, 'Gebruiker bestaat niet');
            return redirect()->route('dolfijnen')->with('error', 'Dit account bestaat niet.');
        }

        // Check if the user or their parents/children have the role 'Dolfijn'
        $hasDolfijnenRole = $account->roles->contains('role', 'Dolfijn') ||
            $account->children->pluck('roles')->flatten()->contains('role', 'Dolfijn') ||
            $account->parents->pluck('roles')->flatten()->contains('role', 'Dolfijn');

        // Log if no 'Dolfijn' role is found
        if (!$hasDolfijnenRole) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'View account', 'Dolfijnen', $account->name . ' ' . $account->infix . ' ' . $account->last_name, 'Geen Dolfijn rol gevonden');
            return redirect()->route('dolfijnen')->with('error', 'Je hebt geen toegang tot dit account.');
        }

        // Log successful view
        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'View account', 'Dolfijnen', $account->name . ' ' . $account->infix . ' ' . $account->last_name, '');

        // Return the view with the necessary data
        return view('speltakken.dolfijnen.group_details', ['user' => $user, 'roles' => $roles, 'account' => $account]);
    }
}
