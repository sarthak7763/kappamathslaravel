<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;
use Hash;

class ProfileController extends BaseController
{

    public function getuserinfo()
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
		        if($user->image!="")
		        {
		        	$userimage=url('/').'/images/user/'.$user->image;
		        }
		        else{
		        	$userimage=url('/').'/images/user/profile.png';
		        }

		        $userarray=array(
		        	'name'=>$user->name,
		        	'email'=>$user->email,
		        	'mobile'=>$user->mobile,
		        	'username'=>$user->username,
		        	'image'=>$userimage
		        );

		        $success['userarray'] =  $userarray;
                return $this::sendResponse($success, 'User Found.');
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }  
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }

    public function updateprofile(Request $request)
    {
    	try{
    		$user=auth()->user();
    		if($user)
    		{
	    		$validator = Validator::make($request->all(), [
		            'name' => 'required',
		            'username'=>'required',
		            'mobile'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.', $validator->messages()->all()[0]);       
		        }

		        $userid=$user->id;
		        $userdet=User::find($userid);

			    if(is_null($userdet)){
	               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	            }

	            if($userdet->username==$request->username)
	            {
	            	$userdet->name = $request->name;
	            	$userdet->mobile=$request->mobile;
            		$userdet->save();
	            }
	            else{
	            	try{
		            $checkusername=User::where('username',$request->username)->get()->first();
		            if($checkusername)
		            {
		                return $this::sendError('Username exists.', ['error'=>'Username already exists. Please try with another one.']);
		            }
		            else{
		            	$userdet->username=$request->username;
		                $userdet->name = $request->name;
	            		$userdet->mobile=$request->mobile;
            			$userdet->save();
		            }
		        }
		        catch(\Exception $e){
		                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
		               }
	            }

	            if($user->image!="")
		        {
		        	$userimage=url('/').'/images/user/'.$user->image;
		        }
		        else{
		        	$userimage=url('/').'/images/user/profile.png';
		        }      

	            $userarray=array(
		        	'name'=>$user->name,
		        	'email'=>$user->email,
		        	'mobile'=>$user->mobile,
		        	'username'=>$user->username,
		        	'image'=>$userimage
		        );

		        $success['userarray'] =  $userarray;
	            return $this::sendResponse($success, 'Profile updated successfully.');
    		}
    		else{
    			return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
    		}	
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }

    public function  changepassword(Request $request)
    {
    	try{
    		$user=auth()->user();
    		if($user)
    		{
	    		$validator = Validator::make($request->all(), [
		            'old_password' => 'required',
		            'new_password'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.', $validator->messages()->all()[0]);       
		        }

		        $userid=$user->id;
		        $userdet=User::find($userid);

		        if(is_null($userdet)){
	               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	            }

		        $old_password=$request->old_password;
		        $new_password=$request->new_password;

		        if (Hash::check($old_password, $userdet->password)) {
				    $finalnewpassword=bcrypt($new_password);
				    $userdet->password=$finalnewpassword;
				    $userdet->save();

					if($user->image!="")
			        {
			        	$userimage=url('/').'/images/user/'.$user->image;
			        }
			        else{
			        	$userimage=url('/').'/images/user/profile.png';
			        }

				    $userarray=array(
			        	'name'=>$user->name,
			        	'email'=>$user->email,
			        	'mobile'=>$user->mobile,
			        	'username'=>$user->username,
			        	'image'=>$userimage
			        );

				    $success['userarray'] =  $userarray;
	            	return $this::sendResponse($success, 'Password updated successfully.');

				}
		        else{
		        	return $this::sendError('Wrong Password.', ['error'=>'Please enter correct old password.']);
		        }
		    }
		    else{
    			return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
    		}
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }

    }
}