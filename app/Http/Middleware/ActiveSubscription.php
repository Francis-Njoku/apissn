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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isAdmin() || $this->hasActiveSubscription()) {
            return $next($request);
        }
        
        return $this->unauthorizedResponse();
    }
    
    /**
     * Check if the authenticated user is an admin.
     *
     * @return bool
     */
    private function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->role_id == 1;
    }
    
    /**
     * Check if the authenticated user has an active subscription.
     *
     * @return bool
     */
    private function hasActiveSubscription(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        return Payment::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('due_date', '>', now())
            ->orderByDesc('due_date')
            ->exists();

        return $latestPayment !== null;
    }
    
    /**
     * Return unauthorized response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function unauthorizedResponse()
    {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized: Active subscription required'
        ], 401);
    }
}