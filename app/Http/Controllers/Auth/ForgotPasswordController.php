<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\User;
use Auth;
use Hash;
use App\Mail\UserForgotMail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function adminforgotpassword(Request $request)
    {
        try{
            $form_data=$request->all();
            $request->validate([
                'email'=>'required|email'
            ]);

            $email=$request->input('email');

            $checkmail=User::where('email',$email)->where('role','A')->get()->first();   
            if(!$checkmail)
            {
                return back()->with('error','Email does not exists. Please enter admin registered email.');
            }

            $userid=$checkmail->id;
            $user_forgot_otp=rand(111111,999999).$userid;

            $userdet = User::find($userid);

             if(is_null($userdet)){
               return back()->with('error','Something went wrong.');
            }

        try{
            
            $userdet->forgot_otp = $user_forgot_otp;
            $userdet->save();

            $newforgototp=base64_encode($user_forgot_otp);
            $forgotlink=url('/').'/password/reset-password/'.$newforgototp;

            $details = [
              'type'=>'admin',
              'name' =>$userdet->name,
              'link' =>$forgotlink
            ];
   
          \Mail::to($email)->send(new \App\Mail\UserForgotMail($details));

            return back()->with('success','Please check your email. We have sent link to reset password to your account.');
        }
        catch(\Exception $e){
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


    public function resetpassword(Request $request)
    {
        if($request->segment(3))
        {
            $forgot_token=$request->segment(3);
            $newforgottoken=base64_decode($forgot_token);

            $checkmailcode=User::where('forgot_otp',$newforgottoken)->where('role','A')->get()->first();

            if(!$checkmailcode)
            {
                return redirect('login')->with('deleted','Invalid Forgot Link.');
            }

            return view('auth/passwords/reset',compact('forgot_token'));
        }
        else{
          return redirect('login')->with('deleted','Invalid Forgot Link.');
        }   
    }


    public function adminresetpassword(Request $request)
    {
      try{
        $request->validate([
          'password' => 'required|min:8|confirmed',
          'password_confirmation' => 'required|min:8'
        ]);

        
        if($request->forgot_token!="")
        {
          $forgot_token=$request->forgot_token;
        }
        else{
          return redirect('login')->with('deleted','Invalid Forgot Link.');
        }

        $newforgottoken=base64_decode($forgot_token);
        $checkmailcode=User::where('forgot_otp',$newforgottoken)->where('role','A')->get()->first();

        if(!$checkmailcode)
        {
            return redirect('login')->with('deleted','Invalid Forgot Link.');
        }

        $userid=$checkmailcode->id;
        $user = User::find($userid);
          if(is_null($user)){
           return redirect('login')->with('deleted','Something went wrong.');
        }

        $input = $request->all();

          $user->password = bcrypt($request->password);
          $user->forgot_otp="";

          try{
            $user->save();
            return back()->with('success', 'Password reset successfully.');

          }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
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
                            return back()->with('deleted','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('deleted','Something went wrong.');
                    }      
               }

    }



}
