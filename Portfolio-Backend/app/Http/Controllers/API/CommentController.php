<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display comments for an article.
     */
    public function index(Article $article)
    {
        $comments = $article->comments()
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($comments);
    }

    /**
     * Store a new comment.
     */
    public function store(Request $request, Article $article)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $comment = $article->comments()->create([
            'name' => $request->name,
            'email' => $request->email,
            'content' => $request->content,
            'is_approved' => false,
        ]);

        return response()->json([
            'message' => 'Comment submitted successfully and is pending approval.',
            'comment' => $comment
        ], 201);
    }

    /**
     * List all comments (admin only).
     */
    public function adminIndex(Request $request)
    {
        $query = Comment::with('article');
        
        if ($request->has('approved')) {
            $query->where('is_approved', $request->boolean('approved'));
        }
        
        // Filter by article
        if ($request->has('article_id')) {
            $query->where('article_id', $request->article_id);
        }
        
        $comments = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json($comments);
    }

    /**
     * Approve or reject a comment (admin only).
     */
    public function approve(Comment $comment)
    {
        $comment->is_approved = true;
        $comment->save();
        
        return response()->json($comment);
    }

    /**
     * Delete a comment (admin only).
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        
        return response()->json(null, 204);
    }
}