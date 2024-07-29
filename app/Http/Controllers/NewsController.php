<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Log;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class NewsController extends Controller
{
    public function home()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        $news = News::where('accepted', true)
            ->orderBy('date', 'desc')
            ->paginate(25);


        return view('news.home', ['user' => $user, 'roles' => $roles, 'news' => $news]);
    }

    public function news()
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();


        return view('news.new_news', ['user' => $user, 'roles' => $roles]);
    }

    public function viewNewsPage(Request $request)
    {
        $search = request('search');

        // Get the 'items' query parameter or default to 25 if not set
        $items = $request->query('items', 25);

        // Ensure 'items' is a positive integer
        if (!is_numeric($items) || $items <= 0) {
            $items = 25;
        }

        // Fetch the news items based on the 'items' parameter
        $news = News::where('accepted', true)
            ->orderBy('date', 'desc')
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%')
                        ->orWhere('category', 'like', '%' . $search . '%')
                        ->orWhere('speltak', 'like', '%' . $search . '%')
                        ->orWhere('date', 'like', '%' . $search . '%')
                        ->orWhere('category', 'like', '%' . $search . '%');
                });
            })
            ->paginate((int)$items);


        return view('news.list', ['news' => $news, 'search' => $search, 'items' => $items]);
    }


    public function viewNewsItem($id)
    {
        if ($id === '-1') {
            $news = null;
        } else {
            try {
                $news = News::find($id);
            } catch (ModelNotFoundException $exception) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'View news items', 'news', 'News id: ' . $id, 'Nieuws bestaat niet');
                $news = null;
            }
            if ($news === null) {
                $log = new Log();
                $log->createLog(auth()->user()->id, 1, 'View news items', 'news', 'News id: ' . $id, 'Nieuws bestaat niet');
                $news = null;
            }
        }


        return view('news.item', ['news' => $news]);
    }

    public function newsCreate(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'content' => 'string|max:65535|required',
            'description' => 'string|max:200|required',
            'date' => 'date|required',
            'category' => 'string|required',
            'title' => 'string|required',
            'speltak' => 'array',
            'image' => 'mimes:jpeg,png,jpg,gif,webp|max:6000|required',
        ]);

        try {
            // Process image upload
            $newPictureName = time() . '.' . $request->image->extension();
            $destinationPath = 'files/news/news_images';
            $request->image->move(public_path($destinationPath), $newPictureName);

            if ($request->input('speltak') !== null) {
                $speltak = $request->input('speltak');
                $speltakken = implode(', ', $speltak);
            } else {
                $speltakken = null;
            }

            // Validate content for disallowed elements or styles
            if (ForumController::validatePostData($request->input('content'))) {

                // Create the news item
                $news = News::create([
                    'content' => $request->input('content'),
                    'description' => $request->input('description'),
                    'date' => $request->input('date'),
                    'category' => $request->input('category'),
                    'title' => $request->input('title'),
                    'user_id' => Auth::id(),
                    'speltak' => $speltakken,
                    'image' => $newPictureName,
                    'accepted' => false,
                ]);

                // Log the creation of the news item
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Create nieuws', 'nieuws', 'News id: ' . $news->id, '');

                return redirect()->route('news.new')->with('success', 'Je nieuwsitem is opgeslagen!.');
            } else {
                throw ValidationException::withMessages(['content' => 'Je nieuwsitem kan niet opgeslagen worden.']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // General exception handling for unexpected errors
            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het opslaan van je nieuwsitem. Probeer het opnieuw.')->withInput();
        }
    }

    public function userNews()
    {
        $search = request('search');

        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        // Search unaccepted news
        $news_unaccepted = News::where('accepted', false)
            ->where('user_id', Auth::id())
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('content', 'LIKE', '%' . $search . '%')
                        ->orWhere('title', 'LIKE', '%' . $search . '%')
                        ->orWhere('description', 'LIKE', '%' . $search . '%')
                        ->orWhere('speltak', 'LIKE', '%' . $search . '%')
                        ->orWhere('category', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('date', 'desc')
            ->get();

        // Search accepted news
        $news_accepted = News::where('accepted', true)
            ->where('user_id', Auth::id())
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('content', 'LIKE', '%' . $search . '%')
                        ->orWhere('title', 'LIKE', '%' . $search . '%')
                        ->orWhere('description', 'LIKE', '%' . $search . '%')
                        ->orWhere('speltak', 'LIKE', '%' . $search . '%')
                        ->orWhere('category', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('date', 'desc')
            ->get();


        return view('news.my_news', ['user' => $user, 'search' => $search, 'roles' => $roles, 'news_unaccepted' => $news_unaccepted, 'news_accepted' => $news_accepted]);
    }

    public function editNews($id)
    {
        $user = Auth::user();
        $roles = $user->roles()->orderBy('role', 'asc')->get();

        try {
            $news = News::find($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'News details', 'news', 'News id: ' . $id, 'Nieuws bestaat niet');
            return redirect()->route('news.user')->with('error', 'Dit nieuws bestaat niet.');
        }
        if ($news === null) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'News details', 'news', 'News id: ' . $id, 'Nieuws bestaat niet');
            return redirect()->route('news.user')->with('error', 'Dit nieuws bestaat niet.');
        }

        if ($news->accepted === 1 || $news->user_id !== Auth::id()) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'News details', 'news', 'News id: ' . $id, 'Nieuws mag niet bewerkt worden');
            return redirect()->route('news.user')->with('error', 'Je mag dit nieuws niet bewerken.');
        }

        return view('news.edit_news', ['user' => $user, 'roles' => $roles, 'news' => $news]);
    }

    public function saveEditNews(Request $request, $id)
    {
        $request->validate([
            'content' => 'string|max:65535|required',
            'description' => 'string|max:200|required',
            'date' => 'date|required',
            'category' => 'string|required',
            'title' => 'string|required',
            'speltak' => 'array',
            'image' => 'mimes:jpeg,png,jpg,gif,webp|max:6000',
        ]);

        try {
            $news = News::find($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'News edit', 'news', 'News id: ' . $id, 'Nieuws bestaat niet');
            return redirect()->route('news.user')->with('error', 'Dit nieuws bestaat niet.');
        }
        if ($news === null) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'News edit', 'news', 'News id: ' . $id, 'Nieuws bestaat niet');
            return redirect()->route('news.user')->with('error', 'Dit nieuws bestaat niet.');
        }

        try {
            if (isset($request->image)) {
                // Process image upload
                $newPictureName = time() . '.' . $request->image->extension();
                $destinationPath = 'files/news/news_images';
                $request->image->move(public_path($destinationPath), $newPictureName);

                $news->image = $newPictureName;
            }

            if ($request->input('speltak') !== null) {
                $speltak = $request->input('speltak');
                $speltakken = implode(', ', $speltak);
            } else {
                $speltakken = null;
            }

            // Validate content for disallowed elements or styles
            if (ForumController::validatePostData($request->input('content'))) {

                $news->content = $request->input('content');
                $news->description = $request->input('description');
                $news->date = $request->input('date');
                $news->category = $request->input('category');
                $news->title = $request->input('title');
                $news->speltak = $speltakken;

                $news->save();

                // Log the creation of the news item
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'edit nieuws', 'nieuws', 'News id: ' . $news->id, '');

                return redirect()->route('news.user.edit', $id)->with('success', 'Je nieuwsitem is opgeslagen!.');
            } else {
                throw ValidationException::withMessages(['content' => 'Je nieuwsitem kan niet opgeslagen worden.']);
            }
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // General exception handling for unexpected errors
            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het opslaan van je nieuwsitem. Probeer het opnieuw.')->withInput();
        }
    }

    public function deleteNews($id)
    {
        try {
            $news = News::find($id);
        } catch (ModelNotFoundException $exception) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'News delete', 'news', 'News id: ' . $id, 'Nieuws bestaat niet');
            return redirect()->route('news.user')->with('error', 'Dit nieuws bestaat niet.');
        }
        if ($news === null) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'News delete', 'news', 'News id: ' . $id, 'Nieuws bestaat niet');
            return redirect()->route('news.user')->with('error', 'Dit nieuws bestaat niet.');
        }

        if ($news->accepted === 1 || $news->user_id !== Auth::id()) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 1, 'News delete', 'news', 'News id: ' . $id, 'Nieuws mag niet verwijderd worden');
            return redirect()->route('news.user')->with('error', 'Je mag dit nieuws niet verwijderen.');
        }

        $news->delete();


        $log = new Log();
        $log->createLog(auth()->user()->id, 2, 'News delete', 'news', $news->id, '');

        return redirect()->route('news.user')->with('success', 'Je nieuwsitem is permanent verwijderd!');

    }
}
