<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function checkwebuserlogin(Request $request)
    {
        try{

            $form_data=$request->all();
            $request->validate([
                'email'=>'required|email',
                'password'=>'required'
            ]);

            $remember_me = $request->has('remember') ? true : false;
            $email=$request->input('email');
            $password=$request->input('password');
            $check = $request->only('email', 'password');

            try{
            if(\Auth::attempt(['email'=>$email,'password'=>$password ], $remember_me))
            {
                $user = auth()->user();
                return redirect('/admin');
            }
            else
            {
                return back()->with('error','your username and password are wrong.');
            }
        }
        catch(\Exception $e)
        {
          return back()->with('error','Something went wrong.');
        }


        }
        catch(\Exception $e){
                  
                  if($e instanceof \Illuminate\Validation\ValidationException){
                        $listmessage=[];
                        foreach($e->errors() as $key=>$list)
                        {
                            $listmessage[$key]=$list[0];
                        }

                        if(count($listmessage) > 0)
                        {
                          return back()->with('valid_error',$listmessage);
                        }
                        else{
                        return back()->with('error','Something went wrong.');
                        }   
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }

               }

        
        
    }
     
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
  
