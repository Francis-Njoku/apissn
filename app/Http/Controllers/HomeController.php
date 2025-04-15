<?php

namespace App\Http\Controllers;

use App\Models\FeaturedImage;
use App\Models\Banner;
use App\Models\DataTrack;
use Illuminate\Http\Request;
use PDF;

class HomeController extends Controller
{


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    function generate_pdf()
    {
        $data = [
            'foo' => 'bar'
        ];
        $body = iconv("UTF-8", "UTF-8//IGNORE", "Chima");

        $pdf = PDF::loadView('emails.agro');
        $pdf->SetProtection(['copy', 'print'], '', 'pass');
        return $pdf->output('document.pdf');
    }

    public function forum()
    {
        return view('front.forum_home');
    }

    public function tiny()
    {
        return view('tiny');
    }

    public function v4()
    {
        return view('front.v4');
    }

    public function testing()
    {

        /*$pdf->allow('AllFeatures')      // Change permissions
            ->flatten()                 // Merge form data into document (doesn't work well with UTF-8!)
            ->compress('88')          // Compress/Uncompress
            ->keepId('first')           // Keep first/last Id of combined files
            ->dropXfa()                 // Drop newer XFA form from PDF
            ->dropXmp()                 // Drop newer XMP data from PDF
            ->needAppearances()         // Make clients create appearance for form fields
            ->setPassword('chima')          // Set owner password
            ->setUserPassword('chima')      // Set user password
            ->passwordEncryption(128)   // Set password encryption strength
            ->saveAs('new.pdf'); */

        $filename = 'pdf' . rand(200, 200000) . '.pdf';

        $pdf = new Pdf('/asset/news_book/doc/SEO_Stats.pdf');
        // Should set password and then send the file to the browser for download
        $pdf->setPassword('Chima')->saveAs($filename);

        return $filename;
        // Check for errors
        /*if (!$pdf->saveAs('my.pdf')) {
                $error = $pdf->getError();
            }*/
    }

    public function featuredImage()
    {
        $header = "Featured | Nairametrics";
        $og_url = '/top-banner';
        $title = "Terms and Disclaimer | Nairametrics Stocks";
        $description = "Terms and Disclaimer - Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $get_image = FeaturedImage::inRandomOrder()
            ->where('status', 'active')
            ->limit(1)
            ->get();

        $ip = \Request::ip();

        return view('front.featured-image')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description, 'get_image' => $get_image
        ]);
    }

    public function banner()
    {
        $header = "Banner | Nairametrics";
        $og_url = '/mid-banner';
        $title = "Terms and Disclaimer | Nairametrics Stocks";
        $description = "Terms and Disclaimer - Stock Select Finance Agrotech Markets Bonds Dividends Forum | Nairametrics Stocks";

        $get_image = Banner::inRandomOrder()
            ->where('status', 'active')
            ->limit(1)
            ->get();

        $ip = \Request::ip();

        return view('front.featured-image')->with([
            'header' => $header, 'og_url' => $og_url,
            'title' => $title, 'description' => $description, 'get_image' => $get_image
        ]);
    }
}
