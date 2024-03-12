<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        if ($validator->fails()) {
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
            return response()->json(['imageUrl' => $imageUrl]);
        }

        // If no image is uploaded or validation fails, return an error response
        return response()->json(['error' => 'Invalid image uploaded'], 400);
    }

    public function uploadPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pdf' => 'required|mimes:pdf',
        ]);

        if ($validator->fails()) {
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
            return response()->json(['pdfUrl' => $pdfUrl]);
        }

        // If no image is uploaded or validation fails, return an error response
        return response()->json(['error' => 'Invalid pdf uploaded'], 400);
    }

    public function toggleLike($postId, $postType)
    {

        // Get the authenticated user
        $user = Auth::user();

        $like = Like::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->where('location', $postType)
            ->first();

        if ($like) {
            // If the user has already liked the post, remove the like
            $like->delete();
            $isLiked = false;
        } else {
            // If the user hasn't liked the post yet, add the like
            Like::create([
                'user_id' => $user->id,
                'post_id' => $postId,
                'location' => $postType
            ]);
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

        // Find the comment by ID
        $comment = Comment::findOrFail($id);

        if ($comment->user_id === Auth::id()) {
            // Update the comment content
            $comment->content = $request->input('content');
            $comment->save();

            // Return a response
            return response()->json(['message' => 'Reactie succesvol bijgewerkt.'], 200);
        } else {
            return response()->json(['message' => 'Bewerking mislukt'], 401);
        }
    }

}
