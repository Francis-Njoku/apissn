<?php


namespace App\Http\Controllers\dash;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mail;
use App\Models\User;
use App\Models\Plan;
use App\Models\Entries;
use App\Models\Payment;
use App\Models\Role;
use App\Models\Newsletter;
use App\Models\NewsType;
use App\Models\DataTrack;
use App\Models\PremiumArticle;
use App\Models\BodyImage;
use App\Models\CategoryUser;
use App\Models\FeaturedImage;
use App\Mail\Unsubscriber;
use App\Mail\CryptoUnsubscriber;
use App\Mail\UnsubscriberContent;
use Illuminate\Support\Facades\Validator;
use Newsletter as NewsletterAPI;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use File;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
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

            return 404;
        }
    }

    private function check_user()
    {
        $user_type = DB::table('users')->where('id', auth()->user()->id)->first();
        abort_if($user_type->role_id != '1', 404);
    }

    // random number generator
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

    private function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public function index()
    {
        $this->check_user();
        return view('dashboard.admin_index');
    }

    public function add_newsletter()
    {
        $this->check_user();
        $users = User::select('id')
            ->get();

        $payment = Payment::select('id')
            ->get();

        $total_amount = Payment::sum('amount');

        $total_active = Payment::where('status', 'active')
            ->get();

        $news_type = NewsType::select('id', 'news')
            ->get();


        return view('dashboard.admin_store_newsletter')->with([
            'users' => $users,
            'payment' => $payment, 'total_amount' => $total_amount, 'total_active' => $total_active,
            'news_type' => $news_type
        ]);
    }

    public function store_newsletter(Request $request)
    {
        $this->check_user();
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'news_type' => 'required',
            'news_date' => 'required'
        ]);

        // Add Premium Newsletter
        if ($request->news_type == 4) {
            /*$this->validate($request, [
                'featured' => 'required',
            ]); */

            $get_ran = $this->random_strings(8);

            $slug = $this->clean($request->title) . '-' . $get_ran;
            if ($request->file('featured')) {
                // Get filename with the extension
                $filenameWithExt = $request->file('featured')->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Clean filename
                $cleanFileName = $this->clean($filename);
                // Get just ext
                $extension = $request->file('featured')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore = $cleanFileName . '_' . time() . '.' . $extension;
                // upload image
                $path = $request->file('featured')->storeAs('public/featured_image', $fileNameToStore);
            } else {
                $fileNameToStore = '';
            }

            $post = new PremiumArticle();
            $post->title = $request->title;
            $post->content = $request->body;
            $post->slug = $slug;
            $post->featured_image = $fileNameToStore;
            $post->status = $request->status;
            $post->save();

            $ip = \Request::ip();

            $post_track = new DataTrack();
            $post_track->ip_address = $ip;
            $post_track->track_id  = '7';
            $post_track->save();

            Session::flash('success', 'Successful');

            return redirect('/admin/articles/');
        }
        // End Premium Newsletter

        // Add Newsletter

        else {

            $this->validate($request, [
                'name' => 'required',
            ]);


            $post = new Newsletter();
            $post->title = $request->title;
            $post->body = $request->body;
            $post->name = $request->name;
            $post->news_type_id = $request->news_type;
            $post->news_date = $request->news_date;
            $post->save();

            $ip = \Request::ip();

            $post_track = new DataTrack();
            $post_track->ip_address = $ip;
            $post_track->track_id  = '4';
            $post_track->save();

            Session::flash('success', 'Successful');

            return redirect('/admin-newsletter-list/');
        }
    }

    public function list_newsletter()
    {
        $this->check_user();

        $get_list = DB::table('newsletter as nw')
            ->join('news_type as nt', 'nt.id', '=', 'nw.news_type_id')
            ->select(
                'nw.id as id',
                'nw.title as title',
                'nw.body as body',
                'nw.news_date as news_date',
                'nt.news as news',
                'nw.status as status'
            )
            ->orderBy('nw.news_date', 'desc')
            ->paginate(16);

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '4';
        $post_track->save();

        return view('dashboard.admin_list_newsletter')->with(['get_list' => $get_list]);
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


        if (count($check_news_exist) <= 0) {
            Session::flash('error', 'Newsletter does not exist');

            return redirect('/admin-newsletter-list/');
        }

        foreach ($check_news_exist as $list) {
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
            ->select(
                'nw.id as id',
                'nw.title as title',
                'nw.name as caption',
                'nw.body as body',
                'nw.status as status',
                'nw.news_date as news_date',
                'nt.news as news'
            )
            ->where('nw.id', $slug)
            ->get();


        if (count($check_news_exist) <= 0) {
            Session::flash('error', 'Newsletter does not exist');

            return redirect('/admin-newsletter-list/');
        }

        $news_type = NewsType::select('id', 'news')
            ->get();

        return view('dashboard.admin_update_newsletter')->with([
            'news' => $check_news_exist,
            'news_type' => $news_type
        ]);
    }

    public function update_newsletter(Request $request)
    {
        $this->check_user();
        $this->validate($request, [
            'id' => 'required',
            'title' => 'required',
            'body' => 'required',
            'news_type' => 'required',
            'status' => 'required'
        ]);

        // Update article
        if ($request->news_type == 4) {
            $check_id_exist = PremiumArticle::select('id')
                ->where('id', $request->id)
                ->get();

            if (count($check_id_exist) < 1) {
                Session::flash('error', 'Article does not exist');

                return redirect('/admin/articles');
            }

            // get random number
            $get_ran = $this->random_strings(8);

            $get_slug =  explode(" ", $request->title);
            $slug = strtolower(implode("-", $get_slug)) . '-' . $get_ran;

            if (($request->featured) != '' && ($request->story_doc) == '') {
                // Get filename with the extension
                $filenameWithExt = $request->file('featured')->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Clean filename
                $cleanFileName = $this->clean($filename);
                // Get just ext
                $extension = $request->file('featured')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore = $cleanFileName . '_' . time() . '.' . $extension;
                // upload image
                $path = $request->file('featured')->storeAs('public/featured_image', $fileNameToStore);

                $post = PremiumArticle::find($request->id);
                $post->title = $request->title;
                $post->content = $request->body;
                $post->slug = $slug;
                $post->featured_image = $fileNameToStore;
                $post->status = $request->status;
                $post->save();
            } else {
                $check_id_exist = PremiumArticle::select('id')
                    ->where('id', $request->id)
                    ->get();

                foreach ($check_id_exist as $get_featured) {
                    if ($get_featured->featured_image == null || $get_featured->featured_image == '') {
                        Session::flash('error', 'Article does not have featured image');

                        return redirect('/admin/articles');
                    }
                }


                $post = PremiumArticle::find($request->id);
                $post->title = $request->title;
                $post->content = $request->body;
                $post->slug = $slug;
                $post->status = $request->status;
                $post->save();
            }


            $ip = \Request::ip();

            $post_track = new DataTrack();
            $post_track->ip_address = $ip;
            $post_track->track_id  = '7';
            $post_track->save();

            Session::flash('success', 'Successful');

            return redirect('/admin/articles');
        }
        // Update newsletters
        else {
            $this->validate($request, [
                'name' => 'required',
                'news_date' => 'required',
            ]);

            $post = Newsletter::find($request->id);
            $post->title = $request->title;
            $post->body = $request->body;
            $post->name = $request->name;
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
    }

    public function listUsers()
    {
        $this->check_user();

        $get_user = User::orderBy('created_at', 'desc')
            ->paginate(10);

        $url = '/admin/list-users/pagination';

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '4';
        $post_track->save();

        return view('dashboard.admin_list_users')->with(['data' => $get_user, 'url' => $url]);
    }

    public function fetch_data(Request $request)
    {
        $this->check_user();
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $data = DB::table('users')
                ->where('id', 'like', '%' . $query . '%')
                ->orWhere('email', 'like', '%' . $query . '%')
                ->orWhere('last_name', 'like', '%' . $query . '%')
                ->orWhere('first_name', 'like', '%' . $query . '%')
                ->orderBy($sort_by, $sort_type)
                ->paginate(10);
            return view('dashboard.pagination_data')->with(['data' => $data]);
        }
    }


    public function list_subscribers()
    {
        $this->check_user();

        $subscribers = DB::table('payment')
            ->join('users', 'users.id', '=', 'payment.user_id')
            ->join('plan', 'plan.track', '=', 'payment.plan_id')
            ->select(
                'payment.order_id',
                'payment.amount',
                'payment.due_date',
                'plan.plan_name',
                'payment.status',
                'users.email',
                'users.phone',
                'payment.created_at',
                'plan.plan_type'
            )
            ->orderBy('payment.created_at', 'desc')
            ->paginate(10);

        $active_subscribers = DB::table('payment as py')
            ->join('users as us', 'us.id', '=', 'py.user_id')
            ->join('plan as pn', 'pn.track', '=', 'py.plan_id')
            ->select(
                'py.order_id as order_id',
                'py.amount as amount',
                'py.due_date as due_date',
                'py.status as status',
                'us.email as email',
                'pn.plan_type as plan'
            )
            ->where('py.status', 'active')
            ->orderBy('py.created_at', 'desc')
            ->get();

        $inactive_subscribers = DB::table('payment as py')
            ->join('users as us', 'us.id', '=', 'py.user_id')
            ->join('plan as pn', 'pn.track', '=', 'py.plan_id')
            ->select(
                'py.order_id as order_id',
                'py.amount as amount',
                'py.due_date as due_date',
                'py.status as status',
                'us.email as email',
                'pn.plan_type as plan',
                'py.created_at as created_at'
            )
            ->where('py.status', '!=', 'active')
            ->orderBy('py.created_at', 'desc')
            ->get();

        $url = '/admin/list-subscribers/pagination';

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '4';
        $post_track->save();

        return view('dashboard.admin_list_subscribers')->with([
            'data' => $subscribers, 'active' => $active_subscribers,
            'inactive' => $inactive_subscribers, 'url' => $url
        ]);
    }

    public function fetch_subscribers(Request $request)
    {
        $this->check_user();
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $data = DB::table('payment')
                ->join('users', 'users.id', '=', 'payment.user_id')
                ->join('plan', 'plan.track', '=', 'payment.plan_id')
                ->select(
                    'payment.order_id',
                    'payment.amount',
                    'payment.due_date',
                    'plan.plan_name',
                    'payment.status',
                    'users.email',
                    'users.phone',
                    'payment.created_at',
                    'plan.plan_type'
                )
                ->orderBy('payment.created_at', 'desc')
                ->where('users.email', 'like', '%' . $query . '%')
                ->orWhere('payment.amount', 'like', '%' . $query . '%')
                ->orWhere('plan.plan_type', 'like', '%' . $query . '%')
                ->orWhere('payment.status', 'like', '%' . $query . '%')
                ->orWhere('plan.plan_name', 'like', '%' . $query . '%')
                ->orWhere('payment.order_id', 'like', '%' . $query . '%')
                ->orderBy($sort_by, $sort_type)
                ->paginate(10);
            return view('dashboard.subscriber_pagination_data')->with(['data' => $data]);
        }
    }

    private function unsubscriber_email($details, $user_email)
    {
        $data = $details;


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new Unsubscriber($data));
        //echo "Basic Email Sent. Check your inbox.";
    }

    private function crypto_unsubscriber_email($details, $user_email)
    {
        $data = $details;


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new CryptoUnsubscriber($data));
        //echo "Basic Email Sent. Check your inbox.";
    }

    private function unsubscriber_content_email($details, $user_email)
    {
        $data = $details;


        Mail::to($user_email)

            ->bcc('chimauche.njoku@nairametrics.com')->send(new UnsubscriberContent($data));
        //echo "Basic Email Sent. Check your inbox.";
    }

    public function delist_subscriber()
    {
        $this->check_user();

        $subscribers = DB::table('payment as py')
            ->join('users as us', 'us.id', '=', 'py.user_id')
            ->join('plan as pn', 'pn.track', '=', 'py.plan_id')
            ->select(
                'py.id as id',
                'py.order_id',
                'py.amount as amount',
                'py.due_date as due_date',
                'pn.plan_name as plan_name',
                'py.status as status',
                'us.first_name as first_name',
                'us.email as email',
                'py.created_at as created_at',
                'pn.plan_type as plan'
            )
            ->orderBy('py.created_at', 'desc')
            ->get();

        foreach ($subscribers as $subscriber) {
            if ((strtotime(date($subscriber->due_date)) <  strtotime(date("Y-m-d"))) && ($subscriber->status == 'active')) {
                $post = Payment::find($subscriber->id);
                $post->status = 'completed';
                $post->save();

                $data2 = array(
                    'email' => 'ssn@nairametrics.com',
                    'name' => $subscriber->first_name,
                    'subject' => 'Nairametrics Stock Select Newsletter'
                );

                $data3 = array(
                    'email' => 'ssn@nairametrics.com',
                    'name' => $subscriber->first_name,
                    'subject' => 'Nairametrics - What Crypto did Newsletter'
                );

                if ($subscriber->plan_name == "Stocks" || $subscriber->plan_name == "Agrotech") {
                    $this->unsubscriber_email($data2, $subscriber->email);

                    // Add user to newsletter list
                    if (!NewsletterAPI::unsubscribe($subscriber->email)) {
                        Session::flash('error', 'Error occured, contact admin');

                        return redirect('/admin/list-subscribers/');
                    }
                } elseif ($subscriber->plan_name == "Cryptocurrency") {
                    $this->crypto_unsubscriber_email($data3, $subscriber->email);

                    // Add user to newsletter list
                    if (!NewsletterAPI::unsubscribe($subscriber->email)) {
                        Session::flash('error', 'Error occured, contact admin');

                        return redirect('/admin/list-subscribers/');
                    }
                } else {
                    $this->unsubscriber_content_email($data2, $subscriber->email);
                }
            }
        }

        Session::flash('success', 'Successful');

        return redirect('/admin/list-subscribers/');
    }

    public function add_article()
    {
        $this->check_user();
        $users = User::select('id')
            ->get();

        $article = Payment::select('id')
            ->get();

        // All articles
        $all_articles = PremiumArticle::select('id')
            ->get();

        // Approved articles
        $total_approved = Payment::where('status', 'active')
            ->get();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '7';
        $post_track->save();

        return view('dashboard.admin_store_article')->with([
            'users' => $users,
            'all_articles' => $all_articles, 'total_approved' => $total_approved
        ]);
    }

    public function store_article(Request $request)
    {
        $this->check_user();
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
            'featured' => 'required',
            'status' => 'required'
        ]);

        $get_ran = $this->random_strings(8);

        $slug = $this->clean($request->title) . '-' . $get_ran;
        if ($request->file('featured')) {
            // Get filename with the extension
            $filenameWithExt = $request->file('featured')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Clean filename
            $cleanFileName = $this->clean($filename);
            // Get just ext
            $extension = $request->file('featured')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $cleanFileName . '_' . time() . '.' . $extension;
            // upload image
            $path = $request->file('featured')->storeAs('public/featured_image', $fileNameToStore);
        } else {
            $fileNameToStore = 'noimage.jpg';
        }

        /*
        if ($request->file('story_doc')) {
            // Get filename with the extension
            $filename2WithExt = $request->file('story_doc')->getClientOriginalName();
            // Get just filename
            $filename2 = pathinfo($filename2WithExt, PATHINFO_FILENAME);
            // Clean filename
            $cleanFileName2 = $this->clean($filename2);
            // Get just ext
            $extension2 = $request->file('story_doc')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore2 = $cleanFileName2 . '_' . time() . '.' . $extension2;
            // upload image
            $path2 = $request->file('story_doc')->storeAs('public/doc', $fileNameToStore2);
        } else {
            $fileNameToStore2 = 'no-file.pdf';
        } */

        $post = new PremiumArticle();
        $post->title = $request->title;
        $post->content = $request->content;
        $post->slug = $slug;
        $post->featured_image = $fileNameToStore;
        //$post->story_doc = $fileNameToStore2;
        $post->status = $request->status;
        $post->save();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '7';
        $post_track->save();

        Session::flash('success', 'Successful');

        return redirect('/admin/articles/');
    }

    public function addImage(Request $request)
    {
        $this->check_user();

        $validation = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        if ($validation->passes()) {
            $image = $request->file('image');
            $new_name = rand() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $request->file('image')->storeAs('public/body', $new_name);
            //$image->move(public_path('/public/body/'), $new_name);

            $post = new BodyImage();
            $post->image = $new_name;
            $post->save();

            //echo url('');

            Session::flash('success', 'Successful');

            return redirect('/admin/images');
        } else {
            Session::flash('error', 'failed');

            return redirect('/admin/images');
        }
    }
    public function store_image(Request $request)
    {
        $this->check_user();

        $validation = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        if ($validation->passes()) {
            $image = $request->file('image');
            $new_name = rand() . '_' . time() . '.' . $image->getClientOriginalExtension();
            $request->file('image')->storeAs('public/body', $new_name);
            //$image->move(public_path('/public/body/'), $new_name);

            $post = new BodyImage();
            $post->image = $new_name;
            $post->save();

            //echo url('');

            return response()->json([
                'message'   => 'Image Upload Successfully',
                'uploaded_image' => '<img src="/storage/body/' . $new_name . '" class="img-thumbnail" width="300" />',
                'get_url' => '<input type="text" value="' . url('/storage/body/' . $new_name) . '" placeholder="' . url('/storage/body/' . $new_name) . '" id="copyText">',
                'copy_url' => '<button onclick="myCopyFunction()">Copy url</button>',
                'class_name'  => 'alert-success',

            ]);
        } else {
            return response()->json([
                'message'   => $validation->errors()->all(),
                'uploaded_image' => '',
                'class_name'  => 'alert-danger'
            ]);
        }
    }

    public function listImage()
    {
        $this->check_user();

        $image = DB::table('body_image')
            ->orderBy('created_at', 'desc')
            ->paginate(16);

        return view('dashboard.admin_list_image')->with([
            'get_image' => $image,
        ]);
    }

    public function deleteImage($id)
    {

        $image = BodyImage::where('id', $id)->firstorfail()->delete();

        Session::flash('success', 'Successful');

        return redirect('/admin/images');
    }

    public function store_text_ajax(Request $request)
    {
        $this->check_user();

        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {


            $post = new BodyImage();
            $post->image = $request->input('image');
            $post->save();
            return response()->json([
                'status' => 200,
                'message' => 'Added Successfully',
            ]);
        }
    }

    public function store_image2(Request $request)
    {
        $this->check_user();
        $this->validate($request, [
            'image' => 'required',
        ]);

        // get random number
        $get_ran = $this->random_strings(8);

        $get_slug =  explode(" ", $request->title);
        $slug = strtolower(implode("-", $get_slug)) . '-' . $get_ran;


        if ($request->image != '') {
            // Get filename with the extension
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Clean filename
            $cleanFileName = $this->clean($filename);
            // Get just ext
            $extension = $request->file('image')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $cleanFileName . '_' . time() . '.' . $extension;
            // upload image
            $path = $request->file('image')->storeAs('public/body', $fileNameToStore);

            $post = new BodyImage();
            $post->image = $fileNameToStore;
            $post->save();

            $ip = \Request::ip();

            $post_track = new DataTrack();
            $post_track->ip_address = $ip;
            $post_track->track_id  = '4';
            $post_track->save();

            return response()->json([
                'status' => 200,
                'message' => 'Successfully'
            ]);
        } else {
            Session::flash('error', 'No image uploaded');

            $ip = \Request::ip();

            $post_track = new DataTrack();
            $post_track->ip_address = $ip;
            $post_track->track_id  = '4';
            $post_track->save();
            return response()->json(['success' => 'success full']);
        }
    }

    public function list_articles()
    {
        $this->check_user();

        $get_list = DB::table('premium_article')
            ->orderBy('created_at', 'desc')
            ->paginate(16);

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '7';
        $post_track->save();

        return view('dashboard.admin_list_articles')->with(['get_list' => $get_list]);
    }

    public function article($slug)
    {
        $this->check_user();

        $check_article_exist = PremiumArticle::where('slug', $slug)
            ->get();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '7';
        $post_track->save();


        if (count($check_article_exist) <= 0) {
            Session::flash('error', 'Article does not exist');

            return redirect('/admin/articles');
        }


        return view('dashboard.admin_article')->with(['article' => $check_article_exist]);
    }

    public function edit_article($slug)
    {
        $this->check_user();

        /*$check_news_exist = Newsletter::select('id', 'body')
        ->where('id', $slug)
        ->get();*/
        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '7';
        $post_track->save();

        $articles = PremiumArticle::where('id', $slug)
            ->get();


        if (count($articles) <= 0) {
            Session::flash('error', 'Article does not exist');

            return redirect('/admin/articles');
        }

        return view('dashboard.admin_update_article')->with(['articles' => $articles]);
    }

    public function update_article(Request $request)
    {
        $this->check_user();
        $this->validate($request, [
            'id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'status' => 'required'
        ]);

        $check_id_exist = PremiumArticle::select('id')
            ->where('id', $request->id)
            ->get();

        if (count($check_id_exist) < 1) {
            Session::flash('error', 'Article does not exist');

            return redirect('/admin/articles');
        }

        // get random number
        $get_ran = $this->random_strings(8);

        $get_slug =  explode(" ", $request->title);
        $slug = strtolower(implode("-", $get_slug)) . '-' . $get_ran;

        // Check if image and pdf are not empty
        if (($request->featured) != '' && ($request->story_doc) != '') {
            // Get filename with the extension
            $filenameWithExt = $request->file('featured')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Clean filename
            $cleanFileName = $this->clean($filename);
            // Get just ext
            $extension = $request->file('featured')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $cleanFileName . '_' . time() . '.' . $extension;
            // upload image
            $path = $request->file('featured')->storeAs('public/featured_image', $fileNameToStore);

            // Get filename with the extension
            $filename2WithExt = $request->file('story_doc')->getClientOriginalName();
            // Get just filename
            $filename2 = pathinfo($filename2WithExt, PATHINFO_FILENAME);
            // Clean filename
            $cleanFileName2 = $this->clean($filename2);
            // Get just ext
            $extension2 = $request->file('story_doc')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore2 = $cleanFileName . '_' . time() . '.' . $extension2;
            // upload image
            $path2 = $request->file('story_doc')->storeAs('public/doc', $fileNameToStore2);

            $post = PremiumArticle::find($request->id);
            $post->title = $request->title;
            $post->content = $request->content;
            $post->featured_image = $fileNameToStore;
            // $post->story_doc = $fileNameToStore2;
            $post->status = $request->status;
            $post->save();
        } elseif (($request->featured) != '' && ($request->story_doc) == '') {
            // Get filename with the extension
            $filenameWithExt = $request->file('featured')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Clean filename
            $cleanFileName = $this->clean($filename);
            // Get just ext
            $extension = $request->file('featured')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $cleanFileName . '_' . time() . '.' . $extension;
            // upload image
            $path = $request->file('featured')->storeAs('public/featured_image', $fileNameToStore);

            $post = PremiumArticle::find($request->id);
            $post->title = $request->title;
            $post->content = $request->content;
            $post->featured_image = $fileNameToStore;
            $post->status = $request->status;
            $post->save();
        } elseif (($request->featured) == '' && ($request->story_doc) != '') {
            // Get filename with the extension
            $filename2WithExt = $request->file('story_doc')->getClientOriginalName();
            // Get just filename
            $filename2 = pathinfo($filename2WithExt, PATHINFO_FILENAME);
            // Clean filename
            $cleanFileName2 = $this->clean($filename2);
            // Get just ext
            $extension2 = $request->file('story_doc')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore2 = $cleanFileName2 . '_' . time() . '.' . $extension2;
            // upload image
            $path2 = $request->file('story_doc')->storeAs('public/doc', $fileNameToStore2);

            $post = PremiumArticle::find($request->id);
            $post->title = $request->title;
            $post->content = $request->content;
            // $post->story_doc = $fileNameToStore2;
            $post->status = $request->status;
            $post->save();
        } else {
            $post = PremiumArticle::find($request->id);
            $post->title = $request->title;
            $post->content = $request->content;
            $post->status = $request->status;
            $post->save();
        }


        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '7';
        $post_track->save();

        Session::flash('success', 'Successful');

        return redirect('/admin/articles');
    }

    public function addFeaturedImage()
    {
        $this->check_user();
        $users = User::select('id')
            ->get();

        $payment = Payment::select('id')
            ->get();

        $total_amount = Payment::sum('amount');

        $total_active = Payment::where('status', 'active')
            ->get();

        $news_type = NewsType::select('id', 'news')
            ->get();


        return view('dashboard.admin_store_featured_image')->with([
            'users' => $users,
            'payment' => $payment, 'total_amount' => $total_amount, 'total_active' => $total_active,
            'news_type' => $news_type
        ]);
    }

    public function storeFeaturedImage(Request $request)
    {

        $this->check_user();
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'news_type' => 'required',
            'news_date' => 'required'
        ]);

        $post = new Newsletter();
        $post->title = $request->title;
        $post->body = $request->body;
        $post->news_type_id = $request->news_type;
        $post->news_date = $request->news_date;
        $post->save();

        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '4';
        $post_track->save();

        Session::flash('success', 'Successful');

        return redirect('/admin-stock-add/');
    }
}
