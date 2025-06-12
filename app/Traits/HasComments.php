<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Comment;

trait HasComments
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function approvedComments(): MorphMany
    {
        return $this->comments()->approved();
    }

    public function pendingComments(): MorphMany
    {
        return $this->comments()->pending();
    }

    public function commentsCount(): int
    {
        return $this->comments()->approved()->count();
    }

    public function latestComments(int $limit = 5): MorphMany
    {
        return $this->comments()
            ->approved()
            ->with('user')
            ->latest()
            ->limit($limit);
    }
}
