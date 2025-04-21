<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
//use Socialite;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;

class FacebookController extends Controller
{
    public function redirectToFacebook()
 {
    return Socialite::driver('facebook')->redirect();
 }

 public function handleFacebookCallback()
 {
  try {
       $user = Socialite::driver('facebook')->user();
       $first_last = explode(" ",$user->getName());
            foreach($first_last as $fl)
            {
                $firstname = $fl[0];
                $lastname = $fl[1];
            }

       $saveUser = User::updateOrCreate([
           'facebook_id' => $user->getId(),
       ],[
           'name' => $user->getName(),
           'email' => $user->getEmail(),
           'first_name' => $first_last[0],
           'last_name' => $first_last[1],
           'role_id' => 2,
           'password' => Hash::make($user->getName().'@'.$user->getId())
            ]);

       Auth::loginUsingId($saveUser->id);

       return redirect('/home');
       } catch (\Throwable $th) {
          throw $th;
       }
   }
}