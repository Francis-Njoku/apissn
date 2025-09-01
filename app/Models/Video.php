<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'cf_uid',
        'status',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array', // auto-cast JSON to array
    ];
}
