<?php

namespace App\Http\Controllers\Api;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Comment;
use App\Http\Resources\CommentResource;
use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseHelper;

class CommentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'newsletter_id' => 'required|integer|exists:newsletter,id',
            'status' => ['sometimes', Rule::in(['pending', 'approved', 'rejected', 'spam'])],
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort' => 'sometimes|in:newest,oldest',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }


        $query = Comment::with(['user', 'replies.user'])
            ->where('newsletter_id', $request->newsletter_id);

        // Show approved comments to everyone, and pending comments to their authors
        if (!Auth::user() || !Auth::user()->hasAnyRole(['admin', 'moderator'])) {
            $query->where(function ($q) {
                $q->approved()
                  ->orWhere(function ($q2) {
                      $q2->pending()
                         ->where('user_id', Auth::id());
                  });
            });
        } elseif ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'newest');
        $query->orderBy('created_at', $sort === 'newest' ? 'desc' : 'asc');

        // Get all comments and build the tree
        $allComments = $query->with(['replies' => function ($query) {
            $query->with('user')
                  ->when(
                      !Auth::user() || !Auth::user()->hasAnyRole(['admin', 'moderator']),
                      fn ($q) => $q->where(function ($q2) {
                          $q2->approved()
                             ->orWhere(function ($q3) {
                                 $q3->pending()
                                    ->where('user_id', Auth::id());
                             });
                      })
                  );
        }])->get();

        $topLevelComments = $allComments->whereNull('parent_id');

        return response()->json([
            'data' => CommentResource::collection($topLevelComments->load('user')),
            'meta' => [
                'total' => $allComments->count(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'newsletter_id' => 'required|integer|exists:newsletter,id',
            'content' => 'required|string|min:3|max:1000',
            'parent_id' => 'sometimes|exists:comments,id',
            'author_name' => 'required_without:user_id|string|max:255',
            'author_email' => 'required_without:user_id|email|max:255',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        $data = $validator->validated();
        $data['ip_address'] = $request->ip();

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
            unset($data['author_name'], $data['author_email']);
        }

        // Auto-approve for trusted users, otherwise set to pending
        $data['status'] = $this->shouldAutoApprove($request) ? 'approved' : 'pending';

        $comment = Comment::create($data);
        $comment->load(['user', 'replies']);

        return response()->json([
            'message' => 'Comment created successfully',
            'data' => new CommentResource($comment)
        ], 201);
    }

    public function show(Comment $comment): JsonResponse
    {
        $comment->load(['user', 'replies.user']);

        // Check if user can view this comment
        if (!$comment->isApproved() &&
            !$this->canModerate() &&
            !(Auth::check() && $comment->user_id === Auth::id())) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        return response()->json([
            'data' => new CommentResource($comment)
        ]);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        if (!$this->canEditComment($comment)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:3|max:1000',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        $comment->update([
            'content' => $request->content,
            'status' => 'pending' // Reset to pending after edit
        ]);

        $comment->load(['user', 'replies']);

        return response()->json([
            'message' => 'Comment updated successfully',
            'data' => new CommentResource($comment)
        ]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        if (!$this->canDeleteComment($comment)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }

    private function shouldAutoApprove(Request $request): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false; // Guest comments need moderation
        }

        // Auto-approve for admins and moderators
        if ($user->hasAnyRole(['admin', 'moderator'])) {
            return true;
        }

        // Auto-approve for verified users with good history
        return $user->hasVerifiedEmail() && $user->comments()->approved()->count() >= 3;
    }

    private function canModerate(): bool
    {
        return Auth::check() && Auth::user()->hasAnyRole(['admin', 'moderator']);
    }

    private function canEditComment(Comment $comment): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $user->id === $comment->user_id || $user->hasAnyRole(['admin', 'moderator']);
    }

    private function canDeleteComment(Comment $comment): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $user->id === $comment->user_id || $user->hasAnyRole(['admin', 'moderator']);

    }
}