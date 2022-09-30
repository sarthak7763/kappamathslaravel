<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;

class ForgotController extends BaseController
{

    public function forgotpassword(Request $request)
    {
        try{
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return $this::sendValidationError('Validation Error.', $validator->messages()->all()[0]);       
        }

            try{
                    $checkmail=User::where('email',$request->email)->get()->first();
                    if(!$checkmail)
                    {
                        return $this::sendError('Email not exists.', ['error'=>'Email does not exists. Please enter your registered email.']);
                    }

                }catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }

               $user_forgot_otp=rand(111111,999999);
               $userid=$checkmail->id;

               $userdet = User::find($userid);

             if(is_null($userdet)){
               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please enter correct email.']);
            }

        try{
            
            $userdet->forgot_otp = $user_forgot_otp;
            $userdet->save();

            $success['email'] =  $request->email; 
            $success['otp'] =  $user_forgot_otp;

            return $this::sendResponse($success, 'Please check your email. We have sent OTP to reset password to your account.');
        }
        catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
        
    }
    catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }

}

    public function resendotp(Request $request)
    {
        try{
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if($validator->fails()){
            return $this::sendValidationError('Validation Error.', $validator->messages()->all()[0]);       
        }

            try{
                    $checkmail=User::where('email',$request->email)->get()->first();
                    if(!$checkmail)
                    {
                        return $this::sendError('Email not exists.', ['error'=>'Email does not exists. Please enter your registered email.']);
                    }

                }catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }

               $userid=$checkmail->id;
               $userdet = User::find($userid);

             if(is_null($userdet)){
               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please enter correct email.']);
            }

        try{
            
            if($userdet->forgot_otp!="")
            {
                $user_forgot_otp=$userdet->forgot_otp;
            }
            else{
                $user_forgot_otp=rand(111111,999999);
                $userdet->forgot_otp = $user_forgot_otp;
                $userdet->save();
            }

            $success['email'] =  $request->email; 
            $success['otp'] =  $user_forgot_otp;
            
            return $this::sendResponse($success, 'Please check your email. We have sent OTP to reset password to your account.');
        }
        catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
        
    }
    catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }

    }

    public function verifyotp(Request $request)
    {
        try{
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp'=>'required'
        ]);

        if($validator->fails()){
            return $this::sendValidationError('Validation Error.', $validator->messages()->all()[0]);       
        }

            try{
                    $checkmail=User::where('email',$request->email)->get()->first();
                    if(!$checkmail)
                    {
                        return $this::sendError('Email not exists.', ['error'=>'Email does not exists. Please enter your registered email.']);
                    }

                }catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }

               $userid=$checkmail->id;
               $userdet = User::find($userid);

             if(is_null($userdet)){
               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please enter correct email.']);
            }

        try{

            if($userdet->forgot_otp!="")
            {
                if($userdet->forgot_otp==$request->otp)
                {
                    $success['email'] =  $request->email;
                    return $this::sendResponse($success, 'OTP verified successfully. Please reset password to your account.');
                }
                else{
                    return $this::sendError('wrong OTP.', ['error'=>'Please enter correct OTP.']);
                }
            }
            else{
                return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']); 
            }
        }
        catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
        
    }
    catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }

}

    public function resetpassword(Request $request)
    {
        try{
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if($validator->fails()){
            return $this::sendValidationError('Validation Error.', $validator->messages()->all()[0]);       
        }

            try{
                    $checkmail=User::where('email',$request->email)->get()->first();
                    if(!$checkmail)
                    {
                        return $this::sendError('Email not exists.', ['error'=>'Email does not exists. Please enter your registered email.']);
                    }

                }catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong4']);    
               }

               $userid=$checkmail->id;
               $userdet = User::find($userid);

             if(is_null($userdet)){
               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please enter correct email.']);
            }

        try{
        
            $userdet->password = bcrypt($request->password);
            $userdet->forgot_otp="";
            $userdet->save();

            $success['message'] = 'Password reset successfully.';
            return $this::sendResponse($success, 'Password reset successfully to your account.');
        }
        catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }
        
    }
    catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong1']);    
               }

    }
    
}