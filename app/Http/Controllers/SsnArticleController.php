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
use App\Models\Newsletter;
use App\Models\Payment;
use App\Models\PremiumArticle;
use App\Mail\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;

class SsnArticleController extends Controller
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

    public function newsletter()
    {
        $article = DB::table('newsletter')
        ->where('status', 'approved')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        $header = "Stock Select | Nairametrics";
        $og_url = '/ssn';
        $title = "Stock Select | Nairametrics";
        $description = "Stock Select | Nairametrics Stocks";
        
        $url = '/ssn-pagination';

        return view('ssn.ssn')->with(['title'=> $title, 'description' => $description,
        'og_url' => $og_url, 'header' => $header, 'article' => $article, 'url' => $url]);
    }

    public function fetch_ssn(Request $request)
    {
     if($request->ajax())
     {
         
      $sort_by = $request->get('sortby');
      $sort_type = $request->get('sorttype');
            $query = $request->get('query');
            $query = str_replace(" ", "%", $query);
      $data = DB::table('newsletter')
        ->where('name', 'like', '%'.$query.'%')
        ->where('status', 'approved')
        ->orderBy($sort_by, $sort_type)
        ->paginate(15);              
      return view('ssn.ssn_pagination')->with(['article' => $data]);
     }
    }

}
