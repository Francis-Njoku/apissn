<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Comment;

trait HasComments
{
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'newsletter_id');
    }

    public function approvedComments(): HasMany
    {
        return $this->comments()->approved();
    }

    public function pendingComments(): HasMany
    {
        return $this->comments()->pending();
    }

    public function commentsCount(): int
    {
        return $this->comments()->approved()->count();
    }

    public function latestComments(int $limit = 5): HasMany
    {
        return $this->comments()
            ->approved()
            ->with('user')
            ->latest()
            ->limit($limit);
    }
}
