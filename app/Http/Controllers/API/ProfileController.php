<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Coursetopic;
use App\Subject;
use App\Resultmarks;
use App\Quiztopic;
use Validator;
use Hash;
use DB;

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
		        	$user->image=url('/').'/images/user/profile_placeholder.png';
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
		        	$userdet->image=url('/').'/images/user/profile_placeholder.png';
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
		        	$userdet->image=url('/').'/images/user/profile_placeholder.png';
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

    public function mysubscribecourselist(Request $request)
    {
    	try{
    		$user=auth()->user();
    		
    		if($user)
    		{
    			$new_subtopiclist=[];
    			$ongoing_subtopiclist=[];
    			$complete_subtopiclist=[];

    			$userid=$user->id;
    			$userdet=User::find($userid);

		        if(is_null($userdet)){
	               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	            }

	            $subscribed_coursesubtopics=$userdet->subscribed_coursesubtopics;
	            if($subscribed_coursesubtopics!="")
	            {
	            	$subscribed_coursesubtopicsarray=explode(',',$subscribed_coursesubtopics);
	            	if(count($subscribed_coursesubtopicsarray) > 0)
	            	{
		            	$ongoingcoursesubtopicsdata=Coursetopic::whereIn('id', $subscribed_coursesubtopicsarray)->get();
	            		if($ongoingcoursesubtopicsdata)
	            		{
	            			$ongoingcoursesubtopicsdataarray=$ongoingcoursesubtopicsdata->toArray();
	            			if($ongoingcoursesubtopicsdataarray)
	            			{
	            				$ongoing_subtopiclist=[];
	            			foreach($ongoingcoursesubtopicsdataarray as $row)
	            			{

	            				$quiztopicdata=Quiztopic::where('course_topic',$row['id'])->where('quiz_status','1')->where('quiz_type','1')->get()->first();
	            				if($quiztopicdata)
	            				{
	            					
	            				}

	            				$subject=$row['subject'];

	            				$subjectdetail=Subject::where('id',$subject)->get()->first();
	            				if($subjectdetail)
	            				{
	            					$subjectdetaildata=$subjectdetail->toArray();
	            					if($subjectdetaildata)
	            					{
	            						$course_name=$subjectdetaildata['title'];
	            					}
	            					else{
	            						$course_name="";
	            					}
	            				}
	            				else{
	            					$course_name="";
	            				}

	            				$ongoing_subtopiclist[]=array(
	            					'sub_topic_id'=>$row['id'],
	            					'sub_topic_name'=>$row['topic_name'],
	            					'course_name'=>$course_name,
	            					'total_score'=>0,
	            					'current_score'=>0
	            				);
	            			}

	            			}
	            			else{
	            				$ongoing_subtopiclist=[];
	            			}
	            		}
	            		else{
	            			$ongoing_subtopiclist=[];
	            		}
	            	}
	            	else{
	            		$ongoing_subtopiclist=[];
	            	}

	            	if(count($subscribed_coursesubtopicsarray) > 0)
	            	{
	            		$newcoursesubtopicsdata=Coursetopic::where('topic_status','1')->whereNotIn('id', $subscribed_coursesubtopicsarray)->get();
	            	}
	            	else{
	            		$newcoursesubtopicsdata=Coursetopic::where('topic_status','1')->get();
	            	}
	            	
	            	if($newcoursesubtopicsdata)
	            	{
	            		$newcoursesubtopicsdataarray=$newcoursesubtopicsdata->toArray();
	            		if($newcoursesubtopicsdataarray)
	            		{
	            			$new_subtopiclist=[];

	            			foreach($newcoursesubtopicsdataarray as $list)
	            			{
	            				$subject=$list['subject'];

	            				$subjectdetail=Subject::where('id',$subject)->get()->first();
	            				if($subjectdetail)
	            				{
	            					$subjectdetaildata=$subjectdetail->toArray();
	            					if($subjectdetaildata)
	            					{
	            						$course_name=$subjectdetaildata['title'];
	            					}
	            					else{
	            						$course_name="";
	            					}
	            				}
	            				else{
	            					$course_name="";
	            				}

	            				$new_subtopiclist[]=array(
	            					'sub_topic_id'=>$list['id'],
	            					'sub_topic_name'=>$list['topic_name'],
	            					'course_name'=>$course_name,
	            					'total_score'=>0,
	            					'current_score'=>0
	            				);
	            			}
	            		}
	            		else{
	            			$new_subtopiclist=[];
	            		}

	            	}
	            	else{
	            		$new_subtopiclist=[];
	            	}

	            	if(count($subscribed_coursesubtopicsarray) > 0)
	            	{
	            		$complete_subtopiclist=[];
	            		foreach($subscribed_coursesubtopicsarray as $arr)
	            		{
	            			$coursesubtopicsdet=Coursetopic::where('id', $arr)->get()->first();
	            			if($coursesubtopicsdet)
	            			{
	            				$coursesubtopicsdetdata=$coursesubtopicsdet->toArray();
	            				if($coursesubtopicsdetdata)
	            				{
	            					$subject=$coursesubtopicsdetdata['subject'];

	            				$subjectdetail=Subject::where('id',$subject)->get()->first();
	            				if($subjectdetail)
	            				{
	            					$subjectdetaildata=$subjectdetail->toArray();
	            					if($subjectdetaildata)
	            					{
	            						$course_name=$subjectdetaildata['title'];
	            					}
	            					else{
	            						$course_name="";
	            					}
	            				}
	            				else{
	            					$course_name="";
	            				}

	            					$quiztopicdata=Quiztopic::where('course_topic',$arr)->where('quiz_status','1')->where('quiz_type','1')->get()->first();
			            			if($quiztopicdata)
			            			{
			            				$quiztopicdataarray=$quiztopicdata->toArray();
			            				if($quiztopicdataarray)
			            				{
			            					$quiz_id=$quiztopicdataarray['id'];
			            					$quizresultmarksdetail=DB::table('result_marks')->where('user_id',$user->id)->where('topic_id',$quiz_id)->whereColumn('random_question_ids','question_ids')->get()->first();
			            					if($quizresultmarksdetail)
			            					{
			            						$complete_subtopiclist[]=array(
				            					'sub_topic_id'=>$coursesubtopicsdetdata['id'],
				            					'sub_topic_name'=>$coursesubtopicsdetdata['topic_name'],
				            					'course_name'=>$course_name,
				            					'total_score'=>$quizresultmarksdetail->total_marks,
				            					'current_score'=>$quizresultmarksdetail->marks
	            									);
			            					}
			            				}
			            			}
	            				}
	            			}
	            		}
	            	}
	            	else{
	            		$complete_subtopiclist=[];
	            	}

	            }
	            else{
	            	$ongoing_subtopiclist=[];
    				$complete_subtopiclist=[];

	            	$newcoursesubtopicsdata=Coursetopic::where('topic_status','1')->get();
	            	if($newcoursesubtopicsdata)
	            	{
	            		$newcoursesubtopicsdataarray=$newcoursesubtopicsdata->toArray();
	            		if($newcoursesubtopicsdataarray)
	            		{
	            			$new_subtopiclist=[];

	            			foreach($newcoursesubtopicsdataarray as $list)
	            			{
	            				$subject=$list['subject'];

	            				$subjectdetail=Subject::where('id',$subject)->get()->first();
	            				if($subjectdetail)
	            				{
	            					$subjectdetaildata=$subjectdetail->toArray();
	            					if($subjectdetaildata)
	            					{
	            						$course_name=$subjectdetaildata['title'];
	            					}
	            					else{
	            						$course_name="";
	            					}
	            				}
	            				else{
	            					$course_name="";
	            				}

	            				$new_subtopiclist[]=array(
	            					'sub_topic_id'=>$list['id'],
	            					'sub_topic_name'=>$list['topic_name'],
	            					'course_name'=>$course_name,
	            					'total_score'=>0,
	            					'current_score'=>0
	            				);
	            			}
	            		}
	            		else{
	            			$new_subtopiclist=[];
	            		}
	            	}
	            	else{
	            		$new_subtopiclist=[];
	            	}
	            }

	            $success['new'] =  $new_subtopiclist;
	            $success['ongoing'] =  $ongoing_subtopiclist;
	            $success['completed'] =  $complete_subtopiclist;
	            return $this::sendResponse($success, 'My Courses List.');


    		}
    		else{
    			return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
    		}
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }
    }

    public function updatecoursetopicongoingstatus(Request $request)
    {
    	try{
    		$user=auth()->user();
    		
    		if($user)
    		{
    			$validator = Validator::make($request->all(), [
		            'sub_topic_id' => 'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $userid=$user->id;
		        $subtopicid=$request->sub_topic_id;

		        $coursesubtopic = Coursetopic::find($subtopicid);
		          if(is_null($coursesubtopic)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $userdet=User::find($userid);

		        if(is_null($userdet)){
	               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	            }

	            $usersubtopicsubscribe=User::where('id',$userid)->whereRaw('FIND_IN_SET(?, subscribed_coursesubtopics)', [$subtopicid])->get()->first();
	            if(!$usersubtopicsubscribe)
	            {
	            	$subscribed_coursesubtopics=$userdet->subscribed_coursesubtopics;
		            if($subscribed_coursesubtopics!="")
		            {
		            	$newsubscribed_coursesubtopics=$subscribed_coursesubtopics.','.$subtopicid;
		            }
		            else{
		            	$newsubscribed_coursesubtopics=$subtopicid;
		            }

	            	$userdet->subscribed_coursesubtopics=$newsubscribed_coursesubtopics;

		            try{
			            $userdet->save();
			         }catch(\Exception $e){

			            return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
			         }

		            $success=[];
		            return $this::sendResponse($success, 'Subtopic Subscribed successfully');
	            }
	            else{
	            	return $this::sendError('Unauthorised.', ['error'=>'Already Subscribed.']);
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


  

    

}