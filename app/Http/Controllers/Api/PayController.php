<?php

namespace App\Http\Controllers\Api;

use Paystack;
use Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\MailchimpService;
use App\Models\User;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Entries;
use App\Models\Coupons;
use App\Http\Resources\PaymentResource;
use App\Http\Controllers\Controller;

class PayController extends Controller
{
    private function getPlanType($id)
    {
        $find_user = Plan::select('plan_name')
            ->where('track', $id)
            ->get();

        if (count($find_user) == 1) {
            foreach ($find_user as $content) {
                $conte = $content->plan_name;
            }
            return $conte;
        } else {
            Session::flash('error', 'Plan not found');

            abort(404);
        }
    }
    public function redirectToGateway(Request $request)
    {
        try {
            // Retrieve data from the request
            $validatedData = $request->validate([
                'planType' => 'required|string|max:255',
                'amount' => 'required',
                'callBackUrl' => 'required',
            ]);

            // Get the base URL
            $callback_url = URL::to('/') . '/api/pay/callback/';

            $email = Auth::user()->email;
            $first_name = Auth::user()->first_name;
            $last_name = Auth::user()->last_name;

            $getPlan = Plan::where('track', $validatedData['planType'])->first();
            if (!$getPlan) {
                // Plan does not exist
                return response()->json([
                    'exists' => false,
                    'message' => 'Plan not found'
                ], 400);
            }

            $metadata = json_encode(
                $array = [
                    'planType' => $validatedData['planType'],
                    'callBackUrl' => $validatedData['callBackUrl'],
                    'userId' => Auth::id(),
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    ]
            );
            $paystackData = [
                    'email' => $email,
                    'amount' => $getPlan->amount * 100, // Amount in kobo
                    'callback_url' => $callback_url,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'planType' => $validatedData['planType'],
                    'metadata' => $metadata,
                ];


            // Get Paystack authorization URL
            $authorizationUrl = Paystack::getAuthorizationUrl($paystackData)->url;

            // Return authorization URL to the React app
            return response()->json(['authorization_url' => $authorizationUrl], 200);
            //return response()->json(['authorization_url' => $authorizationUrl], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to initiate payment. Please try again.', 'error' => $e->getMessage()], 500);
        }
    }

    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        //dd($paymentDetails);

        $paymentDetails = Paystack::getPaymentData();

        $status = (($paymentDetails['status']));
        $message = (($paymentDetails['message']));
        $paid_at = (($paymentDetails['data']['paid_at']));
        $fees = (($paymentDetails['data']['fees'])) / 100;

        $email = (($paymentDetails['data']['customer']['email']));
        $amount_paid = (($paymentDetails['data']['amount'])) / 100;
        $status_response = (($paymentDetails['data']['status']));
        $reference = (($paymentDetails['data']['reference']));
        $ip_address = (($paymentDetails['data']['ip_address']));
        $plan_type = (($paymentDetails['data']['metadata']['planType']));
        $userId = (($paymentDetails['data']['metadata']['userId']));
        $callBackUrl = (($paymentDetails['data']['metadata']['callBackUrl']));
        $customer_code = (($paymentDetails['data']['customer']['customer_code']));
        $gateway_response = $message; // Use payment message as gateway response

        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want

        // Store entries
        $post = new Entries();
        $post->email = $email;
        $post->amount = $amount_paid;
        $post->status = $status_response;
        $post->reference = $reference;
        $post->ip_address = $ip_address;
        $post->save();


        // Get plan type
        $plan_name = $this->getPlanType($plan_type);


        // Get due date
        $date_now = date("Y-m-d");
        if ($plan_type == 23 || $plan_type == 63) {
            $add_date = date('Y-m-d', strtotime("+6 months", strtotime($date_now)));
        } elseif ($plan_type == 25  || $plan_type == 65  ||   $plan_type == 35 || $plan_type == 43) {
            $add_date = date('Y-m-d', strtotime("+1 year", strtotime($date_now)));
        } elseif ($plan_type == 27 || $plan_type == 67) {
            $add_date = date('Y-m-d', strtotime("+3 months", strtotime($date_now)));
        } elseif ($plan_type == 21 || $plan_type == 61 || $plan_type == 41 || $plan_type == 22) {
            $add_date = date('Y-m-d', strtotime("+30 days", strtotime($date_now)));
        } else {
            $add_date = date("Y-m-d");
        }



        // Store payment
        $post = new Payment();
        $post->user_id = $userId;
        $post->ip_address = $ip_address;
        $post->order_id = $customer_code;
        $post->gateway_response = $gateway_response;
        $post->status_response = $status_response;
        $post->reference = $reference;
        $post->amount = $amount_paid;
        $post->plan_id = $plan_type;
        $post->due_date = $add_date;
        $post->status = 'active';
        $post->save();


        if ($callBackUrl) {
            return redirect($callBackUrl.'/?trxref='.$reference);
        } else {
            return response()->json([
        'status' => 'error',
        'message' => 'Callback URL not found',
        'reference' => $reference
            ], 400); // 400 Bad Request
        }

    }

    public function paymentReference($reference)
    {
        $getReference = Payment::where('reference', $reference)->first();

        if ($getReference) {
            $getPlanDetail = Plan::where('track', $getReference->plan_id)->first();

            return response()->json([
                'exists' => true,
                'amount' => $getReference->amount,
                'reference' => $reference,
                'planName' => $getPlanDetail->plan_name,
                'planType' => $getPlanDetail->plan_type,
                'status' => 'Successful',
                'message' => 'Completed',
                'active' => $getReference->status,
            ], 200);
        } else {
            return response()->json([
                'exists' => false,
                'message' => 'An error occured, please contact admin',
            ], 400);
        }
    }

    public function paymentHistory()
    {
        return PaymentResource::collection(
            Payment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10)
        );
    }

    public function paymentStatus()
    {
        $userId = Auth::id();

        $hasActiveSubscription = Payment::where('user_id', $userId)
            ->where('status', 'active')
            ->where('due_date', '>', Carbon::now())
            ->exists();

        if ($hasActiveSubscription) {
            return response()->json([
                'status' => 'active',
                'message' => 'User has an active subscription'
            ], 200);
        } else {
            return response()->json([
                'status' => 'inactive',
                'message' => 'User does not have an active subscription'
            ], 200);
        }
    }

    public function userPaymentHistory(Request $request, $identifier)
    {
        $perPage = $request->input('per_page', 15);
        
        // Check if identifier is numeric (ID) or email
        if (is_numeric($identifier)) {
            // Identifier is an ID
            $user = User::find($identifier);
        } else {
            // Identifier is an email
            $user = User::where('email', $identifier)->first();
        }
        
        // Check if user exists
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        $query = Payment::with('user')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        return PaymentResource::collection($query->paginate($perPage));
    }

    /**
     * List all payments with user details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function allPaymentsWithUsers(Request $request)
    {
        // Handle per_page parameter with validation (min: 1, max: 100, default: 15)
        $perPage = $request->query('per_page', 15);
        $perPage = max(1, min(100, (int) $perPage));
        
        // Handle all parameter for returning all records
        $all = filter_var($request->query('all', false), FILTER_VALIDATE_BOOLEAN);

        $query = Payment::with('user')
            ->orderBy('created_at', 'desc');

        if ($all) {
            return PaymentResource::collection($query->get());
        }

        return PaymentResource::collection($query->paginate($perPage));
    }

    public function paymentsSummary(Request $request)
    {
        // Optional query params
        $userId      = $request->query('user_id');                  // e.g. /api/pay/summary?user_id=42
        $onlySuccess = filter_var($request->query('only_success', 'true'), FILTER_VALIDATE_BOOLEAN);
        $dateField   = $request->query('date_field', 'created_at'); // created_at | updated_at | due_date

        // Whitelist the date field
        $allowedDateFields = ['created_at', 'updated_at', 'due_date'];
        if (!in_array($dateField, $allowedDateFields, true)) {
            $dateField = 'created_at';
        }

        // Periods
        $now = Carbon::now();

        // Month windows
        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd   = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd   = $now->copy()->subMonthNoOverflow()->endOfMonth();

        // Year windows
        $thisYearStart  = $now->copy()->startOfYear();
        $thisYearEnd    = $now->copy()->endOfYear(); // use endOfYear; switch to $now for YTD if you prefer
        $lastYearStart  = $now->copy()->subYearNoOverflow()->startOfYear();
        $lastYearEnd    = $now->copy()->subYearNoOverflow()->endOfYear();

        // Base query with filters (no date window yet)
        $base = Payment::query();

        if ($userId) {
            $base->where('user_id', $userId);
        }

        if ($onlySuccess) {
            // Adjust these semantics to match your gateway statuses
            $base->where(function ($q) {
                $q->where('status', 'active')
                ->orWhere('status_response', 'success')
                ->orWhere('gateway_response', 'successful');
            });
        }

        // ===== Month totals & counts =====
        $thisMonthTotal = (clone $base)
            ->whereBetween($dateField, [$thisMonthStart, $thisMonthEnd])
            ->sum('amount');

        $lastMonthTotal = (clone $base)
            ->whereBetween($dateField, [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $thisMonthCount = (clone $base)
            ->whereBetween($dateField, [$thisMonthStart, $thisMonthEnd])
            ->count();

        $lastMonthCount = (clone $base)
            ->whereBetween($dateField, [$lastMonthStart, $lastMonthEnd])
            ->count();

        // ===== Year totals & counts =====
        $thisYearTotal = (clone $base)
            ->whereBetween($dateField, [$thisYearStart, $thisYearEnd])
            ->sum('amount');

        $lastYearTotal = (clone $base)
            ->whereBetween($dateField, [$lastYearStart, $lastYearEnd])
            ->sum('amount');

        $thisYearCount = (clone $base)
            ->whereBetween($dateField, [$thisYearStart, $thisYearEnd])
            ->count();

        $lastYearCount = (clone $base)
            ->whereBetween($dateField, [$lastYearStart, $lastYearEnd])
            ->count();

        // ===== Subscription (all-time) =====
        $subscriptionTotal = (clone $base)->sum('amount');
        $subscriptionCount = (clone $base)->count();

        // Month deltas
        $monthChangeAbs = (float) $thisMonthTotal - (float) $lastMonthTotal;
        $monthChangePct = ((float) $lastMonthTotal) == 0.0 ? null : round(($monthChangeAbs / (float) $lastMonthTotal) * 100, 2);

        // Year deltas
        $yearChangeAbs = (float) $thisYearTotal - (float) $lastYearTotal;
        $yearChangePct = ((float) $lastYearTotal) == 0.0 ? null : round(($yearChangeAbs / (float) $lastYearTotal) * 100, 2);

        return response()->json([
            'period' => [
                'this_month' => [
                    'start' => $thisMonthStart->toDateTimeString(),
                    'end'   => $thisMonthEnd->toDateTimeString(),
                ],
                'last_month' => [
                    'start' => $lastMonthStart->toDateTimeString(),
                    'end'   => $lastMonthEnd->toDateTimeString(),
                ],
                'this_year' => [
                    'start' => $thisYearStart->toDateTimeString(),
                    'end'   => $thisYearEnd->toDateTimeString(),
                ],
                'last_year' => [
                    'start' => $lastYearStart->toDateTimeString(),
                    'end'   => $lastYearEnd->toDateTimeString(),
                ],
            ],
            'filters' => [
                'user_id'      => $userId,
                'only_success' => $onlySuccess,
                'date_field'   => $dateField,
            ],
            'totals' => [
                // month
                'this_month'       => (float) $thisMonthTotal,
                'last_month'       => (float) $lastMonthTotal,
                'month_change_abs' => (float) $monthChangeAbs,
                'month_change_pct' => $monthChangePct,
                // year
                'this_year'        => (float) $thisYearTotal,
                'last_year'        => (float) $lastYearTotal,
                'year_change_abs'  => (float) $yearChangeAbs,
                'year_change_pct'  => $yearChangePct,
                // all-time
                'subscription_total' => (float) $subscriptionTotal,
            ],
            'counts' => [
                'this_month'         => $thisMonthCount,
                'last_month'         => $lastMonthCount,
                'this_year'          => $thisYearCount,
                'last_year'          => $lastYearCount,
                'subscription_count' => $subscriptionCount,
            ],
        ], 200);
    }


}
