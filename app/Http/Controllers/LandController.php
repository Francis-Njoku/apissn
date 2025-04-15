<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Newsletter as Letter;
use Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\FeaturedImage;
use App\Models\NewsTrack;
use App\Models\DataTrack;
use App\Models\Coupons;
use App\Models\Payment;
use App\Mail\News;
use Illuminate\Http\Request;

class LandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $article = DB::table('premium_article')
            ->where('status', 'approved')
            ->where('type', 2)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        $blurb = DB::table('premium_article')
            ->where('status', 'approved')
            ->where('type', 5)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();


        $header = "Stock Select | Nairametrics";
        $og_url = '/';
        $title = "Nairametrics Stocks";
        $description = "Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.v4_index')->with([
            'header' => $header, 'og_url' => $og_url, 'article' => $article,
            'title' => $title, 'description' => $description, 'blurb' => $blurb
        ]);
    }

    public function index_v5()
    {
        $article = DB::table('premium_article')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->offset(10)
            ->limit(3)
            ->get();


        $header = "Stock Select | Nairametrics";
        $og_url = '/';
        $title = "Nairametrics Stocks";
        $description = "Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.v5_index')->with([
            'header' => $header, 'og_url' => $og_url, 'article' => $article,
            'title' => $title, 'description' => $description
        ]);
    }

    public function plan()
    {
        $header = "Pricing | Nairametrics";
        $og_url = '/pricing';
        $title = "Pricing | Nairametrics Stocks";
        $description = "Pricing - Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.plan')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);;
    }

    public function pricing()
    {
        $header = "Pricing | Nairametrics";
        $og_url = '/pricing';
        $title = "Pricing | Nairametrics Stocks";
        $description = "Pricing - Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.pricing')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);;
    }

    public function premium_article_pricing()
    {
        $header = "Premium Content Pricing | Nairametrics";
        $og_url = '/pricing';
        $title = "Premium Content Pricing | Nairametrics Stocks";
        $description = "Pricing - Premium Nairametrics Article Finance Agrotech Markets Bonds Dividends | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.premium_article_pricing')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);;
    }

    public function coupon()
    {
        $header = "stock select coupon | Nairametrics";
        $og_url = '/coupon';
        $title = "Stock Select Coupon | Nairametrics Stocks";
        $description = "Coupon - Premium Nairametrics Article Finance Agrotech Markets Bonds Dividends | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.coupon')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);;
    }

    public function stock_pricing()
    {
        $header = "Pricing | Nairametrics";
        $og_url = '/pricing';
        $title = "Pricing | Nairametrics Stocks";
        $description = "Pricing - Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.stock_pricing')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);
    }

    public function get_coupon(Request $request)
    {
        $this->validate($request, [
            'coupon' => 'required',

        ]);

        $strconvert = strtolower($request->coupon);

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        $get_news = Coupons::select('id', 'name', 'amount')
            ->where('name', $strconvert)
            ->where('status', 'active')
            ->get();

        // check if newsletter exist
        if (count($get_news) <= 0) {

            Session::flash('error', 'Coupon code not available');
            return redirect('/coupon');
        } else {
            foreach ($get_news as $news) {
                $coupon_id = $news->id;
            }
            return redirect('/coupon/' . $coupon_id);
        }
    }
    public function coupon_id($slug)
    {
        $get_data = Coupons::select('name', 'id', 'amount')
            ->where('id', $slug)
            ->where('status', 'active')
            ->get();

        if (count($get_data) != 1) {
            Session::flash('success', 'Coupon code not available');
            return redirect('/coupon');
        }


        $header = "Pricing - Coupon | Nairametrics";
        $og_url = '/coupon/' . $slug;
        $title = "Pricing | Nairametrics Stocks";
        $description = "Pricing - Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '18';
        $post_track->save();
        return view('front.coupon_pricing')->with([
            'header' => $header, 'og_url' => $og_url, 'get_data' => $get_data,
            'title' => $title, 'description' => $description
        ]);
    }


    public function crypto_pricing()
    {
        $header = "Pricing | Nairametrics";
        $og_url = '/pricing';
        $title = "Pricing | Cryptocurrency - What Crypto did";
        $description = "Pricing - Cryptocurreny | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.crypto_pricing')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);;
    }

    public function agrotech_pricing()
    {
        $header = "Agrotech Pricing | Nairametrics";
        $og_url = '/pricing';
        $title = "Agrotech Pricing | Nairametrics Stocks";
        $description = "Agrotech Pricing - Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.agrotech_pricing')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);;
    }

    public function thank()
    {
        $header = "Thank you | Nairametrics";
        $og_url = '/thank-you';
        $title = "Thank you | Nairametrics Stocks";
        $description = "Thank you - Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        Session::flash('error', 'Error occured, contact admin');

        //$request->session()->flash('error', 'Error occured, contact admin');
        return view('front.thank_you')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);;
    }
    public function terms()
    {
        $header = "Terms and Disclaimer | Nairametrics";
        $og_url = '/disclaimer';
        $title = "Terms and Disclaimer | Nairametrics Stocks";
        $description = "Terms and Disclaimer - Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.terms')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);;
    }

    public function privacy()
    {
        $header = "Privacy Policy | Nairametrics";
        $og_url = '/privacy-policy';
        $title = "Privacy Policy | Nairametrics Stocks";
        $description = "Privacy Policy - Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.privacy')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);;
    }

    private function news_email($details, $user_email, $body)
    {
        $data = $details;

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '1';
        $post_track->save();


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new News($data, $body));
        //echo "Basic Email Sent. Check your inbox.";
    }
    public function news_book()
    {
        $description = 'Personally compiled by Ugodre';
        $title = 'Stock Select Newsletter';
        $og_url = '/get-newsletter';
        $header = 'Personally compiled by Ugodre';
        $og_image = '/asset/news_book/img/newsletter.jpg';
        $css = 'coming-soon.min.css';

        $get_news = Letter::select('news_date', 'name')
            ->where('status', 'approved')
            ->where('news_type_id', '1')
            ->orderBy('news_date', 'desc')
            ->get();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '1';
        $post_track->save();


        return view('front/get_newsletter')->with([
            'title' => $title, 'description' => $description,
            'og_url' => $og_url, 'get_news' => $get_news, 'header' => $header, 'og_image' => $og_image,
            'css' => $css
        ]);
    }

    public function news_single_newsletter($slug)
    {
        $get_data = Letter::select('name')
            ->where('id', $slug)
            ->where('news_type_id', '1')
            ->get();

        if (count($get_data) != 1) {
            Session::flash('success', 'Newletter for that week not available');
            return redirect('/get-newsletter');
        }


        $description = 'Personally compiled by Ugodre';
        $title = 'Stock Select Newsletter';
        $og_url = '/get-newsletter/' . $slug;
        $header = 'Personally compiled by Ugodre';
        $og_image = '/asset/news_book/img/newsletter.jpg';
        $css = 'coming-soon.min.css';

        $get_news = Letter::select('news_date', 'name', 'title')
            ->where('status', 'approved')
            ->where('id', $slug)
            ->where('news_type_id', '1')
            ->orderBy('news_date', 'desc')
            ->get();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '1';
        $post_track->save();


        return view('front/get_single_newsletter')->with([
            'title' => $title, 'description' => $description,
            'og_url' => $og_url, 'get_news' => $get_news, 'header' => $header, 'og_image' => $og_image,
            'css' => $css
        ]);
    }

    public function news_agrotech()
    {
        $description = 'Monthly Recommendations on Agro-Tech investments';
        $title = 'Agrotech Newsletter | Nairametrics';
        $og_url = '/get-agrotech-newsletter';
        $header = 'Agrotech Newsletter | Nairametrics';
        $og_image = '/asset/news_book/img/agrotech-bg.jpg';
        $css = 'coming-soon2.min.css';

        $get_news = Letter::select('news_date')
            ->where('status', 'approved')
            ->where('news_type_id', '2')
            ->orderBy('news_date', 'desc')
            ->get();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '1';
        $post_track->save();


        return view('front/agrotech_newsletter')->with([
            'title' => $title, 'description' => $description,
            'og_url' => $og_url, 'get_news' => $get_news, 'header' => $header, 'og_image' => $og_image,
            'css' => $css
        ]);
    }

    public function news_crypto()
    {
        $description = 'Bi-weekly Recommendations on Cryptocurrency';
        $title = 'Cryptocurrency Newsletter | Nairametrics';
        $og_url = '/get-crypto-newsletter';
        $header = 'Crypto Newsletter | Nairametrics';
        $og_image = '/asset/news_book/img/crypto-back.png';
        $css = 'coming-soon3.min.css';

        $get_news = Letter::select('news_date')
            ->where('status', 'approved')
            ->where('news_type_id', '3')
            ->orderBy('news_date', 'desc')
            ->get();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '1';
        $post_track->save();


        return view('front/crypto_newsletter')->with([
            'title' => $title, 'description' => $description,
            'og_url' => $og_url, 'get_news' => $get_news, 'header' => $header, 'og_image' => $og_image,
            'css' => $css
        ]);
    }

    public function send_news_book(Request $request)
    {
        $this->validate($request, [
            'mail' => 'required',
            'name' => 'required',

        ]);

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        $get_news = Letter::select('title', 'body', 'id')
            ->where('news_date', $request->name)
            ->where('status', 'approved')
            ->limit(10)
            ->get();

        // check if newsletter exist
        if (count($get_news) <= 0) {

            Session::flash('error', 'Newletter for that week not available');
            return redirect('/get-newsletter');
        }

        // check if user subscription is active

        $get_status = DB::table('users as us')
            ->join('payment as py', 'py.user_id', '=', 'us.id')
            ->select('us.email as email', 'us.id as id', 'us.name as first_name')
            ->where('us.email', $request->mail)
            ->where('py.status', 'active')
            ->get();


        // check if newsletter exist
        if (count($get_status) <= 0) {

            Session::flash('success', 'Subscribe to get newsletter');
            return redirect('/pricing');
        }

        foreach ($get_news as $news) {
            $news_id = $news->id;
            $new_body = $news->body;
        }

        foreach ($get_status as $status) {
            $user_id = $status->id;
            $first_name = $status->first_name;
        }

        $data = array(
            'email' => 'ssn@nairametrics.com',
            'name' => $first_name,
            'subject' => 'Download Nairametrics Stock Select Newsletter'

        );


        $ip = \Request::ip();
        $post = new NewsTrack();
        $post->ip_address = $ip;
        $post->email_id = $user_id;
        $post->newsletter_id = $news_id;
        $post->save();
        $this->news_email($data, $request->mail, $new_body);
        //$this->basic_email();

        Session::flash('success2', 'Successful! Kindly check your mail inbox or spam folder');
        return redirect('/get-newsletter');
    }


    public function send_crypto_book(Request $request)
    {
        $this->validate($request, [
            'mail' => 'required',
            'name' => 'required',

        ]);

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        $get_news = Letter::select('title', 'body', 'id')
            ->where('news_date', $request->name)
            ->where('status', 'approved')
            ->limit(10)
            ->get();

        // check if newsletter exist
        if (count($get_news) <= 0) {

            Session::flash('error', 'Newletter for that week not available');
            return redirect('/get-crypto-newsletter');
        }

        // check if user subscription is active

        $get_status = DB::table('users as us')
            ->join('payment as py', 'py.user_id', '=', 'us.id')
            ->select('us.email as email', 'us.id as id', 'us.name as first_name')
            ->where('us.email', $request->mail)
            ->where('py.status', 'active')
            ->get();


        // check if newsletter exist
        if (count($get_status) <= 0) {

            Session::flash('success', 'Subscribe to get newsletter');
            return redirect('/crypto/pricing');
        }

        foreach ($get_news as $news) {
            $news_id = $news->id;
            $new_body = $news->body;
        }

        foreach ($get_status as $status) {
            $user_id = $status->id;
            $first_name = $status->first_name;
        }

        $data = array(
            'email' => 'ssn@nairametrics.com',
            'name' => $first_name,
            'subject' => 'Download Nairametrics What Crypto did Newsletter'

        );


        $ip = \Request::ip();
        $post = new NewsTrack();
        $post->ip_address = $ip;
        $post->email_id = $user_id;
        $post->newsletter_id = $news_id;
        $post->save();
        $this->news_email($data, $request->mail, $new_body);
        //$this->basic_email();

        Session::flash('success2', 'Successful! Kindly check your mail inbox or spam folder');
        return redirect('/get-crypto-newsletter');
    }

    public function thank_you()
    {
        $header = "Thanks for subscribing | Nairametrics";
        $og_url = '/pricing';
        $title = "Thanks for subscribing | Nairametrics Stocks";
        $description = "Thanks for subscribing - Stock Select|Crypto | Finance | Agrotech | Markets | Bonds | Dividends | Forum | Nairametrics Stocks";
        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.thank_you')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description
        ]);;
    }
}
