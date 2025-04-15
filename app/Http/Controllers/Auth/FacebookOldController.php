<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
//use Socialite;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\User;

class FacebookController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /***
     * Create a new controller instance
     * 
     * @return void
     */
    public function handlFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();

            $create['name'] = $user->getName();
            $create['email'] = $user->getEmail();
            $create['facebook_id'] = $user->getId();
            $create['role_id'] = 2;

            $userModel = new User;
            $createdUser = $userModel->addNew($create);
            Auth::loginUsingId($createdUser->id);

            return redirect()->route('home');
            #return redirect('/home');
            
        } catch (Exception $e)
        {
            #dd($e->getMEssage());
            return redirect('auth/facebook');
        }
    }
}