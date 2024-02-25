<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DolfijnenController extends Controller
{
    public function view()
    {
        $user = Auth::user();

        $posts = Post::where('location', 0)
            ->orderBy('created_at', 'desc') // or 'updated_at' if you prefer
            ->paginate(25);


        return view('speltakken.dolfijnen.home', ['user' => $user, 'posts' => $posts]);
    }

    public function postMessage(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $request->validate([
            'content' => 'string|max:65535',
        ]);

        $post = Post::create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
            'location' => 0,
        ]);

        return redirect()->route('dolfijnen', ['user' => $user, 'roles' => $roles])->with('success', 'Je bericht is gepost!');
    }

    public function viewPost($id)
    {
        $user = Auth::user();

        $post = Post::with(['comments' => function ($query) {
            $query->orderBy('created_at', 'desc'); // Sort comments by newest
        }])->findOrFail($id);

        return view('speltakken.dolfijnen.post', ['user' => $user, 'post' => $post]);
    }

    public function postComment(Request $request, $id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $request->validate([
            'content' => 'string|max:65535',
        ]);

        $comment = Comment::create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
            'post_id' => $id,
        ]);

        return redirect()->route('dolfijnen.post', $id);
    }

    public function editPost($id)
    {
        $user = Auth::user();

        $post = Post::findOrFail($id);

        if ($post->user_id === Auth::id()) {
            return view('speltakken.dolfijnen.post_edit', ['user' => $user, 'post' => $post]);
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

            $post->update($validatedData);


            return redirect()->route('dolfijnen.post', $id);
        } else {
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet bewerken.');
        }
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Dolfijnen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

            foreach ($post->comments as $comment) {
                $comment->delete();
            }

            foreach ($post->likes as $like) {
                $like->delete();
            }

            $post->delete();

            return redirect()->route('dolfijnen');
        } else {
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

    public function deleteComment($id, $postId)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id === Auth::id() || auth()->user()->roles->contains('role', 'Dolfijnen Leiding') || auth()->user()->roles->contains('role', 'Administratie') || auth()->user()->roles->contains('role', 'Bestuur') || auth()->user()->roles->contains('role', 'Ouderraad')) {

            $comment->delete();

            return redirect()->route('dolfijnen.post', $postId);
        } else {
            return redirect()->route('dashboard')->with('error', 'Je mag deze post niet verwijderen.');
        }
    }

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

        $search = '';

        $users = User::with(['roles' => function ($query) {
            $query->where('role', 'Dolfijn')->orderBy('role', 'asc');
        }])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Dolfijn');
            })
            ->orderBy('last_name')
            ->paginate(25);

        $selected_role = '';

        return view('speltakken.dolfijnen.group', ['user' => $user, 'roles' => $roles, 'users' => $users, 'search' => $search, 'selected_role' => $selected_role]);
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
                $query->where('role', 'Dolfijn');
            })
            ->orderBy('last_name')
            ->paginate(25);


        $all_roles = Role::orderBy('role')->get();

        return view('speltakken.dolfijnen.group', ['user' => $user, 'roles' => $roles, 'users' => $users, 'search' => $search, 'all_roles' => $all_roles, 'selected_role' => $selected_role]);
    }

    public function groupDetails($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        $account = User::with(['roles' => function ($query) {
            $query->orderBy('role', 'asc');
        }])
            ->whereHas('roles', function ($query) {
                $query->where('role', 'Dolfijn');
            })
            ->find($id);


        return view('speltakken.dolfijnen.group_details', ['user' => $user, 'roles' => $roles, 'account' => $account]);
    }
}
