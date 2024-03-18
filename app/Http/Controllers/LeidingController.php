<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Log;
use App\Models\Post;
use App\Models\User;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LeidingController extends Controller
{
    public function view()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $posts = Post::where('location', 4)
            ->orderBy('created_at', 'desc') // or 'updated_at' if you prefer
            ->paginate(5);


        return view('leiding.home', ['user' => $user, 'posts' => $posts, 'roles' => $roles]);
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
                'location' => 4,
            ]);

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create post', 'Leiding', $post->id, '');

            return redirect()->route('leiding', ['#' . $post->id]);
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

        if ($post->location !== 4) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'View post', 'Leiding', $id, 'Gebruiker had geen toegang tot de post');

            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet bekijken.');
        }

        return view('leiding.post', ['user' => $user, 'post' => $post]);
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
            $log->createLog(auth()->user()->id, 2, 'Create comment', 'Leiding', $comment->id, '');

            return redirect()->route('leiding.post', [$id, '#comments']);
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
            $log->createLog(auth()->user()->id, 2, 'Create comment', 'Leiding', $comment->id, '');

            return redirect()->route('leiding.post', [$id, '#comment-' . $comment->id]);
        } else {
            throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden.']);
        }
    }

    public function editPost($id)
    {
        $user = Auth::user();

        $post = Post::findOrFail($id);

        if ($post->location !== 4) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'Leiding', $id, 'Gebruiker had geen toegang tot de post');
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet bekijken.');
        }

        if ($post->user_id === Auth::id()) {
            return view('leiding.post_edit', ['user' => $user, 'post' => $post]);
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
                $log->createLog(auth()->user()->id, 2, 'Edit post', 'Leiding', $id, '');
                $post->update($validatedData);
            } else {
                $log = new Log();
                $log->createLog(auth()->user()->id, 0, 'Edit post', 'Leiding', $id, 'Post kon niet bewerkt worden');
                throw ValidationException::withMessages(['content' => 'Je post kon niet bewerkt worden.']);
            }

            return redirect()->route('leiding.post', $id);
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'Leiding', $id, 'Gebruiker had geen toegang tot de post');
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur')) {

            foreach ($post->comments as $comment) {
                $comment->delete();
            }

            foreach ($post->likes as $like) {
                $like->delete();
            }

            $post->delete();
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete post', 'Leiding', $id, '');

            return redirect()->route('leiding', ['#posts']);

        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete post', 'Leiding', $id, 'Gebruiker mag post niet verwijderen.');
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    public function deleteComment($id, $postId)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur')) {

            $comment->delete();
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete comment', 'Leiding', $id, '');

            return redirect()->route('leiding.post', [$postId, '#comments']);
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete post', 'Leiding', $id, 'Gebruiker mag reactie niet verwijderen.');
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    /*
     * End of forum section
     */

    public function leiding()
    {
        $user = Auth::user();

        $bestuur = User::whereHas('roles', function ($query) {
            $query->where('role', 'Bestuur');
        })->get();

        $dolfijnen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Dolfijnen Leiding');
        })->get();

        $zeeverkenners = User::whereHas('roles', function ($query) {
            $query->where('role', 'Zeeverkenners Leiding');
        })->get();

        $stamoudste = User::whereHas('roles', function ($query) {
            $query->where('role', 'Loodsen Stamoudste');
        })->get();

        $penningmeester = User::whereHas('roles', function ($query) {
            $query->where('role', 'Loodsen Penningmeester');
        })->get();

        $loodsen = $stamoudste->merge($penningmeester);

        $afterloodsen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Afterloodsen Organisator');
        })->get();

        $ouderraad = User::whereHas('roles', function ($query) {
            $query->where('role', 'Ouderraad');
        })->get();

        $admin = User::whereHas('roles', function ($query) {
            $query->where('role', 'Administratie');
        })->get();

        $vrijwilliger = User::whereHas('roles', function ($query) {
            $query->where('role', 'Vrijwilliger');
        })->get();

        return view('leiding.leiding', ['user' => $user, 'bestuur' => $bestuur, 'dolfijnen' => $dolfijnen, 'zeeverkenners' => $zeeverkenners, 'loodsen' => $loodsen, 'afterloodsen' => $afterloodsen, 'ouderraad' => $ouderraad, 'admin' => $admin, 'vrijwilliger' => $vrijwilliger]);
    }
}
