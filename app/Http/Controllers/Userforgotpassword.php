<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Avatar;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Validator;

class Userforgotpassword extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->segment(3))
        {
            $forgot_token=$request->segment(3);
            $newforgottoken=base64_decode($forgot_token);

            $checkmailcode=User::where('forgot_otp',$newforgottoken)->get()->first();

            if(!$checkmailcode)
            {
                return redirect('login')->with('deleted','Invalid Forgot Link.');
            }

            return view('userforgotpassword',compact('forgot_token'));
        }
        else{
          return redirect('login')->with('deleted','Invalid Forgot Link.');
        }   
    }

    public function resetuserpassword(Request $request)
    {
      try{
        $request->validate([
          'password' => 'required|min:8'
        ]);

        
        if($request->forgot_token!="")
        {
          $forgot_token=$request->forgot_token;
        }
        else{
          return redirect('login')->with('deleted','Invalid Forgot Link.');
        }

        $newforgottoken=base64_decode($forgot_token);
        $checkmailcode=User::where('forgot_otp',$newforgottoken)->get()->first();

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
                    if($e instanceof ValidationException){
                        $listmessage="";
                        foreach($e->errors() as $list)
                        {
                            $listmessage.=$list[0];
                        }

                        if($listmessage!="")
                        {
                            return back()->with('deleted',$listmessage);
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
