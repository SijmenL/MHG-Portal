<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Log;
use App\Models\News;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use DOMDocument;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
        $user = Auth::user();
        $request->validate([
            'content' => 'string|max:65535',
        ]);

        if (ForumController::validatePostData($request->input('content'))) {
            $post = Post::create([
                'content' => $request->input('content'),
                'user_id' => Auth::id(),
                'location' => 4,
            ]);

            $users = User::whereHas('roles', function ($query) {
                $query->whereIn('role', ['Dolfijnen Leiding','Zeeverkenners Leiding','Loodsen Stamoudste','Afterloodsen Organisator','Vrijwilliger','Administratie','Bestuur','Ouderraad','Praktijkbegeleider','Loodsen Mentor']);
            })->where('id', '!=', $user->id)->pluck('id');

            $notification = new Notification();
            $notification->sendNotification($user->id, $users, 'Heeft een post geplaatst!', '/leiding/post/' . $post->id, 'leiding', 'new_post', $post->id);


            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create post', 'leiding', 'Post id: ' . $post->id, '');

            return redirect()->route('leiding', ['#' . $post->id]);
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
            $log->createLog(auth()->user()->id, 1, 'View post', 'leiding', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('leiding')->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->location !== 4) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'View post', 'leiding', 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');

            return redirect()->route('leiding')->with('error', 'Je mag deze post niet bekijken.');
        }

        return view('leiding.post', ['user' => $user, 'post' => $post]);
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
                $log->createLog(auth()->user()->id, 1, 'Post comment', 'leiding', 'Comment id: ' . $id, 'Post bestaat niet');

                return redirect()->route('leiding')->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
            }

            $comment = Comment::create([
                'content' => $request->input('content'),
                'user_id' => Auth::id(),
                'post_id' => $id,
            ]);

            $displayText = trim(mb_substr(strip_tags(html_entity_decode($request->input('content'))), 0, 100));

            $notification = new Notification();
            $notification->sendNotification(Auth::id(), [$post->user_id], 'Heeft een reactie geplaatst: ' . $displayText, '/leiding/post/' . $post->id . '#' . $comment->id, 'leiding', 'new_comment', $post->id);


            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create comment', 'leiding', 'Comment id: ' . $comment->id, '');

            return redirect()->route('leiding.post', [$id, '#comments']);
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
            $log->createLog(auth()->user()->id, 1, 'Post comment', 'leiding', 'Comment id: ' . $id, 'Post bestaat niet');

            return redirect()->route('leiding')->with('error', 'We hebben deze post of reactie niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
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
            $notification->sendNotification(Auth::id(), [$originalComment->user_id], 'Heeft op je gereageerd: ' . $displayText, '/leiding/post/' . $post->id . '#comment-' . $comment->id, 'leiding', 'new_reaction_comment', $post->id);


            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Create comment', 'leiding', 'Comment id: ' . $comment->id, '');

            return redirect()->route('leiding.post', [$id, '#comment-' . $comment->id]);
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
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'leiding', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('leiding')->with('error', 'We hebben je post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->location !== 4) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'leiding', 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');
            return redirect()->route('leiding')->with('error', 'Je mag deze post niet bekijken.');
        }

        if ($post->user_id === Auth::id()) {
            return view('leiding.post_edit', ['user' => $user, 'post' => $post]);
        } else {
            return redirect()->route('leiding')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function storePost(Request $request, $id)
    {
        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'leiding', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('leiding')->with('error', 'We hebben je post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->user_id === Auth::id()) {
            $validatedData = $request->validate([
                'content' => 'string|max:65535',
            ]);

            if (ForumController::validatePostData($request->input('content'))) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Edit post', 'leiding', 'Post id: ' . $id, '');
                $post->update($validatedData);
            } else {
                $log = new Log();
                $log->createLog(auth()->user()->id, 0, 'Edit post', 'leiding', 'Post id: ' . $id, 'Post kon niet bewerkt worden');
                throw ValidationException::withMessages(['content' => 'Je post kon niet bewerkt worden.']);
            }

            return redirect()->route('leiding.post', $id);
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Edit post', 'leiding', 'Post id: ' . $id, 'Gebruiker had geen toegang tot de post');
            return redirect()->route('leiding')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function deletePost($id)
    {
        try {
            $post = Post::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete post', 'leiding', 'Post id: ' . $id, 'Post bestaat niet');

            return redirect()->route('leiding')->with('error', 'We hebben deze post niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($post->user_id === Auth::id() || auth()->user()->roles->contains('role', 'leiding Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

            foreach ($post->comments as $comment) {
                $comment->delete();
            }

            foreach ($post->likes as $like) {
                $like->delete();
            }

            $post->delete();
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete post', 'leiding', 'Post id: ' . $id, '');

            return redirect()->route('leiding', ['#posts']);

        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete post', 'leiding', 'Post id: ' . $id, 'Gebruiker mag post niet verwijderen.');
            return redirect()->route('leiding')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    public function deleteComment($id, $postId)
    {
        try {
            $comment = Comment::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete comment', 'leiding', 'Comment id: ' . $id, 'Reactie bestaat niet');

            return redirect()->route('leiding')->with('error', 'We hebben deze reactie niet gevonden, waarschijnlijk is deze verplaatst of verwijderd!');
        }

        if ($comment->user_id === Auth::id() || auth()->user()->roles->contains('role', 'leiding Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

            $comment->delete();
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Delete comment', 'leiding', 'Comment id: ' . $id, '');

            return redirect()->route('leiding.post', [$postId, '#comments']);
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'Delete comment', 'leiding', 'Comment id: ' . $id, 'Gebruiker mag reactie niet verwijderen.');
            return redirect()->route('leiding')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    /*
     * End of forum section
     */

    public function leiding()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $voorzitter = User::whereHas('roles', function ($query) {
            $query->where('role', 'Voorzitter');
        })->get();
        $vicevoorzitter = User::whereHas('roles', function ($query) {
            $query->where('role', 'Vice-voorzitter');
        })->get();
        $secretaris = User::whereHas('roles', function ($query) {
            $query->where('role', 'Secretaris');
        })->get();
        $penningmeester = User::whereHas('roles', function ($query) {
            $query->where('role', 'Penningmeester');
        })->get();
        $other_bestuur = User::whereHas('roles', function ($query) {
            $query->where('role', 'Bestuur');
        })->whereDoesntHave('roles', function ($query) {
            $query->whereIn('role', ['Voorzitter', 'Vice-voorzitter', 'Secretaris', 'Penningmeester']);
        })->get();
        $bestuur = $voorzitter->merge($vicevoorzitter)->merge($secretaris)->merge($penningmeester)->merge($other_bestuur);


        $hoofdleiding_dolfijnen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Dolfijnen Hoofdleiding');
        })->get();
        $penningmeester_dolfijnen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Dolfijnen Penningmeester');
        })->get();
        $other_leiding_dolfijnen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Dolfijnen Leiding');
        })->whereDoesntHave('roles', function ($query) {
            $query->whereIn('role', ['Dolfijnen Hoofdleiding', 'Dolfijnen Penningmeester']);
        })->get();
        $dolfijnen = $hoofdleiding_dolfijnen->merge($penningmeester_dolfijnen)->merge($other_leiding_dolfijnen);


        $hoofdleiding_zeeverkenners = User::whereHas('roles', function ($query) {
            $query->where('role', 'Zeeverkenners Hoofdleiding');
        })->get();
        $penningmeester_zeeverkenners = User::whereHas('roles', function ($query) {
            $query->where('role', 'Zeeverkenners Penningmeester');
        })->get();
        $other_leiding_zeeverkenners = User::whereHas('roles', function ($query) {
            $query->where('role', 'Zeeverkenners Leiding');
        })->whereDoesntHave('roles', function ($query) {
            $query->whereIn('role', ['Zeeverkenners Hoofdleiding', 'Zeeverkenners Penningmeester']);
        })->get();
        $zeeverkenners = $hoofdleiding_zeeverkenners->merge($penningmeester_zeeverkenners)->merge($other_leiding_zeeverkenners);

        $mentor_loodsen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Loodsen Mentor');
        })->get();
        $stamoudste_loodsen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Loodsen Stamoudste');
        })->get();
        $penningmeester_loodsen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Loodsen Penningmeester');
        })->get();
        $loodsen = $mentor_loodsen->merge($stamoudste_loodsen)->merge($penningmeester_loodsen);

        $hoofdleiding_afterloodsen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Afterloodsen Voorzitter');
        })->get();
        $penningmeester_afterloodsen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Afterloodsen Penningmeester');
        })->get();
        $other_leiding_afterloodsen = User::whereHas('roles', function ($query) {
            $query->where('role', 'Afterloodsen Organisator');
        })->whereDoesntHave('roles', function ($query) {
            $query->whereIn('role', ['Afterloodsen Voorzitter', 'Afterloodsen Penningmeester']);
        })->get();
        $afterloodsen = $hoofdleiding_afterloodsen->merge($penningmeester_afterloodsen)->merge($other_leiding_afterloodsen);

        $ouderraad = User::whereHas('roles', function ($query) {
            $query->where('role', 'Ouderraad');
        })->get();

        $praktijkbgeleiding = User::whereHas('roles', function ($query) {
            $query->where('role', 'Praktijkbegeleider');
        })->get();

        $admin = User::whereHas('roles', function ($query) {
            $query->where('role', 'Administratie');
        })->get();

        $vrijwilliger = User::whereHas('roles', function ($query) {
            $query->where('role', 'Vrijwilliger');
        })->get();

        return view('leiding.leiding', ['roles' => $roles, 'user' => $user, 'praktijkbgeleiding' => $praktijkbgeleiding, 'bestuur' => $bestuur, 'dolfijnen' => $dolfijnen, 'zeeverkenners' => $zeeverkenners, 'loodsen' => $loodsen, 'afterloodsen' => $afterloodsen, 'ouderraad' => $ouderraad, 'admin' => $admin, 'vrijwilliger' => $vrijwilliger]);
    }
}
