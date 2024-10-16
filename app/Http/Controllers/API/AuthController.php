<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Subscription;
use App\Usersubscriptions;
use Carbon\Carbon;
use Validator;

class AuthController extends BaseController
{

    public function checkusername(Request $request)
    {
        try{
            $input = $request->all();

            $validator=Validator::make($request->all(), [
                'username'=>'required'
            ]);

        if($validator->fails()){
            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
        }

        try{
            $checkusername=User::where('username',$request->username)->get()->first();
            if($checkusername)
            {
                return $this::sendError('Username exists.', ['error'=>'Username already exists. Please try with another one.']);
            }
            else{
                $success['username'] =  $request->username;
                return $this::sendResponse($success, 'Username is unique.');
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

    public function register(Request $request)
    {
        try{
        $validator=Validator::make($request->all(), [
            'name' => 'required',
            'username'=>'required',
            'email' => 'required|email',
            'number'=>'required',
            'password' => 'required|min:8',
            'fcm_token'=>'required'
        ]);

        if($validator->fails()){
            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);      
        }

        try{
            $checkusername=User::where('username',$request->username)->get()->first();
            if($checkusername)
            {
                return $this::sendError('Username exists.', ['error'=>'Username already exists. Please try with another one.']);
            }
            else{
                try{
                    $checkmail=User::where('email',$request->email)->get()->first();
                    if($checkmail)
                    {
                        return $this::sendError('Email exists.', ['error'=>'Email already exists. Please try with another one.']);
                    }

                }catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
            }
        }
        catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }

        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username'=>$request->username,
                'country_code'=>$request->country_code,
                'mobile'=>$request->number,
                'password' => bcrypt($request->password),
                'role'=>'S',
                'status'=>'1',
                'device_id'=>$request->fcm_token
            ]);
            $subscription =Subscription::where('subscription_status','1')->where('flag',0)->first();
            if($subscription){
                $user_sub = [
                    'user_id'               => $user->id,
                    'subscription_id'       => $subscription->id,
                    'transaction_id'        => '0',
                    'subscription_payment'  => $subscription->price,
                    'subscription_start'    => date('Y-m-d'),
                    'subscription_end'      => Carbon::now()->addDays($subscription->subscription_tenure),
                    'subscription_status'   => '1'
                ];
                Usersubscriptions::create($user_sub);
            }

            $token = $user->createToken('Laravel8PassportAuth')->accessToken;

            if($user->image!="")
            {
                $user->image=url('/').'/images/user/'.$user->image;
            }
            else{
                $user->image=url('/').'/images/user/profile_placeholder.png';
            }
            
            $success['token'] =  $token; 
            $success['name'] =  $request->name;
            $success['userdet']=$user;
      
            return $this::sendResponse($success, 'User login successfully.');

        }
        catch(\Exception $e){
            return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
        }
        
    }
    catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }

}
 
    /**
     * Login Req
     */
    public function login(Request $request)
    {
        try{
        $input = $request->all();
        $validator = Validator::make($input, [
            'username' => 'required',
            'password' => 'required',
            'fcm_token'=>'required'
        ]);
   
        if($validator->fails()){
            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);      
        }

        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $data = [
            $fieldType => $request->username,
            'password' => $request->password
        ];
        
        try{
        if (auth()->attempt($data)) {

            $user=auth()->user();
            //$user = Auth::user();
            $token = $user->createToken('Laravel8PassportAuth')->accessToken;

            if($user->role=="S")
            {
                if($user->status=="1")
                {

                    $userdet = User::find($user->id);

                    if(is_null($userdet)){
                       return $this::sendForbiddenError('Unauthorised.', ['error'=>'Unauthorised User']);
                    }

                    $userdet->device_id=$request->fcm_token;
                    $userdet->save();

                    if($user->image!="")
                    {
                        $user->image=url('/').'/images/user/'.$user->image;
                    }
                    else{
                        $user->image=url('/').'/images/user/profile_placeholder.png';
                    }

                    $success['token'] =  $token; 
                    $success['name'] =  $user->name;
                    $success['userdet']=$user;

                    return $this::sendResponse($success, 'User login successfully.');
                }
                elseif($user->status=="2"){
                    return $this::sendError('Account suspended.', ['error'=>'Your account has been suspended.']);
                }
                else{
                    return $this::sendError('Account Supended.', ['error'=>'Your account has been suspended.']);
                }
            }
            else{
                return $this::sendForbiddenError('Unauthorised.', ['error'=>'Unauthorised User']);
            }

        } else {
            return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Invalid '.$fieldType.' or password.']);
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

}