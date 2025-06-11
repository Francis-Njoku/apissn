<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['newsletter_id', 'user_id', 'body', 'status'];

    public function article()
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
