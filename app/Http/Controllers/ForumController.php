<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Newsletter as Letter;
use Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\ForumReplies;
use App\Models\ForumTopic;
use App\Models\ForumCategory;
use App\Models\TrackTopic;
use App\Models\CategoryUser;
use App\Models\NewsTrack;
use App\Models\DataTrack;
use App\Models\Payment;
use App\Mail\News;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.index');
    }

    public function plan()
    {
        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.plan');
    }

    public function pricing()
    {
        $header = "Pricing | Nairametrics Stocks";
        $og_url = '/pricing';
        $title = "Pricing | Nairametrics Stocks";
        $description = "Pricing | Nairametrics Stocks";

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.pricing')->with(['title'=> $title, 'description' => $description,
        'og_url' => $og_url, 'header' => $header]);
    }

    public function thank()
    {
        $header = "Thank you | Nairametrics Stocks";
        $og_url = '/';
        $title = "Thank you | Nairametrics Stocks";
        $description = "Terms and condition | Nairametrics Stocks";

        Session::flash('error', 'Error occured, contact admin');

        //$request->session()->flash('error', 'Error occured, contact admin');
        return view('front.thank_you')->with(['title'=> $title, 'description' => $description,
        'og_url' => $og_url, 'header' => $header]);
    }
    public function terms()
    {
        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();

        $header = "Terms and condition | Nairametrics Stocks";
        $og_url = '/disclaimer';
        $title = "Terms and condition | Nairametrics Stocks";
        $description = "Terms and condition | Nairametrics Stocks";

        return view ('front.terms')->with(['title'=> $title, 'description' => $description,
        'og_url' => $og_url, 'header' => $header]);
    }

    private function news_email($details, $user_email, $body) {
        $data = $details;

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new News($data, $body));
        //echo "Basic Email Sent. Check your inbox.";
    }
    public function news_book()
    {
        $description = 'Personally compiled by Ugodre';
        $title = 'Stock Select Newsletter';
        $og_url = '/';
        $header = 'Stock Select Newsletter';

        $get_news = Letter::select('news_date')
        ->where('status', 'approved')
        ->orderBy('news_date', 'desc')
        ->get();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();    


        return view('front/get_newsletter')->with(['title'=> $title, 'description' => $description,
            'og_url' => $og_url, 'get_news' => $get_news, 'header' => $header]);

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
        if(count($get_news) <= 0)
        {
            
        Session::flash('error', 'Newletter for that week not available');
        return redirect('/get-newsletter');
        }

        // check if user subscription is active

        $get_status = DB::table('users as us')
        ->join('payment as py', 'py.user_id', '=', 'us.id')
        ->select('us.email as email', 'us.id as id','us.name as first_name')
        ->where('us.email', $request->mail)
        ->where('py.status', 'active')
        ->get();


         // check if newsletter exist
         if(count($get_status) <= 0)
         {
             
         Session::flash('success', 'Subscribe to get newsletter');
         return redirect('/pricing');
         }

         foreach($get_news as $news)
         {
             $news_id = $news->id;
             $new_body = $news->body;
         }

         foreach($get_status as $status)
         {
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

    public function forum()
    {
        $get_category = DB::table('forum_category as fc')
        ->leftjoin('forum_topic as ft', 'ft.category_id', '=', 'fc.id')
        ->leftjoin('forum_replies as fr', 'ft.id', '=', 'fr.topic_id')
        ->selectRaw('fc.name as category, fc.description as description, 
        count(ft.id) as topic_count, count(fr.id) as reply_count, fc.modify_date as updated_at')
        ->groupBy('fc.name', 'fc.description', 'fc.modify_date')
        ->paginate(15);

        $get_data_list = ForumTopic::inRandomOrder()
            ->where('status', 'active')
            ->limit(4)
            ->get();

        $get_trending_topic = DB::table('forum_topic as ft')
        ->join('forum_category as fc', 'fc.id', '=', 'ft.category_id')
        ->leftjoin('forum_replies as fr', 'ft.id', '=', 'fr.topic_id')
        ->selectRaw('ft.title as title, ft.slug as slug, fc.slug as category, count(fr.id) as reply_count')
        ->groupBy('ft.title', 'ft.slug', 'fc.slug') 
        ->orderBy('reply_count', 'desc')
        ->limit(4)
        ->get();

        $get_latest_topic = DB::table('forum_topic as ft')
        ->join('forum_category as fc', 'fc.id', '=', 'ft.category_id')
        ->selectRaw('ft.title as title, ft.slug as slug, fc.slug as category')
        ->groupBy('ft.title', 'ft.slug', 'fc.slug') 
        ->orderBy('ft.created_at', 'desc')
        ->limit(4)
        ->get();

        $header = "Forum | Nairametrics Stocks";
        $og_url = '/forum';
        $title = "Forum | Nairametrics Stocks";
        $description = "Stocks Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        return view('forum.home')->with(['title'=> $title, 'description' => $description,
        'og_url' => $og_url, 'header' => $header, 'get_category' => $get_category, 
        'get_trending_topic' => $get_trending_topic, 'get_latest_topic' => $get_latest_topic]);
    }

}
