<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Log;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ForumController extends Controller
{
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:6000',
        ]);

        if ($validator->fails()) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 0, 'Upload image', '', '', 'Afbeelding uploaden mislukt');

            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Define storage path where you want to store the uploaded images
            $storagePath = 'files/forum-images/';

            $newPictureName = time() . '-' . $image->getClientOriginalName();

            // Store the uploaded image in the storage path
            $image->move(public_path($storagePath), $newPictureName);

            // Generate the full URL of the uploaded image
            $imageUrl = asset($storagePath . $newPictureName);

            // Return the URL of the uploaded image
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Upload image', '', $imageUrl, '');
            return response()->json(['imageUrl' => $imageUrl]);
        }

        // If no image is uploaded or validation fails, return an error response
        $log = new Log();
        $log->createLog(auth()->user()->id, 0, 'Upload image', '', '', 'Afbeelding uploaden mislukt');
        return response()->json(['error' => 'Invalid image uploaded'], 400);
    }

    public function uploadPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pdf' => 'required|mimes:pdf|max:6000',
        ]);

        if ($validator->fails()) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 0, 'Upload pdf', '', '', 'Pdf uploaden mislukt');

            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        if ($request->hasFile('pdf')) {
            $image = $request->file('pdf');

            // Define storage path where you want to store the uploaded images
            $storagePath = 'files/forum-documents/';

            $newImageName = time() . '-' . $image->getClientOriginalName();

            // Store the uploaded image in the storage path
            $image->move(public_path($storagePath), $newImageName);

            // Generate the full URL of the uploaded image
            $pdfUrl = asset($storagePath . $newImageName);

            // Return the URL of the uploaded image
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Upload pdf', '', $pdfUrl, '');
            return response()->json(['pdfUrl' => $pdfUrl]);
        }

        // If no image is uploaded or validation fails, return an error response
        $log = new Log();
        $log->createLog(auth()->user()->id, 0, 'Upload pdf', '', '', 'Pdf uploaden mislukt');
        return response()->json(['error' => 'Invalid pdf uploaded'], 400);
    }

    public function toggleLike($postId, $postType)
    {
        $user = Auth::user();

        $like = Like::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->where('location', $postType)
            ->first();

        if ($like) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Like', '', 'Post id: '.$postId, 'Like verwijderd');

            $like->delete();
            $isLiked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'post_id' => $postId,
                'location' => $postType
            ]);

            $post = null;
            if ($postType === '0') {
                $post = Post::findOrFail($postId);
            } else {
                $comment = Comment::findOrFail($postId);
                $post = Post::findOrFail($comment->post_id);
            }
            $location = '';

            switch ($post->location) {
                case 0:
                    $location = 'dolfijnen';
                    break;
                case 1:
                    $location = 'zeeverkenners';
                    break;
                case 2:
                    $location = 'loodsen';
                    break;
                case 3:
                    $location = 'afterloodsen';
                    break;
                case 4:
                    $location = 'leiding';
                    break;
                default:
                    break;
            }

            if ($post->user_id !== Auth::id()) {
                $notification = new Notification();
                if ($postType === '0') {
                    $notification->sendNotification(Auth::id(), [$post->user_id], 'Heeft je post geliket!', '/' . $location . '/post/' . $post->id, $location, 'liked_post');
                } else {
                    $notification->sendNotification(Auth::id(), [$post->user_id], 'Heeft je reactie geliket!', '/' . $location . '/post/' . $post->id, $location, 'liked_comment');
                }
            }

            $log = new Log();
            $log->createLog(auth()->user()->id, 2, 'Like', '', 'Post id: '.$postId, 'Like geplaatst');

            $isLiked = true;
        }

        // Get the updated like count
        $likeCount = Like::where('post_id', $postId)->where('location', $postType)->count();


        // Return JSON response with updated like count and like status
        return response()->json([
            'likeCount' => $likeCount,
            'isLiked' => $isLiked,
            'location' => $postType
        ]);
    }

    public function updateComment(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'content' => 'required|string',
        ]);

        if (self::validatePostData($request->input('content'))) {

            // Find the comment by ID
            $comment = Comment::findOrFail($id);

            if ($comment->user_id === Auth::id()) {
                // Update the comment content
                $comment->content = $request->input('content');
                $comment->save();

                // Return a response
                $log = new Log();
                $log->createLog(auth()->user()->id, 2, 'Edit comment', '', 'Comment id: '.$id, '');
                return response()->json(['message' => 'Reactie succesvol bijgewerkt.'], 200);
            } else {
                $log = new Log();
                $log->createLog(auth()->user()->id, 0, 'Edit comment', '', 'Comment id: '.$id, 'Reactie kon niet bewerkt worden');
                return response()->json(['message' => 'Bewerking mislukt'], 401);
            }
        } else {
            $log = new Log();
            $log->createLog(auth()->user()->id, 0, 'Edit comment', '', 'Comment id: '.$id, 'Reactie kon niet bewerkt worden');
            throw ValidationException::withMessages(['content' => 'Je post kon niet bewerkt worden.']);
        }
    }

    public function searchUser(Request $request)
    {
        $search = $request->input('search', '');
        $ids = explode(',', $request->input('selected', ''));

        $selectedUsers = User::where(function ($query) use ($ids, $search) {
            $query->whereIn('id', $ids)
                ->where(function ($query) use ($search) {
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
        })->get();

        $remainingUsers = User::where(function ($query) use ($search) {
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
            ->whereNotIn('id', $ids)
            ->orderBy('created_at', 'asc')
            ->get();

        $remainingUsersCount = max(0,($selectedUsers->count() + $remainingUsers->count()) - 7);

        $firstNineUsers = $selectedUsers->merge($remainingUsers)->splice(0, 7);

        foreach ($firstNineUsers as $user) {
            if ($user->profile_picture) {
                $user->profile_picture_url = asset('profile_pictures/' . $user->profile_picture);
            } else {
                $user->profile_picture_url = asset('img/no_profile_picture.webp');
            }
        }

        $usersWithRemainingCount = [
            'users' => $firstNineUsers,
            'remainingUsersCount' => $remainingUsersCount
        ];

        return response()->json($usersWithRemainingCount);
    }



    public static function validatePostData($content)
    {
        // Check for the presence of <script> tags
        if (str_contains($content, '<script>') || str_contains($content, '<script') || str_contains($content, '</script>')) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 0, 'Create content', '', '', 'Post of reactie mislukt, bevatte javascript.');
            return false;
        }

        // Wrap content in a basic HTML structure
        $content = '<!DOCTYPE html><html><body>' . $content . '</body></html>';

        // Suppress errors and warnings
        libxml_use_internal_errors(true);

        // Load HTML
        $dom = new DOMDocument();
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Clear libxml errors
        libxml_clear_errors();

        // Check for classes
        $elements = $dom->getElementsByTagName('*');
        $containsClasses = false;

        foreach ($elements as $element) {
            $classes = $element->getAttribute('class');
            if (!empty($classes) && strpos($classes, 'forum-image') === false && strpos($classes, 'forum-pdf') === false) {
                $containsClasses = true;
                break;
            }
        }

        if ($containsClasses) {
            $log = new Log();
            $log->createLog(auth()->user()->id, 0, 'Create content', '', '', 'Post of reactie mislukt, bevatte ongeldige css classes.');
            return false;
        }

        return true;
    }


}
