<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Log;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AfterloodsenController extends Controller
{
    public function view()
    {
        $user = Auth::user();
        $posts = Post::where('location', 3)
            ->orderBy('created_at', 'desc') // or 'updated_at' if you prefer
            ->paginate(5);


        return view('speltakken.afterloodsen.home', ['user' => $user, 'posts' => $posts]);
    }

    /*
     * Forum section, including posts, comments and like handling.
     */

    public function postMessage(Request $request)
    {
        $request->validate([
            'content' => 'string|max:65535',
        ]);

        if (ForumController::validatePostData($request->input('content'))) {
            $post = Post::create([
                'content' => $request->input('content'),
                'user_id' => Auth::id(),
                'location' => 3,
            ]);

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create post', 'Afterloodsen', $post->id, '');

            return redirect()->route('afterloodsen', ['#' . $post->id]);
        } else {
            throw ValidationException::withMessages(['content' => 'Je post kan niet geplaatst worden.']);
        }

    }

    public function viewPost($id)
    {
        $user = Auth::user();

        $post = Post::with(['comments' => function ($query) {
            $query->withCount('likes') // Count the number of likes for each comment
            ->orderByDesc('likes_count') // Sort top-level comments by the number of likes (descending)
            ->with(['comments' => function ($query) {
                $query->orderBy('created_at', 'asc'); // Sort nested comments by oldest first
            }]);
        }])->findOrFail($id);

        if ($post->location !== 3) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'View post', 'Afterloodsen', $id, 'Gebruiker had geen toegang tot de post');

            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet bekijken.');
        }

        return view('speltakken.afterloodsen.post', ['user' => $user, 'post' => $post]);
    }

    public function postComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'string|max:65535',
        ]);

        if (ForumController::validatePostData($request->input('content'))) {

            $comment = Comment::create([
                'content' => $request->input('content'),
                'user_id' => Auth::id(),
                'post_id' => $id,
            ]);

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create comment', 'Afterloodsen', $comment->id, '');

            return redirect()->route('afterloodsen.post', [$id, '#comments']);
        } else {
            throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden.']);
        }
    }

    public function postReaction(Request $request, $id, $commentId)
    {
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

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create comment', 'Afterloodsen', $comment->id, '');

            return redirect()->route('afterloodsen.post', [$id, '#comment-' . $comment->id]);
        } else {
            throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden.']);
        }
    }

    public function editPost($id)
    {
        $user = Auth::user();

        $post = Post::findOrFail($id);

        if ($post->location !== 3) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'Afterloodsen', $id, 'Gebruiker had geen toegang tot de post');
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet bekijken.');
        }

        if ($post->user_id === Auth::id()) {
            return view('speltakken.afterloodsen.post_edit', ['user' => $user, 'post' => $post]);
        } else {
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function storePost(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id === Auth::id()) {
            $validatedData = $request->validate([
                'content' => 'string|max:65535',
            ]);

            if (ForumController::validatePostData($request->input('content'))) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Edit post', 'Afterloodsen', $id, '');
                $post->update($validatedData);
            } else {
                $log = new Log();
                $log->createLog(auth()->user()->id, 0, 'Edit post', 'Afterloodsen', $id, 'Post kon niet bewerkt worden');
                throw ValidationException::withMessages(['content' => 'Je post kon niet bewerkt worden.']);
            }

            return redirect()->route('afterloodsen.post', $id);
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'Afterloodsen', $id, 'Gebruiker had geen toegang tot de post');
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Afterloodsen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

            foreach ($post->comments as $comment) {
                $comment->delete();
            }

            foreach ($post->likes as $like) {
                $like->delete();
            }

            $post->delete();
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete post', 'Afterloodsen', $id, '');

            return redirect()->route('afterloodsen', ['#posts']);

        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete post', 'Afterloodsen', $id, 'Gebruiker mag post niet verwijderen.');
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    public function deleteComment($id, $postId)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Afterloodsen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

            $comment->delete();
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete comment', 'Afterloodsen', $id, '');

            return redirect()->route('afterloodsen.post', [$postId, '#comments']);
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete post', 'Afterloodsen', $id, 'Gebruiker mag reactie niet verwijderen.');
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    /*
     * End of forum section
     */

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

        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'View account', 'Afterloodsen', $id, '');


        return view('speltakken.afterloodsen.group_details', ['user' => $user, 'roles' => $roles, 'account' => $account]);
    }
}
