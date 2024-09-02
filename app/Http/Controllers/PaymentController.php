<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Entries;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Coupons;
use Paystack;
//use Newsletter;
use Mail;
//use Newsletter;
use Spatie\Newsletter\Facades\Newsletter;
use Illuminate\Support\Facades\Redirect;
use App\Mail\Agrotech;
use App\Mail\Subscriber;
use App\Mail\CryptoSubscriber;
use App\Mail\PremiumContentSubscriber;
use App\Services\MailchimpService;


class PaymentController extends Controller
{
    protected $mailchimp;

    public function __construct()
    {
        $this->middleware('auth');
       // $this->mailchimp = $mailchimp;
        
    }

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

    private function getCoupon($id)
    {
        $strconvert = strtolower($id);
        $find_coupon = Coupons::select('id')
            ->where('name', $id)
            ->get();

        if (count($find_coupon) == 1) {
            foreach ($find_coupon as $content) {
                $conte = $content->name;
            }
            return $conte;
        } else {
            Session::flash('error', 'Coupon code does not exist');

            abort(404);
        }
    }

    // Stock Subscriber Mail Sender
    private function subscriber_email($details, $user_email)
    {
        $data = $details;


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new Subscriber($data));
        //echo "Basic Email Sent. Check your inbox.";
    }


    // Crypto Subscriber Mail Sender
    private function crypto_subscriber_email($details, $user_email)
    {
        $data = $details;


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new CryptoSubscriber($data));
        //echo "Basic Email Sent. Check your inbox.";
    }

    // Premium Content Subscriber Mail Sender
    private function premium_subscriber_email($details, $user_email)
    {
        $data = $details;


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new PremiumContentSubscriber($data));
        //echo "Basic Email Sent. Check your inbox.";
    }

    // Agrotech Subscriber Mail Sender
    private function agrotech_email($details, $user_email)
    {
        $data = $details;


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new Agrotech($data));
        //echo "Basic Email Sent. Check your inbox.";
    }

    public function index()
    {

        if (Auth::check()) {
            return redirect('/pricing');
        } else {
            return redirect('/register');
        }
    }

    public function coupon()
    {

        if (Auth::check()) {
            return redirect('/coupon');
        } else {
            return redirect('/register');
        }
    }

    private function getUser($email)
    {
        $find_user = User::select('id')
            ->where('email', $email)
            ->get();

        if (count($find_user) == 1) {
            foreach ($find_user as $content) {
                $conte = $content->id;
            }
            return $conte;
        } else {
            Session::flash('error', 'Account not found');

            return 404;
        }
    }

    private function userDetails($email)
    {
        $get_content = User::select('email', 'phone', 'last_name', 'first_name')
            ->where('email', $email)
            ->get();

        return $get_content;
    }

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway()
    {
        $this->middleware('auth');
        try{
            return Paystack::getAuthorizationUrl()->redirectNow();
        }catch(\Exception $e) {
            return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }  
        //return Paystack::getAuthorizationUrl()->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
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
        //$plan_type = (($paymentDetails['data']['metadata']['plan_type']));
        $plan_type = (($paymentDetails['data']['planType']));
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
        $user_id = Auth::id();

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
        $post->user_id = $user_id;
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

        dd($paymentDetails);

        
        // get user details for newsletter subscription
        $news_letter = $this->userDetails($email);

        foreach ($news_letter as $letter) {
            $phone = $letter->phone;
            $first_name = $letter->first_name;
            $last_name = $letter->last_name;
        }


        // if plan name is stocks or agrotech

        if ($plan_name == "Stocks" || $plan_name == "Agrotech" || $plan_name == "Coupon") {
            // Add user to newsletter list
            if (Newsletter::isSubscribed($email)) {
                // Update subscriber
                 \Log::info($first_name);
                Newsletter::subscribeOrUpdate($email, ['FNAME' => $first_name, 'LNAME' => $last_name]);
                $message = 'Your subscription has been updated!';
            } else {
                // Subscribe new user
                \Log::info($first_name);
                \Log::info($last_name);
                //\Log::info("chimauche@gmail.com");
                Newsletter::subscribeOrUpdate($email, ['FNAME' => $first_name, 'LNAME' => $last_name]);
                $message = 'You have been subscribed!';
            }
    
            if (Newsletter::lastActionSucceeded()) {
                \Log::info('Success');
                Session::flash('success', 'added successfully');
                return redirect()->back()->with('success', $message);
            }
            else{
                //\Log::info('failed');
                $error = Newsletter::getLastError();
                \Log::error('Mailchimp error: ' . $error); // Log the error for further inspection
                Session::flash('error', 'Error occured, contact admin');
                return redirect()->back()->with('error', 'Something went wrong. Please try again.');
            }

            

            //Newsletter::subscribeOrUpdate($email, ['FNAME' => $first_name, 'LNAME' => $last_name, 'PHONE' => $phone]);
            /*if (!Newsletter::subscribeOrUpdate($email, ['FNAME' => $first_name, 'LNAME' => $last_name, 'PHONE' => $phone])) {
                Session::flash('error', 'Error occured, contact admin');

                return redirect('/thank-you/');
            }*/

            
            /*if(!$this->mailchimp->subscribeOrUpdateList(env('MAILCHIMP_LIST_ID'), "chima@gmail.com", ['FNAME' => $first_name, 'LNAME' => $last_name, 'PHONE' => $phone]))
            {
                Session::flash('error', 'Error occured, contact admin');

                return redirect('/thank-you/');
            }*/
            /*try {
                $this->mailchimp->subscribeOrUpdateList(env('MAILCHIMP_LIST_ID'), "chima@gmail.com", ['FNAME' => $first_name, 'LNAME' => $last_name, 'PHONE' => $phone]);
                //return redirect('/thank-you/');
                echo "Chima";

                //return response()->json(['message' => 'Subscribed/Updated successfully']);
            } catch (\Exception $e) {
                Session::flash('error', 'Error occured, contact admin');
                //return response()->json(['error' => $e->getMessage()], 400);
            }*/



            

            $data2 = array(
                'email' => 'newsletter@nairametrics.com',
                'name' => $first_name,
                'subject' => 'Nairametrics Stock Select Newsletter'
            );

            $this->subscriber_email($data2, $email);

            return redirect('/thank-you/');
        } elseif ($plan_name == "Premium Content") {
            $data2 = array(
                'email' => 'ssn@nairametrics.com',
                'name' => $first_name,
                'subject' => 'Nairametrics Premium Content Subscription'
            );

            $this->premium_subscriber_email($data2, $email);

            return redirect('/premium-article/');
        } elseif ($plan_name == "Cryptocurrency") {
            // Add user to newsletter list
            if (!Newsletter::subscribeOrUpdate($email, ['FNAME' => $first_name, 'LNAME' => $last_name, 'PHONE' => $phone])) {
                Session::flash('error', 'Error occured, contact admin');

                return redirect('/thank-you/');
            }

            $data2 = array(
                'email' => 'ssn@nairametrics.com',
                'name' => $first_name,
                'subject' => 'What Crypto did Newsletter'
            );

            $this->crypto_subscriber_email($data2, $email);

            return redirect('/thank-you/');
        }
    }
}