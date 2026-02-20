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

class CommentModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth.jwt', 'admin']);
    }

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,approved,rejected,spam,all',
            'newsletter_id' => 'sometimes|integer|exists:newsletter,id',
            'author_type' => 'sometimes|in:guest,registered',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort' => 'sometimes|in:newest,oldest',
            'search' => 'sometimes|string|max:255',
            'user_id' => 'sometimes|integer|exists:users,id',
            'ip_address' => 'sometimes|ip',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        $query = Comment::with(['user', 'newsletter', 'moderatedBy']);

        // Status filter - support multiple statuses or 'all'
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Newsletter filter
        if ($request->has('newsletter_id')) {
            $query->where('newsletter_id', $request->newsletter_id);
        }

        // Author type filter
        if ($request->has('author_type')) {
            if ($request->author_type === 'guest') {
                $query->whereNull('user_id');
            } elseif ($request->author_type === 'registered') {
                $query->whereNotNull('user_id');
            }
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // User filter
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // IP address filter
        if ($request->has('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        // Search filter
        if ($request->has('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('content', 'like', $searchTerm)
                      ->orWhere('author_name', 'like', $searchTerm)
                      ->orWhere('author_email', 'like', $searchTerm);
            });
        }

        $sort = $request->get('sort', 'newest');
        $query->orderBy('created_at', $sort === 'newest' ? 'desc' : 'asc');

        $comments = $query->paginate($request->get('per_page', 20));

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

    public function pending(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'newsletter_id' => 'sometimes|integer|exists:newsletter,id',
            'author_type' => 'sometimes|in:guest,registered',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort' => 'sometimes|in:newest,oldest',
            'search' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        $query = Comment::with(['user', 'newsletter'])
            ->pending();

        // Apply filters
        if ($request->has('newsletter_id')) {
            $query->where('newsletter_id', $request->newsletter_id);
        }

        // Author type filter
        if ($request->has('author_type')) {
            if ($request->author_type === 'guest') {
                $query->whereNull('user_id');
            } elseif ($request->author_type === 'registered') {
                $query->whereNotNull('user_id');
            }
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Search filter
        if ($request->has('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('content', 'like', $searchTerm)
                      ->orWhere('author_name', 'like', $searchTerm)
                      ->orWhere('author_email', 'like', $searchTerm);
            });
        }

        $sort = $request->get('sort', 'newest');
        $query->orderBy('created_at', $sort === 'newest' ? 'desc' : 'asc');

        $comments = $query->paginate($request->get('per_page', 20));

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
        $validator = Validator::make($request->all(), [
            'status' => ['required', Rule::in(['approved', 'rejected', 'spam'])],
            'reason' => 'sometimes|string|max:500'
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        $comment->update([
            'status' => $request->status,
            'moderated_at' => now(),
            'moderated_by' => Auth::id(),
            'metadata' => array_merge($comment->metadata ?? [], [
                'moderation_reason' => $request->reason ?? null
            ])
        ]);

        $comment->load(['user', 'moderatedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Comment moderated successfully',
            'data' => new CommentResource($comment)
        ]);
    }

    public function bulkModerate(Request $request): JsonResponse
    {
        // Debug: Log the request content type and body
        \Log::info('Bulk moderate request', [
            'content_type' => $request->header('Content-Type'),
            'method' => $request->method(),
            'all_input' => $request->all(),
            'json' => $request->json()->all(),
            'getContent' => $request->getContent(),
        ]);

        $validator = Validator::make($request->all(), [
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:comments,id',
            'status' => ['required', Rule::in(['approved', 'rejected', 'spam'])],
            'reason' => 'sometimes|string|max:500'
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        $comments = Comment::whereIn('id', $request->comment_ids)->get();
        $updated = 0;

        foreach ($comments as $comment) {
            $comment->update([
                'status' => $request->status,
                'moderated_at' => now(),
                'moderated_by' => Auth::id(),
                'metadata' => array_merge($comment->metadata ?? [], [
                    'bulk_moderation_reason' => $request->reason ?? 'Bulk moderation',
                    'bulk_moderated_at' => now()->toISOString()
                ])
            ]);
            $updated++;
        }

        return response()->json([
            'success' => true,
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

    public function flagged(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'newsletter_id' => 'sometimes|integer|exists:newsletter,id',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort' => 'sometimes|in:newest,oldest',
            'flag_type' => 'sometimes|in:spam,inappropriate,reported,all',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        // Define spam keywords
        $spamKeywords = [
            'spam', 'viagra', 'casino', 'lottery', 'gambling', 'porn',
            'xxx', 'adult', 'click here', 'buy now', 'discount', 'free money',
            'make money', 'work from home', 'get rich', 'earn cash'
        ];

        // Define inappropriate keywords
        $inappropriateKeywords = [
            'inappropriate', 'offensive', 'hate speech', 'racist',
            'sexist', 'violence', 'threat', 'harassment'
        ];

        $query = Comment::with(['user', 'newsletter']);

        // Apply newsletter filter
        if ($request->has('newsletter_id')) {
            $query->where('newsletter_id', $request->newsletter_id);
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Flag type filtering
        $flagType = $request->get('flag_type', 'all');
        
        $query->where(function ($q) use ($flagType, $spamKeywords, $inappropriateKeywords) {
            if ($flagType === 'all' || $flagType === 'spam') {
                foreach ($spamKeywords as $keyword) {
                    $q->orWhere('content', 'like', '%' . $keyword . '%');
                }
            }
            
            if ($flagType === 'all' || $flagType === 'inappropriate') {
                foreach ($inappropriateKeywords as $keyword) {
                    $q->orWhere('content', 'like', '%' . $keyword . '%');
                }
            }
            
            if ($flagType === 'all' || $flagType === 'reported') {
                $q->orWhereJsonContains('metadata->reports', true)
                  ->orWhereJsonLength('metadata->reports', '>', 0);
            }
        });

        $sort = $request->get('sort', 'newest');
        $query->orderBy('created_at', $sort === 'newest' ? 'desc' : 'asc');

        $flaggedComments = $query->paginate($request->get('per_page', 20));

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

    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2|max:255',
            'newsletter_id' => 'sometimes|integer|exists:newsletter,id',
            'status' => 'sometimes|in:pending,approved,rejected,spam,all',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'sort' => 'sometimes|in:newest,oldest',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::validationError($validator->errors()->toArray());
        }

        $query = Comment::with(['user', 'newsletter', 'moderatedBy']);

        // Search across multiple fields
        $searchTerm = '%' . $request->q . '%';
        $query->where(function ($q) use ($searchTerm) {
            $q->where('content', 'like', $searchTerm)
                  ->orWhere('author_name', 'like', $searchTerm)
                  ->orWhere('author_email', 'like', $searchTerm)
                  ->orWhere('ip_address', 'like', $searchTerm)
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('first_name', 'like', $searchTerm)
                               ->orWhere('last_name', 'like', $searchTerm);
                  });
        });

        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Newsletter filter
        if ($request->has('newsletter_id')) {
            $query->where('newsletter_id', $request->newsletter_id);
        }

        // Date range filter
        if ($request->has('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'newest');
        $query->orderBy('created_at', $sort === 'newest' ? 'desc' : 'asc');

        $comments = $query->paginate($request->get('per_page', 20));

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

    public function moderationHistory(Comment $comment): JsonResponse
    {
        $history = [
            'comment_id' => $comment->id,
            'content' => $comment->content,
            'current_status' => $comment->status,
            'moderation_history' => []
        ];

        // Add creation event
        $history['moderation_history'][] = [
            'action' => 'created',
            'status' => 'pending',
            'created_at' => $comment->created_at->toISOString(),
            'author' => [
                'name' => $comment->author_name,
                'email' => $comment->author_email,
                'user_id' => $comment->user_id
            ]
        ];

        // Add moderation events from metadata
        if ($comment->moderated_at) {
            $history['moderation_history'][] = [
                'action' => 'moderated',
                'status' => $comment->status,
                'moderated_by' => $comment->moderatedBy ? [
                    'id' => $comment->moderatedBy->id,
                    'name' => $comment->moderatedBy->name,
                ] : null,
                'moderated_at' => $comment->moderated_at->toISOString(),
                'reason' => $comment->metadata['moderation_reason'] ?? null
            ];
        }

        return response()->json(['data' => $history]);
    }
}