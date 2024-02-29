<?php

namespace App\Http\Controllers;

use App\Models\Comment;
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
            ->paginate(25);


        return view('leiding.home', ['user' => $user, 'posts' => $posts, 'roles' => $roles]);
    }

    /*
     * Forum section, including posts, comments and like handling.
     */

    public function postMessage(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $request->validate([
            'content' => 'string|max:65535',
        ]);

        $content = $request->input('content');

        if (str_contains($content, '<script>') && str_contains($content, '<script') && str_contains($content, '</script>')) {
            throw ValidationException::withMessages(['content' => 'Je post kan niet geplaatst worden vanwege ongeldige inhoud.']);
        }


        $dom = new DOMDocument();
        $dom->loadHTML($content);


        $elements = $dom->getElementsByTagName('*');
        $containsClasses = false;

        foreach ($elements as $element) {
            $classes = $element->getAttribute('class');
            if (!empty($classes) && strpos($classes, 'forum-image') === false) {
                $containsClasses = true;
                break;
            }
        }

        if ($containsClasses) {
            throw ValidationException::withMessages(['content' => 'Je post kan niet geplaatst worden.']);
        }

        $post = Post::create([
            'content' => $content,
            'user_id' => Auth::id(),
            'location' => 4,
        ]);

        return redirect()->route('leiding', ['#' . $post->id]);
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
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet bekijken.');
        }

        return view('leiding.post', ['user' => $user, 'post' => $post]);
    }

    public function postComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'string|max:65535',
        ]);

        $content = $request->input('content');

        if (str_contains($content, '<script>') && str_contains($content, '<script') && str_contains($content, '</script>')) {
            throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden vanwege ongeldige inhoud.']);
        }

        $dom = new DOMDocument();
        $dom->loadHTML($content);

        $elements = $dom->getElementsByTagName('*');
        $containsClasses = false;

        foreach ($elements as $element) {
            $classes = $element->getAttribute('class');
            if (!empty($classes) && strpos($classes, 'forum-image') === false) {
                $containsClasses = true;
                break;
            }
        }

        if ($containsClasses) {
            throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden.']);
        }

        $comment = Comment::create([
            'content' => $content,
            'user_id' => Auth::id(),
            'post_id' => $id,
        ]);


        return redirect()->route('leiding.post', [$id, '#comments']);

    }

    public function postReaction(Request $request, $id, $commentId)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|max:65535',
        ]);

        $content = $request->input('content');

        if (str_contains($content, '<script>') && str_contains($content, '<script') && str_contains($content, '</script>')) {
            throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden vanwege ongeldige inhoud.']);
        }


        $dom = new DOMDocument();
        $dom->loadHTML($content);


        $elements = $dom->getElementsByTagName('*');
        $containsClasses = false;

        foreach ($elements as $element) {
            $classes = $element->getAttribute('class');
            if (!empty($classes) && strpos($classes, 'forum-image') === false) {
                $containsClasses = true;
                break;
            }
        }

        if ($containsClasses) {
            throw ValidationException::withMessages(['content' => 'Je reactie kan niet geplaatst worden.']);
        }

        $comment = Comment::create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
            'post_id' => $id,
            'comment_id' => $commentId,
        ]);


        return redirect()->route('leiding.post', [$id, '#comment-' . $comment->id]);
    }

    public function editPost($id)
    {
        $user = Auth::user();

        $post = Post::findOrFail($id);

        if ($post->location !== 4) {
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
        $user = Auth::user();

        $post = Post::findOrFail($id);

        if ($post->user_id === Auth::id()) {
            $validatedData = $request->validate([
                'content' => 'string|max:65535',
            ]);

            $content = $request->input('content');

            if (str_contains($content, '<script>') && str_contains($content, '<script') && str_contains($content, '</script>')) {
                throw ValidationException::withMessages(['content' => 'Je post kan niet bewerkt worden vanwege ongeldige inhoud.']);
            }


            $dom = new DOMDocument();
            $dom->loadHTML($content);


            $elements = $dom->getElementsByTagName('*');
            $containsClasses = false;

            foreach ($elements as $element) {
                $classes = $element->getAttribute('class');
                if (!empty($classes) && strpos($classes, 'forum-image') === false) {
                    $containsClasses = true;
                    break;
                }
            }

            if ($containsClasses) {
                throw ValidationException::withMessages(['content' => 'Je post kan niet bewerkt worden.']);
            } else {
                $post->update($validatedData);
            }

            return redirect()->route('leiding.post', $id);
        } else {
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Administratie')) {

            foreach ($post->comments as $comment) {
                $comment->delete();
            }

            foreach ($post->likes as $like) {
                $like->delete();
            }

            $post->delete();

            return redirect()->route('leiding', ['#posts']);

        } else {
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    public function deleteComment($id, $postId)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Administratie')) {

            $comment->delete();

            return redirect()->route('leiding.post', [$postId, '#comments']);
        } else {
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
