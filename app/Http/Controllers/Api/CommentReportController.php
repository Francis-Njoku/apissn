<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Comment;
use App\Http\Controllers\Controller;

class CommentReportController extends Controller
{
    public function report(Request $request, Comment $comment): JsonResponse
    {
        $request->validate([
            'reason' => 'required|in:spam,inappropriate,harassment,off-topic,other',
            'description' => 'sometimes|string|max:500'
        ]);

        $userId = Auth::id();
        $reports = $comment->metadata['reports'] ?? [];

        // Check if user already reported this comment
        if (collect($reports)->contains('user_id', $userId)) {
            return response()->json(['message' => 'You have already reported this comment'], 409);
        }

        $reports[] = [
            'user_id' => $userId,
            'reason' => $request->reason,
            'description' => $request->description,
            'reported_at' => now()->toISOString(),
            'ip_address' => $request->ip()
        ];

        $comment->update([
            'metadata' => array_merge($comment->metadata ?? [], [
                'reports' => $reports,
                'report_count' => count($reports)
            ])
        ]);

        // Auto-flag if multiple reports
        if (count($reports) >= 3 && $comment->status === 'approved') {
            $comment->update(['status' => 'pending']);
        }

        return response()->json([
            'message' => 'Comment reported successfully'
        ]);
    }

    public function unreport(Comment $comment): JsonResponse
    {
        $userId = Auth::id();
        $reports = collect($comment->metadata['reports'] ?? [])
            ->reject(fn ($report) => $report['user_id'] === $userId)
            ->values()
            ->toArray();

        $comment->update([
            'metadata' => array_merge($comment->metadata ?? [], [
                'reports' => $reports,
                'report_count' => count($reports)
            ])
        ]);

        return response()->json([
            'message' => 'Report removed successfully'
        ]);
    }
}
