<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;

class AuthController extends BaseController
{
    /**
     * Registration Req
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
 
        $token = $user->createToken('Laravel8PassportAuth')->accessToken;
 
        return response()->json(['token' => $token], 200);
    }
 
    /**
     * Login Req
     */
    public function login(Request $request)
    {
        try{
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendValidationError('Validation Error.', $validator->messages()->all()[0]);       
        }

        $data = [
            'email' => $request->email,
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
                    $success['token'] =  $token; 
                    $success['name'] =  $user->name;

                    return $this->sendResponse($success, 'User login successfully.');
                }
                else{
                    return $this->sendError('Account Supended.', ['error'=>'Your account has been suspended.']);
                }
            }
            else{
                return $this->sendError('Unauthorised.', ['error'=>'Unauthorised User']);
            }

        } else {
            return $this->sendError('Unauthorised.', ['error'=>'Invalid email or password.']);
        }
    }
    catch(\Exception $e){
                  return $this->sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }

    }
    catch(\Exception $e){
                  return $this->sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }

    public function userInfo() 
    {

     $user = auth()->user();
     return response()->json(['user' => $user], 200);

    }
}