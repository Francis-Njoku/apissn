<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Newsletter as Letter;
use Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\NewsTrack;
use App\Models\DataTrack;
use App\Models\Payment;
use App\Models\PremiumArticle;
use App\Mail\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;

class PremiumArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $ip = \Request::ip();

        $post_track = new DataTrack();
        $post_track->ip_address = $ip;
        $post_track->track_id  = '2';
        $post_track->save();
        return view('front.index');
    }

    private function news_email($details, $user_email, $body)
    {
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

    public function article()
    {
        $article = DB::table('premium_article')
            ->where('status', 'approved')
            ->where('type', 2)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $get_random = PremiumArticle::inRandomOrder()
            ->where('status', 'approved')
            ->where('type', 2)
            ->limit(5)
            ->get();

        $header = "Premium Article | Nairametrics";
        $og_url = '/premium-article';
        $title = "Premium Article | Nairametrics";
        $description = "Premium Nairametrics Stocks Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $url = '/premium-article-pagination';

        return view('article.premium')->with([
            'title' => $title, 'description' => $description, 'get_random' => $get_random,
            'og_url' => $og_url, 'header' => $header, 'article' => $article, 'url' => $url
        ]);
    }

    public function fetch_articles(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $data = DB::table('premium_article')
                ->where('title', 'like', '%' . $query . '%')
                ->where('status', 'approved')
                ->where('type', 2)
                ->orderBy($sort_by, $sort_type)
                ->paginate(12);
            return view('article.premium_pagination')->with(['article' => $data]);
        }
    }


    public function blurb()
    {
        $article = DB::table('premium_article')
            ->where('status', 'approved')
            ->where('type', 5)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $get_random = PremiumArticle::inRandomOrder()
            ->where('status', 'approved')
            ->where('type', 5)
            ->limit(5)
            ->get();

        $header = "Blurb | Nairametrics";
        $og_url = '/blurb';
        $title = "Blurb | Nairametrics";
        $description = "Blurb Nairametrics Stocks Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $url = '/blurb-pagination';

        return view('article.blurb')->with([
            'title' => $title, 'description' => $description, 'get_random' => $get_random,
            'og_url' => $og_url, 'header' => $header, 'article' => $article, 'url' => $url
        ]);
    }

    public function fetch_blurb(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $data = DB::table('premium_article')
                ->where('title', 'like', '%' . $query . '%')
                ->where('status', 'approved')
                ->where('type', 5)
                ->orderBy($sort_by, $sort_type)
                ->paginate(12);
            return view('article.blurb_pagination')->with(['article' => $data]);
        }
    }

    public function ssnArticle()
    {
        $article = DB::table('newsletter')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $header = "Stock Select Newsletter | Nairametrics";
        $og_url = '/ssn-list';
        $title = "Stock Select Newsletter | Nairametrics";
        $description = "Stock Select Newsletter | Nairametrics Stocks";

        $url = '/ssn-list-pagination';

        return view('article.premium_ssn')->with([
            'title' => $title, 'description' => $description,
            'og_url' => $og_url, 'header' => $header, 'article' => $article, 'url' => $url
        ]);
    }

    public function fetchSsnArticles(Request $request)
    {
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
            $data = DB::table('newsletter')
                ->where('title', 'like', '%' . $query . '%')
                ->where('name', 'like', '%' . $query . '%')
                ->where('status', 'approved')
                ->orderBy($sort_by, $sort_type)
                ->paginate(12);
            return view('article.premium_ssn_pagination')->with(['article' => $data]);
        }
    }
}
