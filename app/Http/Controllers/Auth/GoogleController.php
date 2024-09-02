<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\User;

class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /***
     * Create a new controller instance
     * 
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $first_last = explode(" ",$user->name);
            foreach($first_last as $fl)
            {
                $firstname = $fl[0];
                $lastname = $fl[1];
            }

            $finduser = User::where('google_id', $user->id)->first();

            if ($finduser)
            {
                Auth::login($finduser);

                return redirect('/home');
                
            } else{

                $newUser = User::create([
                    'name' => $user->name,
                    'first_name' => $first_last[0],
                    'last_name' => $first_last[1],
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'role_id' => 2,
                    'password' => encrypt('luciddream23')
                ]);

                Auth::login($newUser);

                return redirect('/home');
            }
        } catch (Exception $e)
        {
            dd($e->getMEssage());
        }
    }
}