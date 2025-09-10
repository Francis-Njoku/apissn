<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Payment;
use App\Models\FeatureLog;
use App\Http\Controllers\Controller;


class AdminMetricsController extends Controller
{
    public function userFunnel(Request $request)
    {
        $start = $request->input('start', now()->subDays(30));
        $end = $request->input('end', now());
        
        return [
            'visitors' => 0, // Placeholder for actual analytics
            'registered' => User::whereBetween('created_at', [$start, $end])->count(),
            'subscribed' => Payment::whereBetween('created_at', [$start, $end])
                ->where('status', 'active')->count(),
            // Using updated_at as proxy for last login activity
            // TODO: Add last_login_at column to users table for accurate tracking
            'active' => User::where('updated_at', '>=', now()->subDays(7))->count()
        ];
    }

    public function subscriptionHealth()
    {
        return [
            'mrr' => Payment::where('status', 'active')->sum('amount'),
            'churn_rate' => $this->calculateChurnRate(),
            'clv' => $this->calculateCLV(),
            'expansion' => Payment::where('status', 'active')
                ->where('created_at', '>=', now()->subMonth())
                ->sum('amount')
        ];
    }

    private function calculateChurnRate()
    {
        $total = Payment::count();
        $cancelled = Payment::where('status', 'cancelled')
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
            
        return $total > 0 ? ($cancelled / $total) * 100 : 0;
    }

    private function calculateCLV()
    {
        $activePayments = Payment::where('status', 'active')->get();
        if ($activePayments->isEmpty()) return 0;
        
        $totalValue = $activePayments->sum('amount');
        return $totalValue / $activePayments->count();
    }
    
    public function cohortAnalysis($period)
    {
        $cohorts = User::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as signup_month,
                COUNT(*) as total_users
            ")
            ->groupBy('signup_month')
            ->orderBy('signup_month')
            ->get();
            
        return $cohorts;
    }
    
    public function engagementMetrics($userId = null)
    {
        $query = FeatureLog::query();
        if ($userId) $query->where('user_id', $userId);
        
        return [
            'feature_usage' => $query->select('feature_name')
                ->selectRaw('sum(usage_count) as total_usage')
                ->groupBy('feature_name')
                ->get()
        ];
    }
    
    public function paymentAnalytics()
    {
        return [
            'total_revenue' => Payment::sum('amount'),
            'avg_payment' => Payment::avg('amount'),
            'success_rate' => Payment::where('status', 'success')->count() / Payment::count() * 100,
            'payment_methods' => Payment::select('payment_method')
                ->selectRaw('count(*) as count')
                ->groupBy('payment_method')
                ->get()
        ];
    }
}