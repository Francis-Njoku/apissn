<?php

// CommentController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        $comment = Comment::create($validated);

        return new CommentResource($comment);
    }

    public function index(Request $request)
    {
        $comments = Comment::with('user')->latest()->paginate(10);
        return CommentResource::collection($comments);
    }

    public function show(Comment $comment)
    {
        return new CommentResource($comment);
    }

    public function update(CommentRequest $request, Comment $comment)
    {
        $comment->update($request->validated());
        return new CommentResource($comment);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json(['message' => 'Comment deleted']);
    }
}
