<?php

namespace App\Http\Middleware;

use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize as Middleware;

class CustomPostSize extends Middleware
{
    /**
     * Determine the server 'post_max_size' as bytes.
     *
     * @return int
     */
    protected function getPostMaxSize()
    {
        // Return 32MB in bytes (32 * 1024 * 1024)
        return 33554432;
    }
}
