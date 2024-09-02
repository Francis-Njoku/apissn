<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Payment::where('user_id', Auth::id())->where('status', 'active')->exists()) {
            return $next($request);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorised'
            ], 401);
        }
    }
}
