<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Closure;

class CommentRateLimit
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'comment_rate_limit:' . ($request->user()?->id ?? $request->ip());

        if (Cache::has($key)) {
            $attempts = Cache::get($key);
            if ($attempts >= 5) { // 5 comments per minute
                return response()->json([
                    'message' => 'Too many comments. Please wait before posting again.'
                ], 429);
            }
            Cache::put($key, $attempts + 1, 60);
        } else {
            Cache::put($key, 1, 60);
        }

        return $next($request);
    }
}
