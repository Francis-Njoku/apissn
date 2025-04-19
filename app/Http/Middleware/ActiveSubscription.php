<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Closure;
use App\Models\Payment;

class ActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role_id == 1) {
            return $next($request);
        } 
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
