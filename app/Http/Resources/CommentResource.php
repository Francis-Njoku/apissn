<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CommentResource extends JsonResource
{
    private $currentDepth = 0;

    public function __construct($resource, $currentDepth = 0)
    {
        parent::__construct($resource);
        $this->currentDepth = $currentDepth;
    }

    public function toArray($request)
    {
        $includeReplies = $this->currentDepth < 5;

        return [
            'id' => $this->id,
            'content' => $this->content,
            'status' => $this->status,
            'author' => [
                'id' => $this->user_id,
                'name' => $this->user ? trim($this->user->first_name . ' ' . $this->user->last_name) : $this->author_name,
                'email' => $this->user ? $this->user->email : $this->author_email,
                'avatar' => $this->user ? $this->user->avatar : null,
            ],
            'newsletter' => $this->whenLoaded('newsletter', function () {
                return [
                    'id' => $this->newsletter->id,
                    'title' => $this->newsletter->title ?? null,
                ];
            }),
            'parent_id' => $this->parent_id,
            'replies_count' => $this->replies()->approved()->count(),
            'replies' => $includeReplies && $this->relationLoaded('replies') ?
                CommentResource::collection($this->replies, $this->currentDepth + 1) : [],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderated_by' => $this->whenLoaded('moderatedBy', function () {
                return [
                    'id' => $this->moderatedBy->id,
                    'name' => $this->moderatedBy->name,
                ];
            }),
            'can_edit' => $this->canEdit($request->user()),
            'can_delete' => $this->canDelete($request->user()),
        ];
    }

    private function canEdit($user): bool
    {
        if (!$user) {
            return false;
        }
        return $user->id === $this->user_id || $user->hasRole('admin') || $user->hasRole('moderator');
    }

    private function canDelete($user): bool
    {
        if (!$user) {
            return false;
        }
        return $user->id === $this->user_id || $user->hasRole('admin') || $user->hasRole('moderator');
    }
}
