<?php

namespace App\Http\Controllers\dash;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Entries;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Newsletter;
use App\Models\NewsType;
use App\Models\DataTrack;
use App\Mail\Unsubscriber;
use App\Models\ForumReplies;
use App\Models\ForumTopic;
use App\Models\ForumCategory;
use App\Models\TrackTopic;
use App\Models\CategoryUser;
use Newsletter as NewsletterAPI;
use Illuminate\Support\Facades\Auth;


class AdminForumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function check_user()
    {
        $user_type = DB::table('users')->where('id', auth()->user()->id)->first();
        abort_if($user_type->role_id != '1', 404);
    }


    public function index()
    {
        $this->check_user();
        return view('dashboard.admin_index');
    }


    public function newsletter($slug)
    {
        $this->check_user();

        $check_news_exist = Newsletter::select('id', 'body')
        ->where('id', $slug)
        ->get();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '4';
        $post_track->save();


        if(count($check_news_exist) <= 0)
        {
            Session::flash('error', 'Newsletter does not exist');

            return redirect('/admin-newsletter-list/');
        }

        foreach($check_news_exist as $list)
        {
            $news = $list->body;
        }

        return view('dashboard.admin_newsletter')->with(['body' => $news]);
    }

    public function edit_newsletter($slug)
    {
        $this->check_user();

        /*$check_news_exist = Newsletter::select('id', 'body')
        ->where('id', $slug)
        ->get();*/
        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '4';
        $post_track->save();

        $check_news_exist = DB::table('newsletter as nw')
        ->join('news_type as nt', 'nt.id', '=', 'nw.news_type_id')
        ->select('nw.id as id','nw.title as title', 'nw.body as body', 'nw.status as status', 
                'nw.news_date as news_date', 'nt.news as news')
        ->where('nw.id', $slug)
        ->get();


        if(count($check_news_exist) <= 0)
        {
            Session::flash('error', 'Newsletter does not exist');

            return redirect('/admin-newsletter-list/');
        }

        $news_type = NewsType::select('id', 'news')
        ->get();

        return view('dashboard.admin_update_newsletter')->with(['news' => $check_news_exist, 
        'news_type' => $news_type]);
    }

    public function update_newsletter(Request $request)
    {
        $this->check_user();
        $this->validate($request, [
            'id' => 'required',
            'title' => 'required',
            'body' => 'required',
            'news_type' => 'required',
            'news_date' => 'required',
            'status' => 'required'
        ]);

        $post = Newsletter::find($request->id);
        $post->title = $request->title;
        $post->body = $request->body;
        $post->news_type_id = $request->news_type;
        $post->news_date = $request->news_date;
        $post->status = $request->status;
        $post->save();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '4';
        $post_track->save();

        Session::flash('success', 'Successful');

        return redirect('/admin-newsletter-list/');
    }

    public function category()
    {
        $this->check_user();

        $category = DB::table('forum_category as fc')
        ->leftjoin('forum_topic as ft', 'ft.category_id', '=', 'fc.id')
        ->leftjoin('forum_replies as fr', 'fr.topic_id', '=', 'ft.id')
        ->leftjoin('track_topic as tt', 'tt.topic_id', '=', 'ft.id')
        ->selectRaw('fc.id as id, fc.name as category, fc.description as description, 
        count(ft.id) as topic_count, count(fr.id) as reply_count, fc.status as status,
        count(tt.id) as track_count')
        ->groupBy('fc.name', 'fc.id','fc.description', 'fc.status')
        ->paginate(10);


        $count_topic = ForumTopic::select('id')
        ->get();

        $count_category = ForumCategory::select('id')
        ->get();

        $url = '/admin/forum/category/pagination';

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        return view('dashboard.admin_forum_category')->with(['data' => $category, 'url' => $url, 'count_topic' => $count_topic, 
        'count_category' => $count_category]);
        
    }

    public function fetch_category(Request $request)
    {
        $this->check_user();
     if($request->ajax())
     {
      $sort_by = $request->get('sortby');
      $sort_type = $request->get('sorttype'); 
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
      $data = DB::table('forum_category as fc')
      ->leftjoin('forum_topic as ft', 'ft.category_id', '=', 'fc.id')
      ->leftjoin('forum_replies as fr', 'fr.topic_id', '=', 'ft.id')
      ->leftjoin('track_topic as tt', 'tt.topic_id', '=', 'ft.id')
      ->selectRaw('fc.name as category, fc.id as id, count(ft.id) as topic_count, count(fr.id) as reply_count, 
      count(tt.id) as track_count, fc.description as description, fc.status as status')
        ->where('fc.name', 'like', '%'.$query.'%')
        ->orderBy($sort_by, $sort_type)
        ->groupBy('fc.name', 'fc.id','fc.description', 'fc.status')
        ->paginate(10);
        
        
      return view('dashboard.admin_forum_pagination_data')->with(['data' => $data]);
     }
    }

    // store category
    public function store_category(Request $request)
    {
        $this->check_user();
        $this->validate($request, [
            'category' => 'required',
            'status' => 'required',
            'description' => 'required'
        ]);

        $ip = \Request::ip();

        // Track users
        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        $get_slug =  explode(" ",$request->category);
        $slug = strtolower(implode("-",$get_slug));

        $post = new ForumCategory();
        $post->description = $request->description;
        $post->name = $request->category;
        $post->slug = $slug;
        $post->modify_date = date('Y-m-d');
        $post->status = $request->status;
        $post->save();

        
        Session::flash('success', 'Successful');

        return redirect('/admin/forum/category/');
    }

    // update category
    public function update_category(Request $request)
    {
        $this->check_user();
        $this->validate($request, [
            'category' => 'required',
            'status' => 'required',
            'id' => 'required',
            'description' => 'required'
        ]);

        $get_slug =  explode(" ",$request->category);
        $slug = strtolower(implode("-",$get_slug));

        $ip = \Request::ip();

        // Track users
        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        $post = ForumCategory::find($request->id);
        $post->description = $request->description;
        $post->name = $request->category;
        $post->slug = $slug;
        $post->modify_date = date('Y-m-d');
        $post->status = $request->status;
        $post->save();

        
        Session::flash('success', 'Successful');

        return redirect('/admin/forum/category/');
    }


    // Topic
    private function random_strings($length_of_string) 
    { 
  
        // String of all alphanumeric character 
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
  
        // Shufle the $str_result and returns substring 
        // of specified length 
        return substr(str_shuffle($str_result),  
                       0, $length_of_string); 
    }

    private function forum_create_email($details, $user_email) {
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

        return $get_moderator;
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
        $og_url = '/admin/forum/create-topic';
        $get_route = 'admin-store-topic';
        $title = "Create Forum | Nairametrics Stocks";
        $description = "Stocks Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";
        
        return view('forum.create_topic')->with(['users' => $users,
        'forums' => $forums, 'topics' => $topics, 'header' => $header, 
        'og_url' => $og_url, 'title' => $title, 'description' => $description, 
        'get_route' => $get_route]);

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

        $get_slug =  explode(" ",$request->topic);
        $slug = strtolower(implode("-",$get_slug)).'-'.$get_ran;

        // check if category exist
        $forums = ForumCategory::where('id', $request->category)
        ->get();

        if(count($forums) < 1)
        {
            Session::flash('error', 'Category does not exist');

            return redirect('/forum/create-topic/');
        }

        foreach($forums as $forum)
        {
            $forum_slug = $forum->slug;
        }


        
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

        return redirect('/forum/'.$forum_slug.'/'.$slug);
        
        
    }

    // Edit topic
    public function edit_topic($id)
    {
        $users = User::select('id')
        ->get();

        $forums = ForumCategory::where('status', 'active')
        ->get();

        $topics = ForumTopic::where('status', 'active')
        ->get();

        $get_topic = DB::table('forum_topic as ft')
        ->join('forum_category as fc', 'fc.id', '=', 'ft.category_id')
        ->select('ft.title as title', 'ft.id as id', 'ft.description as description', 'ft.status as status', 
        'fc.name as category', 'fc.id as cat_id')
        ->where('ft.id', $id)
        ->get();

        if (count($get_topic) < 1)
        {
            Session::flash('error', 'Topic does not exist');

            return redirect('/admin/forum/topics/');
        }

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        $header = "Edit Topic  | Nairametrics Stocks";
        $og_url = '/admin/forum/edit-topic/'.$id;
        $get_route = 'admin-update-topic';
        $title = "Edit Topic | Nairametrics Stocks";
        $description = "Stocks Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";
        
        return view('forum.edit_topic')->with(['users' => $users,
        'forums' => $forums, 'topics' => $topics, 'header' => $header, 
        'og_url' => $og_url, 'title' => $title, 'description' => $description, 
        'get_route' => $get_route, 'get_topic' => $get_topic]);

    }

    // update category
    public function update_topic(Request $request)
    {
        $this->check_user();
        $this->validate($request, [
            'category' => 'required',
            'status' => 'required',
            'id' => 'required',
            'topic' => 'required',
            'description' => 'required'
        ]);

        $get_slug =  explode(" ",$request->topic);
        $slug = strtolower(implode("-",$get_slug));

        $ip = \Request::ip();

        // Track users
        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        $post = ForumTopic::find($request->id);
        $post->description = $request->description;
        $post->category_id = $request->category;
        $post->title = $request->topic;
        $post->slug = $slug;
        $post->status = $request->status;
        $post->save();

        // Update Category latest changes
        $post = ForumCategory::find($request->category);
        $post->modify_date = date('Y-m-d');
        $post->save();

        
        Session::flash('success2', 'Successful');

        return redirect('/admin/forum/topics/');
    }


    public function topic($category, $slug){

        // check if category exist
        $forums = ForumCategory::where('slug', $category)
        ->get();

        if(count($forums) < 1)
        {
            Session::flash('error', 'Category does not exist');

            return redirect('/forum/create-topic/');
        }

        // check if topic exist
        $topics = ForumTopic::where('slug', $slug)
        ->get();

        if(count($topics) < 1)
        {
            Session::flash('error', 'Topic does not exist');

            return redirect('/forum/create-topic/');
        }

        foreach($forums as $forum)
        {
            $forum_title = $forum->name;
        }

        foreach($topics as $topic)
        {
            $topic_title = $topic->title;
            $topic_id = $topic->id;
            $topic_description = $topic->description;
            $topic_date = $topic->created_at;
            $user_id = $topic->user_id;
        }

        // check if User exist
        $users = User::where('id', $user_id)
        ->get();

        if(count($users) < 1)
        {
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

        

        echo $category.' and '.$slug;
    }

    public function topics()
    {
        $this->check_user();

        $category = DB::table('forum_topic as ft')
        ->leftjoin('forum_category as fc', 'fc.id', '=', 'ft.category_id')
        ->leftjoin('forum_replies as fr', 'fr.topic_id', '=', 'ft.id')
        ->selectRaw('ft.id as id, ft.title as title, ft.description as description, 
        count(fr.id) as reply_count, ft.status as status, fc.name as category')
        ->groupBy('ft.id','ft.title', 'fc.name','ft.description', 'ft.status')
        ->paginate(10);


        $count_topic = ForumTopic::select('id')
        ->get();

        $count_category = ForumCategory::select('id')
        ->get();

        $url = '/admin/topic/pagination';

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '5';
        $post_track->save();

        return view('dashboard.admin_forum_topics')->with(['data' => $category, 'url' => $url, 'count_topic' => $count_topic, 
        'count_category' => $count_category]);
        
    }

    public function fetch_topics(Request $request)
    {
        $this->check_user();
     if($request->ajax())
     {
      $sort_by = $request->get('sortby');
      $sort_type = $request->get('sorttype'); 
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
      $data = DB::table('forum_topic as ft')
      ->leftjoin('forum_category as fc', 'fc.id', '=', 'ft.category_id')
      ->leftjoin('forum_replies as fr', 'fr.topic_id', '=', 'ft.id')
      ->selectRaw('ft.id as id, ft.title as title, ft.description as description, 
      count(fr.id) as reply_count, ft.status as status, fc.name as category')
        ->where('ft.title', 'like', '%'.$query.'%')
        ->orderBy($sort_by, $sort_type)
        ->groupBy('ft.id', 'ft.title', 'ft.description', 'fc.name', 'ft.status')
        ->paginate(10);
        
        
      return view('dashboard.admin_forum_topics_pagination_data')->with(['data' => $data]);
     }
    }



}
