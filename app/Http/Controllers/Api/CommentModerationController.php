<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Comment;
use App\Http\Resources\CommentResource;
use App\Http\Controllers\Controller;

class CommentModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth.jwt', 'admin']);
    }

    public function pending(): JsonResponse
    {
        $comments = Comment::with(['user', 'newsletter'])
            ->pending()
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return response()->json([
            'data' => CommentResource::collection($comments),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ]
        ]);
    }

    public function moderate(Request $request, Comment $comment): JsonResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected', 'spam'])],
            'reason' => 'sometimes|string|max:500'
        ]);

        $comment->update([
            'status' => $request->status,
            'moderated_at' => now(),
            'moderated_by' => Auth::id(),
            'metadata' => array_merge($comment->metadata ?? [], [
                'moderation_reason' => $request->reason
            ])
        ]);

        $comment->load(['user', 'moderatedBy']);

        return response()->json([
            'message' => 'Comment moderated successfully',
            'data' => new CommentResource($comment)
        ]);
    }

    public function bulkModerate(Request $request): JsonResponse
    {
        $request->validate([
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
            'status' => ['required', Rule::in(['approved', 'rejected', 'spam'])],
            'reason' => 'sometimes|string|max:500'
        ]);

        $updated = Comment::whereIn('id', $request->comment_ids)
            ->update([
                'status' => $request->status,
                'moderated_at' => now(),
                'moderated_by' => Auth::id(),
                'metadata' => [
                    'moderation_reason' => $request->reason ?? 'Bulk moderation'
                ]
            ]);

        return response()->json([
            'message' => "Successfully moderated {$updated} comments",
            'updated_count' => $updated
        ]);
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'pending' => Comment::pending()->count(),
            'approved' => Comment::approved()->count(),
            'rejected' => Comment::rejected()->count(),
            'spam' => Comment::spam()->count(),
            'total' => Comment::count(),
            'today' => Comment::whereDate('created_at', today())->count(),
            'this_week' => Comment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    public function flagged(): JsonResponse
    {
        // Get comments that might need attention (reported, contains flagged words, etc.)
        $flaggedComments = Comment::with(['user', 'newsletter'])
            ->where(function ($query) {
                $query->where('content', 'like', '%spam%')
                      ->orWhere('content', 'like', '%inappropriate%')
                      ->orWhereJsonContains('metadata->flags', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => CommentResource::collection($flaggedComments),
            'meta' => [
                'current_page' => $flaggedComments->currentPage(),
                'last_page' => $flaggedComments->lastPage(),
                'per_page' => $flaggedComments->perPage(),
                'total' => $flaggedComments->total(),
            ]
        ]);
    }
}
