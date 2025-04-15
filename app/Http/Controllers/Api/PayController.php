<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaymentResource;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Entries;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Coupons;
use Paystack;
use Mail;
use App\Services\MailchimpService;


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
                // Add other validation rules as needed
            ]);
             // Get the base URL
            $baseUrl = URL::to('/');
            // Append the desired string to the base URL
            $fullUrl = $baseUrl . '/api/pay/callback/';
            
            $callbackUrl = $validatedData['callBackUrl'];
            $email = Auth::user()->email; // Email passed from the React app
            $amount = $request->input('amount') * 100; // Convert amount to kobo
            $first_name = Auth::user()->first_name;
            $last_name = Auth::user()->last_name;

            $getPlan = Plan::where('track', $validatedData['planType'])->first();

            if (!$getPlan)
            {
                // Plan does not exist
                return response()->json([
                    'exists' => false,
                    'message' => 'Plan not found'
                ], 400);
            }


            // Merge data to send to Paystack
            $request->merge([
                'callback_url' => $fullUrl,
                'email' => $email,
                'amount' => $getPlan->amount * 100,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'planType' => $validatedData['planType'],

            ]);
            $metadata = json_encode(
                $array = [
                    'planType' => $validatedData['planType'],
                    'callBackUrl' => $validatedData['callBackUrl'],
                    'userId' => Auth::id(),
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    ]
                );
            $customer = json_encode(
                $array = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    ]
                );    

            $paystackData = [
                'email' => $email,
                'amount' => $getPlan->amount * 100, // Amount in kobo
                'callback_url' => $fullUrl,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'planType' => $validatedData['planType'],
                'metadata' => $metadata,
            ];
            

           // Get Paystack authorization URL
           $authorizationUrl = Paystack::getAuthorizationUrl($paystackData)->url;

           // Return authorization URL to the React app
           return response()->json(['authorization_url' => $authorizationUrl], 200);
            // Return authorization URL to the React app
             //return response()->json(['authorization_url' => $authorizationUrl], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to initiate payment. Please try again.', 'error' => $e->getMessage()], 500);
        }
    }

    public function handleGatewayCallback()
    {
        $paymentDetails = Paystack::getPaymentData();

        //$paymentDetails = Paystack::getPaymentData();

        $status = (($paymentDetails['status']));
        $message = (($paymentDetails['message']));
        $reference = (($paymentDetails['data']['reference']));
        $amount_paid = (($paymentDetails['data']['amount'])) / 100;
        $gateway_response = (($paymentDetails['data']['gateway_response']));
        $paid_at = (($paymentDetails['data']['paid_at']));
        $ip_address = (($paymentDetails['data']['ip_address']));
        $callBackUrl = (($paymentDetails['data']['metadata']['callBackUrl']));
        $plan_type = (($paymentDetails['data']['metadata']['planType']));
        $userId = (($paymentDetails['data']['metadata']['userId']));
        $status_response = (($paymentDetails['data']['status']));
        $fees = (($paymentDetails['data']['fees'])) / 100;
        $email = (($paymentDetails['data']['customer']['email']));
        $customer_code = (($paymentDetails['data']['customer']['customer_code']));

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

        // Check if user is registered
        

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

        if($callBackUrl)
        {
            return redirect($callBackUrl.'/?trxref='.$reference);

        }else{

        }

        

        
        
        
    }

    public function paymentReference($reference)
    {
        $getReference = Payment::where('reference', $reference)->first();

        if($getReference)
        {
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
        }
        else{
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
            ->orderBy('created_at','desc')
            ->paginate(10));
    }

    public function paymentStatus()
    {
        if (Payment::where('user_id', Auth::id())->where('status', 'active')->exists()) {
            return response()->json([
                'status' => 'active',
                'message' => 'User has an active subscription'
            ], 200);
        } else {
            return response()->json([
                'status' => 'inactive',
                'message' => 'User does not an active subscription'
            ], 200);
        }
    }
}