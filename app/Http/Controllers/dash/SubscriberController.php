<?php

namespace App\Http\Controllers\dash;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\PremiumArticle;
use App\Models\Entries;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Newsletter;
use App\Models\NewsType;
use App\Models\DataTrack;
use App\Mail\Unsubscriber;
use App\Mail\ForumMail;
use App\Models\ForumReplies;
use App\Models\ForumTopic;
use App\Models\ForumCategory;
use App\Models\TrackTopic;
use App\Models\CategoryUser;
use Mail;
use Newsletter as NewsletterAPI;

class SubscriberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function check_user()
    {
        $user_type = DB::table('users')->where('id', auth()->user()->id)->first();
        abort_if($user_type->role_id != '2', 404);
    }

    private function check_subscriber()
    {
        $check = Payment::select('id')
            ->where('user_id', auth()->user()->id)
            ->where('status', 'active')
            ->get();

        if (count($check) < 1) {
            Session::flash('error', 'Subscribe to view article');

            return redirect('/pricing/');
        }
    }

    private function random_strings($length_of_string)
    {

        // String of all alphanumeric character 
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        // Shufle the $str_result and returns substring 
        // of specified length 
        return substr(
            str_shuffle($str_result),
            0,
            $length_of_string
        );
    }

    private function forum_create_email($details, $user_email)
    {
        $data = $details;


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new ForumMail($data));
        //echo "Basic Email Sent. Check your inbox.";
    }

    private function create_topic_mail($details, $user_email)
    {
        $data = $details;


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new Subscriber($data));
        //echo "Basic Email Sent. Check your inbox.";
    }

    private function moderator_details($id)
    {
        $get_moderator = CategoryUser::select('user_id')
            ->where('category_id', $id)
            ->get();

        foreach ($get_moderator as $moderator) {
            $moderator_email = $moderator->user_id;

            $get_user = User::select('email')
                ->where('id', $moderator_email)
                ->get();

            // check if moderator exist
            if (count($get_user) == 1) {
                foreach ($get_user as $user) {
                    $user_email = $user->email;
                }
            } else {
                $user_email = 'outreach@nairametrics.com';
            }

            $data2 = array(
                'email' => 'ssn@nairametrics.com',
                'subject' => 'Nairametrics Stock Select Newsletter'
            );

            $this->forum_create_email($data2, $user_email);
        }
    }


    public function create_topic()
    {
        $users = User::select('id')
            ->get();

        $forums = ForumCategory::where('status', 'active')
            ->get();

        $topics = ForumTopic::where('status', 'active')
            ->get();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        $header = "Create Forum | Nairametrics Stocks";
        $og_url = '/forum/create-topic';
        $title = "Create Forum | Nairametrics Stocks";
        $description = "Stocks Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";
        $get_route = 'store-topic';

        return view('forum.create_topic')->with([
            'users' => $users,
            'forums' => $forums, 'topics' => $topics, 'header' => $header,
            'og_url' => $og_url, 'title' => $title, 'description' => $description,
            'get_route' => $get_route
        ]);
    }

    // Store topic
    public function store_topic(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|between:-1500000000, 10000000000000000',
            'topic' => 'required',
            'description' => 'required'
        ]);

        $get_ran = $this->random_strings(8);

        $get_slug =  explode(" ", $request->topic);
        $slug = strtolower(implode("-", $get_slug)) . '-' . $get_ran;

        // check if category exist
        $forums = ForumCategory::where('id', $request->category)
            ->get();

        if (count($forums) < 1) {
            Session::flash('error', 'Category does not exist');

            return redirect('/forum/create-topic/');
        }

        foreach ($forums as $forum) {
            $forum_slug = $forum->slug;
        }

        // notify moderator
        $this->moderator_details($request->category);

        $ip = \Request::ip();

        // Track users
        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        // Create new topic
        $post = new ForumTopic();
        $post->title = $request->topic;
        $post->description = $request->description;
        $post->slug = $slug;
        $post->ip_address = $ip;
        $post->category_id = $request->category;
        $post->user_id = auth()->user()->id;
        $post->status = 'pending';
        $post->save();

        // Update Category latest changes
        $post = ForumCategory::find($request->category);
        $post->modify_date = date('Y-m-d');
        $post->save();

        Session::flash('success', 'Successful');

        return redirect('/forum/' . $forum_slug . '/' . $slug);
    }

    public function topic($category, $slug)
    {

        // check if category exist
        $forums = ForumCategory::where('slug', $category)
            ->get();

        if (count($forums) < 1) {
            Session::flash('error', 'Category does not exist');

            return redirect('/forum/create-topic/');
        }

        // check if topic exist
        $topics = ForumTopic::where('slug', $slug)
            ->get();

        if (count($topics) < 1) {
            Session::flash('error', 'Topic does not exist');

            return redirect('/forum/create-topic/');
        }

        foreach ($forums as $forum) {
            $forum_title = $forum->name;
        }

        foreach ($topics as $topic) {
            $topic_title = $topic->title;
            $topic_id = $topic->id;
            $topic_description = $topic->description;
            $topic_date = $topic->created_at;
            $user_id = $topic->user_id;
        }

        // check if User exist
        $users = User::where('id', $user_id)
            ->get();

        if (count($users) < 1) {
            Session::flash('error', 'User does not exist');

            return redirect('/forum/create-topic/');
        }

        $ip = \Request::ip();

        // Track users
        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        // Track topic views
        $post_track = new TrackTopic();
        $post_track->ip_address = $ip;
        $post_track->topic_id  = $topic_id;
        $post_track->save();



        echo $category . ' and ' . $slug;
    }


    // View premium article
    public function show_article($slug)
    {
        /*$check = Payment::select('id')
            ->where('user_id', auth()->user()->id)
            ->where('status', 'active')
            ->get();

        if (count($check) < 1) {
            Session::flash('error', 'Subscribe to view article');

            return redirect('/pricing#ssn-premium/');
        } */

        $article = PremiumArticle::where('slug', $slug)
            ->where('status', 'approved')
            ->get();
        //print_r($article);

        if (count($article) < 1) {
            abort(404);
        }

        foreach ($article as $art) {
            $art_title = $art->title;
            echo $art_title;
        }

        $get_random = PremiumArticle::inRandomOrder()
            ->where('status', 'approved')
            ->limit(5)
            ->get();

        $header = $art_title . " | Nairametrics Stocks";
        $og_url = '/article/' . $art_title;
        $title = $art_title . " | Nairametrics Stocks";
        $description = $art_title . " Stocks Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";
        $url = '/article/' . $art_title;

        return view('article.single')->with([
            'title' => $title, 'description' => $description,
            'og_url' => $og_url, 'header' => $header, 'article' => $article, 'url' => $url,
            'article_title' => $art_title, 'get_random' => $get_random
        ]);
    }


    // View premium article
    public function show_ssn($slug)
    {
        $check = Payment::select('id')
            ->where('user_id', auth()->user()->id)
            ->where('status', 'active')
            ->get();

        if (count($check) < 1) {
            Session::flash('error', 'Subscribe to view stock');

            return redirect('/pricing/');
        }

        $article = Newsletter::where('title', $slug)
            ->where('status', 'approved')
            ->get();

        if (count($article) < 1) {
            abort(404);
        }

        foreach ($article as $art) {
            $art_title = $art->title;
        }

        $get_random = Newsletter::inRandomOrder()
            ->where('status', 'approved')
            ->limit(5)
            ->get();


        $header = $art_title . " | Nairametrics Stocks";
        $og_url = '/ssn/' . $art_title;
        $title = $art_title . " | Nairametrics Stocks";
        $description = $art_title . " Stocks Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";
        $url = '/ssn/' . $art_title;
        $user_email = auth()->user()->email;

        return view('ssn.single')->with([
            'title' => $title, 'description' => $description,
            'og_url' => $og_url, 'header' => $header, 'article' => $article, 'url' => $url,
            'article_title' => $art_title, 'email_address' => $user_email, 'get_random' => $get_random
        ]);
    }
}
