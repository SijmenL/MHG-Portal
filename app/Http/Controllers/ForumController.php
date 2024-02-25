<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    public function upload(Request $request)
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

    public function toggleLike($postId)
    {

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user has already liked the post
        $like = Like::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->first();

        if ($like) {
            // If the user has already liked the post, remove the like
            $like->delete();
            $isLiked = false;
        } else {
            // If the user hasn't liked the post yet, add the like
            Like::create([
                'user_id' => $user->id,
                'post_id' => $postId
            ]);
            $isLiked = true;
        }

        // Get the updated like count
        $likeCount = Like::where('post_id', $postId)->count();


        // Return JSON response with updated like count and like status
        return response()->json([
            'likeCount' => $likeCount,
            'isLiked' => $isLiked
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

        // Update the comment content
        $comment->content = $request->input('content');
        $comment->save();

        // Return a response
        return response()->json(['message' => 'Comment updated successfully'], 200);
    }
}
