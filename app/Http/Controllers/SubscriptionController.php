<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Newsletter;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('pay_test');
    }

    public function newslet()
    {

        if(Newsletter::subscribeOrUpdate('nairametrics2@gmail.com', ['FNAME'=>'Chima', 'LNAME'=>'Testwe']))
        {
            echo 'worked';
        }
        else{
            echo 'failed';
        }

    }
    
}
