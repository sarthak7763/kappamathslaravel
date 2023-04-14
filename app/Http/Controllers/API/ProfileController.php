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

    public function deleteaccount(Request $request)
    {
    	try{
    		$user=auth()->user();
    		if($user)
    		{
    			$userid=$user->id;
		        $userdet=User::find($userid);

		        if(is_null($userdet)){
	               return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	            }

	            $userdet->status=2;
				$userdet->save();

				$success=[];
	           	return $this::sendResponse($success, 'Account deleted successfully.');
    		}
    		else{
    			return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
    		}
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
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
			        	'push_notification'=>(int)$userdet->push_notifications
			        );

				    $success['userdet'] =  $userarray;
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
			        	'email_notification'=>(int)$userdet->email_notifications
			        );

				    $success['userdet'] =  $userarray;
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
    			if(isset($request->type))
    			{
    				$type=$request->type;
    			}
    			else{
    				$type="";
    			}

    			if(isset($request->filter))
    			{
    				$subjectdetail=Subject::where('id',$request->filter)->get()->first();
    				if($subjectdetail)
    				{
    					$filter=$request->filter;
    				}
    				else{
    					$filter="0";
    				}
    			}
    			else{
    				$filter="";
    			}

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
	            		$complete_subtopiclist=$this->completesubscribecourselist($userid,$subscribed_coursesubtopicsarray);

	            		$new_subtopiclist=$this->newongoingsubscribecourselist($userid,$subscribed_coursesubtopicsarray);

	            		$ongoing_subtopiclist=$this->ongoingsubscribecourselist($userid,$subscribed_coursesubtopicsarray);
	            	}
	            	else{
	            		$ongoing_subtopiclist=[];
    					$complete_subtopiclist=[];
	            		$new_subtopiclist=$this->newsubscribecourselist($userid);
	            	}
	            }
	            else{
	            	$ongoing_subtopiclist=[];
    				$complete_subtopiclist=[];

	            	$new_subtopiclist=$this->newsubscribecourselist($userid);
	            }

	            if($type!="")
	            {
	            	if($type=="new")
	            	{
	            		if($filter!="")
	            		{
		            		if(count($new_subtopiclist) > 0)
		            		{
		            			$filternewkeys = array_keys(array_column($new_subtopiclist, 'course_id'), $filter);
		            			$filter_new_subtopiclist=[];
		            			foreach($filternewkeys as $keyval)
		            			{
		            				$filter_new_subtopiclist[]=$new_subtopiclist[$keyval];
		            			}

		            			$success =  $filter_new_subtopiclist;
		            		}
		            		else{
		            			$success =  $new_subtopiclist;
		            		}
	            		}
	            		else{
	            			$success =  $new_subtopiclist;
	            		}
	            	}
	            	elseif($type=="ongoing")
	            	{
	            		if($filter!="")
	            		{
		            		if(count($ongoing_subtopiclist) > 0)
		            		{
		            			$filterongoingkeys = array_keys(array_column($ongoing_subtopiclist, 'course_id'), $filter);
		            			$filter_ongoing_subtopiclist=[];
		            			foreach($filterongoingkeys as $keyval)
		            			{
		            				$filter_ongoing_subtopiclist[]=$ongoing_subtopiclist[$keyval];
		            			}

		            			$success =  $filter_ongoing_subtopiclist;
		            		}
		            		else{
		            			$success =  $ongoing_subtopiclist;
		            		}
	            		}
	            		else{
	            			$success =  $ongoing_subtopiclist;
	            		}
	            	}
	            	elseif($type=="complete")
	            	{
	            		if($filter!="")
	            		{
		            		if(count($complete_subtopiclist) > 0)
		            		{
		            			$filtercompletekeys = array_keys(array_column($complete_subtopiclist, 'course_id'), $filter);
		            			$filter_complete_subtopiclist=[];
		            			foreach($filtercompletekeys as $keyval)
		            			{
		            				$filter_complete_subtopiclist[]=$complete_subtopiclist[$keyval];
		            			}

		            			$success =  $filter_complete_subtopiclist;
		            		}
		            		else{
		            			$success =  $complete_subtopiclist;
		            		}
	            		}
	            		else{
	            			$success =  $complete_subtopiclist;
	            		}
	            	}
	            	elseif($type=="all")
	            	{
	            		$success['new'] =  $new_subtopiclist;
	            		$success['ongoing'] =  $ongoing_subtopiclist;
	            		$success['completed'] =  $complete_subtopiclist;
	            	}
	            	else{
	            		$success=[];
	            	}
	            }
	            else{
	            	$success['new'] =  $new_subtopiclist;
	            	$success['ongoing'] =  $ongoing_subtopiclist;
	            	$success['completed'] =  $complete_subtopiclist;
	            }

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


    public function checkusertopicresult($userid,$sub_topic_id)
    {
    	$quiztopicdata=Quiztopic::where('course_topic',$sub_topic_id)->where('quiz_status','1')->where('quiz_type','1')->get()->first();
    	if($quiztopicdata)
    	{
    		$quiztopicdataarr=$quiztopicdata->toArray();
    		if($quiztopicdataarr)
    		{
    			$quizid=$quiztopicdataarr['id'];
    			$quizresultmarksdata=Resultmarks::where('user_id',$userid)->whereRaw('FIND_IN_SET(?, topic_id)', $quizid)->where('result_type','1')->get();
    			if($quizresultmarksdata)
    			{
    				$quizresultmarksdataarray=$quizresultmarksdata->toArray();

    				$result_id_arr=[];
    				foreach($quizresultmarksdataarray as $row)
    				{
    					$random_question_idsdb=$row['random_question_ids'];
    					$random_question_idsarray=json_decode($random_question_idsdb,true);

    					$random_question_ids=[];
		        		foreach($random_question_idsarray as $listval)
		        		{
		        			foreach($listval as $rowval)
		        			{
		        				$random_question_ids[]=$rowval;
		        			}
		        		}

		        		$attempt_question_idsdb=$row['question_ids'];
			        	if($attempt_question_idsdb!="")
			        	{
			        		$attempt_question_idsarray=json_decode($attempt_question_idsdb,true);

			        		$attempt_question_ids=[];
			        		foreach($attempt_question_idsarray as $listvalnew)
			        		{
			        			foreach($listvalnew as $rowvalnew)
			        			{
			        				$attempt_question_ids[]=$rowvalnew;
			        			}
			        		}

			        		if(count($attempt_question_ids) > 0)
			        		{
			        			$questions_array_diff=array_values(array_diff($random_question_ids, $attempt_question_ids));

			        			if(count($questions_array_diff) == 0)
			        			{
			        				$result_id_arr[]=$row['id'];
			        			}
			        		}
			        	}
    				}
    			}
    			else{
    				$result_id_arr=[];
    			}
    		}
    		else{
    			$result_id_arr=[];
    		}
    	}
    	else{
    		$result_id_arr=[];
    	}

    	return $result_id_arr;
    }


    public function completesubscribecourselist($userid,$subscribed_coursesubtopicsarray)
    {
    	$complete_subtopiclist=[];
    	if(count($subscribed_coursesubtopicsarray) > 0){
    	foreach($subscribed_coursesubtopicsarray as $arr)
    	{
   			$result_id_arr=$this->checkusertopicresult($userid,$arr);

			if(count($result_id_arr) > 0)
			{
				$ongoing_status=1;

				$result_id=max($result_id_arr);
				$resultmarksdet=Resultmarks::where('id', $result_id)->get()->first();
				if($resultmarksdet)
				{
					$resultmarksdetdata=$resultmarksdet->toArray();
					if($resultmarksdetdata)
					{
						$total_score=$resultmarksdetdata['total_marks'];
						$current_score=$resultmarksdetdata['marks'];
					}
					else{
						$total_score=0;
						$current_score=0;
					}
				}
				else{
					$total_score=0;
					$current_score=0;
				}	
			}
			else{
				$result_id="";
				$total_score=0;
				$current_score=0;
				$ongoing_status=0;
			}

    		$coursesubtopicsdet=Coursetopic::where('id', $arr)->get()->first();
    		if($coursesubtopicsdet)
    		{
    			$coursesubtopicsdetdata=$coursesubtopicsdet->toArray();
				if($coursesubtopicsdetdata)
				{
					$subject=$coursesubtopicsdetdata['subject'];

	    			if($coursesubtopicsdetdata['topic_video_id']!="")
					{
						$checkvideo=checkvimeovideoid($coursesubtopicsdetdata['topic_video_id']);
						if($checkvideo['code']=="400")
						{
	    					if($coursesubtopicsdetdata['topic_image']!="")
					        {
					        	$sub_topic_image=url('/').'/images/topics/'.$coursesubtopicsdetdata['topic_image'];
					        }
					        else{
					        	$sub_topic_image="";
					        }
						}
						else{
							if($checkvideo['sub_topic_image']!="")
							{
								$sub_topic_image=$checkvideo['sub_topic_image'];
							}
							else{
	    						if($coursesubtopicsdetdata['topic_image']!="")
						        {
						        	$sub_topic_image=url('/').'/images/topics/'.$coursesubtopicsdetdata['topic_image'];
						        }
						        else{
						        	$sub_topic_image="";
						        }
							}
						}
					}
					else{
						if($coursesubtopicsdetdata['topic_image']!="")
				        {
				        	$sub_topic_image=url('/').'/images/topics/'.$coursesubtopicsdetdata['topic_image'];
				        }
				        else{
				        	$sub_topic_image="";
				        }

					}

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

    				if($ongoing_status=="1")
    				{
    					$complete_subtopiclist[]=array(
    					'sub_topic_id'=>$coursesubtopicsdetdata['id'],
    					'sub_topic_name'=>$coursesubtopicsdetdata['topic_name'],
    					'sub_topic_description'=>$coursesubtopicsdetdata['topic_description'],
    					'course_name'=>$course_name,
    					'thumbnail_image'=>$sub_topic_image,
    					'course_id'=>$subject,
    					'topic_id'=>$coursesubtopicsdetdata['category'],
    					'userid'=>$userid,
    					'result_id'=>$result_id,
    					'total_score'=>$total_score,
    					'current_score'=>$current_score
    					);
    				}
				}
    		}
    	}

    }
    else{
    	$complete_subtopiclist=[];
    }

    	return $complete_subtopiclist;
    }

    public function ongoingsubscribecourselist($userid,$subscribed_coursesubtopicsarray)
    {
    	$ongoingcoursesubtopicsdata=Coursetopic::whereIn('id', $subscribed_coursesubtopicsarray)->get();
    	if($ongoingcoursesubtopicsdata)
    	{
    		$ongoingcoursesubtopicsdataarray=$ongoingcoursesubtopicsdata->toArray();
    		if($ongoingcoursesubtopicsdataarray)
    		{
    			$ongoing_subtopiclist=[];
    			foreach($ongoingcoursesubtopicsdataarray as $list)
    			{

    				$result_id_arr=$this->checkusertopicresult($userid,$list['id']);

    				if(count($result_id_arr) > 0)
    				{
    					$ongoing_status=0;
    				}
    				else{
    					$ongoing_status=1;
    				}

    				$subject=$list['subject'];
    				if($list['topic_video_id']!="")
    				{
    					$checkvideo=checkvimeovideoid($list['topic_video_id']);
    					if($checkvideo['code']=="400")
    					{
	    					if($list['topic_image']!="")
					        {
					        	$sub_topic_image=url('/').'/images/topics/'.$list['topic_image'];
					        }
					        else{
					        	$sub_topic_image="";
					        }
    					}
    					else{
    						if($checkvideo['sub_topic_image']!="")
    						{
    							$sub_topic_image=$checkvideo['sub_topic_image'];
    						}
    						else{
	    						if($list['topic_image']!="")
						        {
						        	$sub_topic_image=url('/').'/images/topics/'.$list['topic_image'];
						        }
						        else{
						        	$sub_topic_image="";
						        }
    						}
    					}
    				}
    				else{
    					if($list['topic_image']!="")
				        {
				        	$sub_topic_image=url('/').'/images/topics/'.$list['topic_image'];
				        }
				        else{
				        	$sub_topic_image="";
				        }
    				}

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

    				if($ongoing_status=="1")
    				{
	    				$ongoing_subtopiclist[]=array(
	    					'sub_topic_id'=>$list['id'],
	    					'sub_topic_name'=>$list['topic_name'],
	    					'sub_topic_description'=>$list['topic_description'],
	    					'course_name'=>$course_name,
	    					'total_score'=>0,
	    					'current_score'=>0,
	    					'thumbnail_image'=>$sub_topic_image,
	    					'course_id'=>$subject,
	    					'topic_id'=>$list['category'],
	    					'userid'=>$userid
	    				);
    				}	
    			}
    		}
    		else{
    			$ongoing_subtopiclist=[];
    		}
    	}
    	else{
    		$ongoing_subtopiclist=[];
    	}

    	return $ongoing_subtopiclist;
    	
    }

    public function newongoingsubscribecourselist($userid,$subscribed_coursesubtopicsarray)
    {
    	$newcoursesubtopicsdata=Coursetopic::where('topic_status','1')->whereNotIn('id', $subscribed_coursesubtopicsarray)->get();
    	if($newcoursesubtopicsdata)
    	{
    		$newcoursesubtopicsdataarray=$newcoursesubtopicsdata->toArray();
    		if($newcoursesubtopicsdataarray)
    		{
    			$new_subtopiclist=[];
    			foreach($newcoursesubtopicsdataarray as $list)
    			{
    				$subject=$list['subject'];
    				if($list['topic_video_id']!="")
    				{
    					$checkvideo=checkvimeovideoid($list['topic_video_id']);
    					if($checkvideo['code']=="400")
    					{
	    					if($list['topic_image']!="")
					        {
					        	$sub_topic_image=url('/').'/images/topics/'.$list['topic_image'];
					        }
					        else{
					        	$sub_topic_image="";
					        }
    					}
    					else{
    						if($checkvideo['sub_topic_image']!="")
    						{
    							$sub_topic_image=$checkvideo['sub_topic_image'];
    						}
    						else{
	    						if($list['topic_image']!="")
						        {
						        	$sub_topic_image=url('/').'/images/topics/'.$list['topic_image'];
						        }
						        else{
						        	$sub_topic_image="";
						        }
    						}
    					}
    				}
    				else{
    					if($list['topic_image']!="")
				        {
				        	$sub_topic_image=url('/').'/images/topics/'.$list['topic_image'];
				        }
				        else{
				        	$sub_topic_image="";
				        }
    				}

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
    					'sub_topic_description'=>$list['topic_description'],
    					'course_name'=>$course_name,
    					'total_score'=>0,
    					'current_score'=>0,
    					'thumbnail_image'=>$sub_topic_image,
    					'course_id'=>$subject,
    					'topic_id'=>$list['category'],
    					'userid'=>$userid
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

    	return $new_subtopiclist;
    }

    public function newsubscribecourselist($userid)
    {
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
    				if($list['topic_video_id']!="")
    				{
    					$checkvideo=checkvimeovideoid($list['topic_video_id']);
    					if($checkvideo['code']=="400")
    					{
	    					if($list['topic_image']!="")
					        {
					        	$sub_topic_image=url('/').'/images/topics/'.$list['topic_image'];
					        }
					        else{
					        	$sub_topic_image="";
					        }
    					}
    					else{
    						if($checkvideo['sub_topic_image']!="")
    						{
    							$sub_topic_image=$checkvideo['sub_topic_image'];
    						}
    						else{
	    						if($list['topic_image']!="")
						        {
						        	$sub_topic_image=url('/').'/images/topics/'.$list['topic_image'];
						        }
						        else{
						        	$sub_topic_image="";
						        }
    						}
    					}
    				}
    				else{
    					if($list['topic_image']!="")
				        {
				        	$sub_topic_image=url('/').'/images/topics/'.$list['topic_image'];
				        }
				        else{
				        	$sub_topic_image="";
				        }
    				}

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
    					'sub_topic_description'=>$list['topic_description'],
    					'course_name'=>$course_name,
    					'total_score'=>0,
    					'current_score'=>0,
    					'thumbnail_image'=>$sub_topic_image,
    					'course_id'=>$subject,
    					'topic_id'=>$list['category'],
    					'userid'=>$userid
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

    	return $new_subtopiclist;
    }

    public function updatecoursetopicongoingstatus(Request $request)
    {
    	try{
    		$user=auth()->user();
    		
    		if($user)
    		{
		        $userid=$user->id;
		        if(isset($request->sub_topic_id))
		        {
		        	$subtopicid=$request->sub_topic_id;
		        	$coursesubtopic = Coursetopic::find($subtopicid);
		        	if($coursesubtopic)
		        	{
		        		$userdet=User::find($userid);
		        		if($userdet)
		        		{
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

						        $userdet->save();

					            $success=[];
					            return $this::sendResponse($success, 'Subtopic Subscribed successfully');
				            }
		        		}
		        	}
		        }                   
    		}	
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
               }
    }


  

    

}