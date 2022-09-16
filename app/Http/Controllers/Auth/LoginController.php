<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use Auth;
use Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

     public function redirectToProvider($service)
    {
        return Socialite::driver($service)->redirect();
    }

    // *
    //  * Obtain the user information from GitHub.
    //  *
    //  * @return \Illuminate\Http\Response
     
    public function handleProviderCallback($service)
    {
        $userSocial = Socialite::driver($service)->user();

        //return $userSocial->name;
        $findUser=User::where('email',$userSocial->email)->first();
        if($findUser)
        {
             Auth::login($findUser);
             $url = config('app.url');
             return redirect($url);

        }
        else
        {
        $user = new User;
        $user->name = $userSocial->name;
        $user->email = $userSocial->email;
        $user->password = Hash::make(123456);
        $user->role = "S";
        $user->save();
        $this->guard()->login($user);
        $url = config('app.url');
        return redirect($url);

        }
        
    }
}
  
