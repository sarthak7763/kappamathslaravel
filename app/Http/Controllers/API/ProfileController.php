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
		        	$user->image=url('/').'/images/user/'.$user->image;
		        }
		        else{
		        	$user->image=url('/').'/images/user/profile.png';
		        }

		        $success['userdet']=$user;
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
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        if ($file = $request->file('image')) {

		        	$validator = Validator::make($request->all(), [
		            	'image' => 'required|mimes:jpeg,png,jpg'
		        	]);

			         if($validator->fails()){
			            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
			        }

		            $name = 'profile_'.time().$file->getClientOriginalName(); 
		            $file->move('images/user/', $name);
		            $image = $name;
		        }
		        else{
		            $image="";
		        }

		        $userid=$user->id;
		        $userdet=User::find($userid);

			    if(is_null($userdet)){
	               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	            }

	            if($userdet->username==$request->username)
	            {
	            	if($image!="")
	            	{
	            		$userdet->image=$image;
	            		$userdet->name = $request->name;
	            		$userdet->mobile=$request->mobile;
            			$userdet->save();
	            	}
	            	else{
	            		$userdet->name = $request->name;
	            		$userdet->mobile=$request->mobile;
            			$userdet->save();
	            	}
	            	
	            }
	            else{
	            	try{
		            $checkusername=User::where('username',$request->username)->get()->first();
		            if($checkusername)
		            {
		                return $this::sendError('Username exists.', ['error'=>'Username already exists. Please try with another one.']);
		            }
		            else{
		            	if($image!="")
		            	{
		            		$userdet->image=$image;
		            		$userdet->username=$request->username;
		                	$userdet->name = $request->name;
	            			$userdet->mobile=$request->mobile;
            				$userdet->save();
		            	}
		            	else{
		            		$userdet->username=$request->username;
		                	$userdet->name = $request->name;
	            			$userdet->mobile=$request->mobile;
            				$userdet->save();
		            	}
		            	
		            }
		        }
		        catch(\Exception $e){
		                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
		               }
	            }

	            if($userdet->image!="")
		        {
		        	$userdet->image=url('/').'/images/user/'.$userdet->image;
		        }
		        else{
		        	$userdet->image=url('/').'/images/user/profile.png';
		        }     

		        $success['userdet']=$userdet;
	            return $this::sendResponse($success, 'Profile updated successfully.');
    		}
    		else{
    			return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
    		}	
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }
    }

    public function updateprofileimage(Request $request)
    {
    	try{
    		$user=auth()->user();
    		if($user)
    		{
    			$validator = Validator::make($request->all(), [
		            'image' => 'required|mimes:jpeg,png,jpg'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $userid=$user->id;
		        $userdet=User::find($userid);

		        if(is_null($userdet)){
	               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	            }

		        if ($file = $request->file('image')) {
		            $name = 'profile_'.time().$file->getClientOriginalName(); 
		            $file->move('images/user/', $name);
		            $image = $name;
		        }
		        else{
		            $image="";
		        }

		        $userdet->image=$image;
				$userdet->save();

				if($userdet->image!="")
		        {
		        	$userdet->image=url('/').'/images/user/'.$userdet->image;
		        }
		        else{
		        	$userdet->image=url('/').'/images/user/profile.png';
		        }

			    $success['userdet']=$userdet;
            	return $this::sendResponse($success, 'Profile image updated successfully.');
    		}
    		else{
    			return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
    		}
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }

    public function updatepassword(Request $request)
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
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
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

				    $success['userdet']=$userdet;
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
               }
    }


    public function updateuserpushnotificationsettings(Request $request)
    {
    	try{
    		$user=auth()->user();

    		if($user)
    		{
    			$validator = Validator::make($request->all(), [
		            'push_notification' => 'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $userid=$user->id;
		        $userdet=User::find($userid);

		        if(is_null($userdet)){
	               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	            }

	            $userdet->push_notifications=$request->push_notification;
	            $userdet->save();

	            $userarray=array(
			        	'push_notification'=>$userdet->push_notifications
			        );

				    $success['userarray'] =  $userarray;
	            	return $this::sendResponse($success, 'Settings updated successfully.');

    		}
    		else{
    			return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
    		}
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }


    public function updateuseremailnotificationsettings(Request $request)
    {
    	try{
    		$user=auth()->user();
    		
    		if($user)
    		{
    			$validator = Validator::make($request->all(), [
		            'email_notification' => 'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $userid=$user->id;
		        $userdet=User::find($userid);

		        if(is_null($userdet)){
	               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	            }

	            $userdet->email_notifications=$request->email_notification;
	            $userdet->save();

	            $userarray=array(
			        	'email_notification'=>$userdet->email_notifications
			        );

				    $success['userarray'] =  $userarray;
	            	return $this::sendResponse($success, 'Settings updated successfully.');
    		}
    		else{
    			return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
    		}

    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }
  

    

}