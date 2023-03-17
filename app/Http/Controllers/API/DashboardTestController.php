<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Subject;
use App\Subjectcategory;
use App\Coursetopic;
use App\Quiztopic;
use App\Homebanner;
use App\Resultmarks;
use App\Subscription;
use App\Usersubscriptions;
use Validator;
use Hash;

class DashboardTestController extends BaseController
{

    public function getallcourseslist()
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$subjectdata=Subject::where('status','1')->get();
	        	if($subjectdata)
	        	{
	        		$subjectdataarray=$subjectdata->toArray();
	        		if($subjectdataarray)
	        		{
	        			$courselist=[];
	        			foreach($subjectdataarray as $list)
	        			{

	        				$coursetopicsdata=Subjectcategory::where('subject',$list['id'])->where('category_status','1')->get();
	        				if($coursetopicsdata)
	        				{
	        					$coursetopics=count($coursetopicsdata);
	        				}
	        				else{
	        					$coursetopics=0;
	        				}

	        				if($list['image']!="")
					        {
					        	$courseimage=url('/').'/images/subjects/'.$list['image'];
					        }
					        else{
					        	$courseimage=url('/').'/images/user/profile.png';
					        }

	        				$courselist[]=array(
	        					'course_id'=>$list['id'],
	        					'title'=>$list['title'],
	        					'image'=>$courseimage,
	        					'topics'=>$coursetopics,
	        					'description'=>$list['description']
	        				);
	        			}
	        		}
	        		else{
	        			$courselist=[];
	        		}
	        	}
	        	else{
	        		$courselist=[];
	        	}

	        	$homebannerdata=Homebanner::orderBy('id','DESC')->get()->first();
		          if($homebannerdata)
		          {
		            $homebannerdataarray=$homebannerdata->toArray();
		            $home_banner=array(
		              'banner_type'=>$homebannerdataarray['banner_type'],
		              'title'=>$homebannerdataarray['title'],
		              'sub_title'=>$homebannerdataarray['sub_title'],
		              'event_date'=>$homebannerdataarray['event_date'],
		              'event_link'=>$homebannerdataarray['event_link']
		            );
		          }
		          else{
		            $home_banner=[];
		          }

		          $package_info=array(
		          	'name'=>'free trial',
		          	'days'=>'10'
		          );

	        	$success['courselist'] =  $courselist;
	        	$success['home_banner'] =  $home_banner;
	        	$success['package_info'] =  $package_info;
                return $this::sendResponse($success, 'Courses List.');
		        
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }  
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }


    public function getcoursetopicsandsubtopicslist(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'course_id' => 'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

	        	$courseid=$request->course_id;
	        	$search=$request->search;

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        if(isset($search) && $search!="")
		        {
		        	$coursetopicsdata=Subjectcategory::where('subject',$courseid)->where('category_name', 'like', '%'.$search.'%')->where('category_status','1')->get();
		        }
		        else{
		        	$coursetopicsdata=Subjectcategory::where('subject',$courseid)->where('category_status','1')->get();
		        }
		        
		        if($coursetopicsdata)
		        {
		        	$coursetopicsdataarray=$coursetopicsdata->toArray();
		        	if($coursetopicsdataarray)
		        	{
		        		$topicslist=[];
		        		foreach($coursetopicsdataarray as $key=>$list)
		        		{
		        			$coursesubtopicsdata=Coursetopic::where('subject',$courseid)->where('category',$list['id'])->where('topic_status','1')->get();

		        			if($coursesubtopicsdata)
		        			{
		        				$coursesubtopicsdataarray=$coursesubtopicsdata->toArray();
		        				if($coursesubtopicsdataarray)
		        				{
		        					$subtopicslist=[];
		        					foreach($coursesubtopicsdataarray as $row)
		        					{
			        						$subtopicslist[]=array(
					        				'sub_topic_id'=>$row['id'],
					        				'sub_topic_name'=>$row['topic_name']
					        			);
		        					}
		        				}
		        				else{
		        					$subtopicslist=[];
		        				}
		        			}
		        			else{
		        				$subtopicslist=[];
		        			}

		        	if($list['category_image']!="")
			        {
			        	$topic_image=url('/').'/images/subjectcategory/'.$list['category_image'];
			        }
			        else{
			        	$topic_image='';
			        }

		        			$topicslist[]=array(
		        				'course_name'=>$subject->title,
		        				'topic_id'=>$list['id'],
		        				'topic_name'=>$list['category_name'],
		        				'topic_description'=>$list['category_description'],
		        				'topic_image'=>$topic_image,
		        				'sub_topics'=>$subtopicslist
		        			);
		        		}
		        	}
		        	else{
		        		$topicslist=[];
		        	}
		        }
		        else{
		        	$topicslist=[];
		        }

		        $success['course_name']=$subject->title;
		        $success['topicslist'] =  $topicslist;
                return $this::sendResponse($success, 'Sub Topics List.');
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }  
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
               }
    }


    public function getcoursetopicssubtopicsearchlist(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'search' => 'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

	        	$search=$request->search;

		        $coursetopicsdata=Subjectcategory::where('category_status','1')->where('category_name', 'like', '%'.$search.'%')->get();
		        if($coursetopicsdata)
		        {
		        	$coursetopicsdataarray=$coursetopicsdata->toArray();
		        	if($coursetopicsdataarray)
		        	{
		        		$topicslist=[];
		        		foreach($coursetopicsdataarray as $list)
		        		{
		        			if($list['category_description']!="")
		        			{
		        				$textstatus=1;
		        			}
		        			else{
		        				$textstatus=0;
		        			}

		        			$subject = Subject::find($list['subject']);
					         if(is_null($subject)){
					           $course_name="";
					        }
					        else{
					        	$course_name=$subject->title;
					        }

		        			$topicslist[]=array(
		        				'id'=>$list['id'],
		        				'course_id'=>$list['subject'],
		        				'name'=>$list['category_name'],
		        				'type'=>'topic',
		        				'video'=>0,
		        				'text'=>$textstatus,
		        				'course_name'=>$course_name
		        			);
		        		}
		        	}
		        	else{
		        		$topicslist=[];
		        	}
		        }
		        else{
		        	$topicslist=[];
		        }

		        $coursesubtopicsdata=Coursetopic::where('topic_status','1')->where('topic_name', 'like', '%'.$search.'%')->get();
		        if($coursesubtopicsdata)
		        {
		        	$coursesubtopicsdataarray=$coursesubtopicsdata->toArray();
		        	if($coursesubtopicsdataarray)
		        	{
		        		$subtopicslist=[];
		        		foreach($coursesubtopicsdataarray as $row)
		        		{

		        			$subject = Subject::find($row['subject']);
					         if(is_null($subject)){
					           $course_name="";
					        }
					        else{
					        	$course_name=$subject->title;
					        }

		        			if($row['topic_video_id']!="")
		        			{
		        				$videostatus=1;
		        			}
		        			else{
		        				$videostatus=0;
		        			}

		        			if($row['topic_description']!="")
		        			{
		        				$textstatus=1;
		        			}
		        			else{
		        				$textstatus=0;
		        			}
		        			
		        			$subtopicslist[]=array(
		        				'id'=>$row['id'],
		        				'course_id'=>$row['subject'],
		        				'topic_id'=>$row['category'],
		        				'name'=>$row['topic_name'],
		        				'type'=>'subtopic',
		        				'video'=>$videostatus,
		        				'text'=>$textstatus,
		        				'course_name'=>$course_name
		        			);
		        		}
		        	}
		        	else{
		        		$subtopicslist=[];
		        	}
		        }
		        else{
		        	$subtopicslist=[];
		        }

		        $finalsearch_array=array_merge($topicslist,$subtopicslist);


		        $success['search_array'] =  $finalsearch_array;
                return $this::sendResponse($success, 'Topics and Subtopics Search List.');
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }  
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
               }
    }

    public function gettopicdetailpage(Request $request)
    {
    	try{
    		$user=auth()->user();
    		if($user)
    		{

		        $validator = Validator::make($request->all(), [
		            'course_id'=>'required',
		            'topic_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $courseid=$request->course_id;
	        	$topicid=$request->topic_id;

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $coursetopicsdetail=Subjectcategory::where('subject',$courseid)->where('id',$topicid)->get()->first();

		        if($coursetopicsdetail)
		        {
		        	$coursetopicsdetaildata=$coursetopicsdetail->toArray();

		        	if($coursetopicsdetaildata['category_image']!="")
			        {
			        	$topic_image=url('/').'/images/subjectcategory/'.$coursetopicsdetaildata['category_image'];
			        }
			        else{
			        	$topic_image='';
			        }


			        $coursesubtopicsdata=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('topic_status','1')->get();

		        			if($coursesubtopicsdata)
		        			{
		        				$coursesubtopicsdataarray=$coursesubtopicsdata->toArray();
		        				if($coursesubtopicsdataarray)
		        				{
		        					$subtopicslist=[];
		        					foreach($coursesubtopicsdataarray as $row)
		        					{
			        						$subtopicslist[]=array(
					        				'sub_topic_id'=>$row['id'],
					        				'sub_topic_name'=>$row['topic_name']
					        			);
		        					}
		        				}
		        				else{
		        					$subtopicslist=[];
		        				}
		        			}
		        			else{
		        				$subtopicslist=[];
		        			}

		        	$topicdetail=array(
		        				'course_name'=>$subject->title,
		        				'topic_id'=>$coursetopicsdetaildata['id'],
		        				'topic_name'=>$coursetopicsdetaildata['category_name'],
		        				'topic_description'=>$coursetopicsdetaildata['category_description'],
		        				'topic_image'=>$topic_image,
		        				'sub_topics'=>$subtopicslist
		        			);

		        	$success['topicdetail'] =  $topicdetail;
                	return $this::sendResponse($success, 'Topics Detail.');
		        }
		        else{
		        	return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
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


    public function getsubtopicdetails(Request $request)
    {
    	try{
    		$user=auth()->user();

    		if($user){
    		$validator = Validator::make($request->all(), [
		            'course_id'=>'required',
		            'topic_id'=>'required',
		            'sub_topic_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $courseid=$request->course_id;
	        	$topicid=$request->topic_id;
	        	$subtopicid=$request->sub_topic_id;

	        	$checkusersubscription=checkusersubscription($user->id);

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $coursetopicsdetail=Subjectcategory::where('subject',$courseid)->where('id',$topicid)->get()->first();

		        if($coursetopicsdetail)
		        {

		        	$coursetopicsdetaildata=$coursetopicsdetail->toArray();

		        	$coursesubtopicsdetail=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('id',$subtopicid)->get()->first();
		        	if($coursesubtopicsdetail)
		        	{
		        		$coursesubtopicsdetaildata=$coursesubtopicsdetail->toArray();

			        $sort_order=$coursesubtopicsdetaildata['sort_order'];

			        $coursesubtopicsdetailnext=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('sort_order','>',$sort_order)->where('topic_status',1)->orderBy('sort_order','ASC')->get()->first();
			        if($coursesubtopicsdetailnext)
			        {
			        	$coursetopicsdetailnextdata=$coursesubtopicsdetailnext->toArray();
			        	$next_topic_key=(int)$coursetopicsdetailnextdata['id'];
			        }
			        else{
			        	$next_topic_key=0;
			        }

			        $coursesubtopicsdetailprevious=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('sort_order','<',$sort_order)->where('topic_status',1)->orderBy('sort_order','DESC')->get()->first();
			        if($coursesubtopicsdetailprevious)
			        {
			        	$coursetopicsdetailpreviousdata=$coursesubtopicsdetailprevious->toArray();
			        	$previous_topic_key=(int)$coursetopicsdetailpreviousdata['id'];
			        }
			        else{
			        	$previous_topic_key=0;
			        }

			        $quiztopicdata=Quiztopic::where('subject',$courseid)->where('category',$topicid)->where('course_topic',$subtopicid)->where('quiz_status','1')->where('quiz_type','1')->get()->first();

			        if($quiztopicdata)
			        {
			        	$quiztopicdataarray=$quiztopicdata->toArray();
			        	$quizid=$quiztopicdataarray['id'];
			        	$result_date=date('Y-m-d H:i:s');

			        	$randomquizresultmarks=Resultmarks::where('user_id',$user->id)->whereRaw('"'.$result_date.'" between `result_marks_date` and `result_marks_end_date`')->whereRaw('FIND_IN_SET(?, topic_id)', $quizid)->where('result_type','1')->get()->first();
			        	if($randomquizresultmarks)
			        	{
			        		$resultmarksdetail=$randomquizresultmarks->toArray();

			        		$random_question_idsdb=$resultmarksdetail['random_question_ids'];

			        		$random_question_idsarray=json_decode($random_question_idsdb,true);

			        		$random_question_ids=[];
			        		foreach($random_question_idsarray as $listval)
			        		{
			        			foreach($listval as $rowval)
			        			{
			        				$random_question_ids[]=$rowval;
			        			}
			        		}

			        		$attempt_question_idsdb=$resultmarksdetail['question_ids'];
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
		        				$result_marks_end_date=$resultmarksdetail['result_marks_end_date'];

		        				$quiz_retake_datetime = date("Y-m-d H:i:s", strtotime('+24 hours', strtotime($result_marks_end_date)));

		        				$quiz_retake_time=date('h:i a',strtotime($quiz_retake_datetime));

		        				$quiz_complete_status=0;

		        			}
		        			else{
		        				$quiz_complete_status=1;
			        			$quiz_retake_time=0;
		        			}
		        		}
		        		else{
		        			$quiz_complete_status=1;
			        		$quiz_retake_time=0;
		        		}

			        		}
			        		else{
			        			$quiz_complete_status=1;
			        			$quiz_retake_time=0;
			        		}

			        	}
			        	else{
			        		$quiz_complete_status=1;
			        		$quiz_retake_time=0;
			        	}
			        }
			        else{
			        	$quiz_complete_status=1;
			        	$quiz_retake_time=0;
			        }

			        

			        if($coursesubtopicsdetaildata['topic_video_id']!="")
			        {
			        	$curlSession = curl_init();
					    curl_setopt($curlSession, CURLOPT_URL, 'https://player.vimeo.com/video/'.$coursesubtopicsdetaildata['topic_video_id'].'/config');
					    curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
					    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

				    	$jsonData = json_decode(curl_exec($curlSession));
				    	curl_close($curlSession);

					    if(isset($jsonData->message))
					    {
					    	return $this::sendError('Unauthorised Exception.', ['error'=>$jsonData->message]);
					    }
					    else{
					    	if(count($jsonData->request->files->progressive) >0)
					    	{
					    		$subtopicvideourl=$jsonData->request->files->progressive[0]->url;
					    	}
					    	else{
					    		return $this::sendError('Unauthorised Exception.', ['error'=>'Invalid Video']);
					    	}
					    }
			        }
			        else{
			        	$subtopicvideourl="";
			        }

			        if($coursesubtopicsdetaildata['topic_image']!="")
			        {
			        	$sub_topic_image=url('/').'/images/topics/'.$coursesubtopicsdetaildata['topic_image'];
			        }
			        else{
			        	$sub_topic_image=$jsonData->video->thumbs->base;
			        }  
				    
		        		$subtopicdetail=array(
		        				'course_name'=>$subject->title,
		        				'topic_name'=>$coursetopicsdetaildata['category_name'],
		        				'sub_topic_id'=>$coursesubtopicsdetaildata['id'],
		        				'sub_topic_name'=>$coursesubtopicsdetaildata['topic_name'],
		        				'sub_topic_description'=>$coursesubtopicsdetaildata['topic_description'],
		        				'sub_topic_image'=>$sub_topic_image,
		        				'sub_topic_video_id'=>$subtopicvideourl,
		        				'previous_topic_key'=>$previous_topic_key,
		        				'next_topic_key'=>$next_topic_key,
		        				'quiz_complete_status'=>$quiz_complete_status,
		        				'quiz_retake_time'=>$quiz_retake_time

		        			);

		        	$success['subtopicdetail'] =  $subtopicdetail;
                	return $this::sendResponse($success, 'Sub Topics Detail.',$checkusersubscription);

		        	}
		        	else{
		        		return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        	}

		        }
		        else{
		        	return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
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


    public function gettransactiondetails(Request $request)
    {
    	try{
    		$user=auth()->user();

    		if($user){
    		$validator = Validator::make($request->all(), [
		            'reference_id'=>'required',
		            'subscription_id'=>'required'
		        ]);

    			if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

    			$reference_id=$request->reference_id;
    			$subscription_id=$request->subscription_id;

    			$checksubscription=Subscription::where('id',$subscription_id)->get()->first();

    			$verifytransaction_data=$this->verifytransaction($reference_id);

    			if($verifytransaction_data['code']==200)
    			{
    				$checksubscription=Subscription::where('id',$subscription_id)->get()->first();

    				$user_subscriptions_list=Usersubscriptions::where('user_id',$user->id)->where('subscription_status',1)->get();
    				if($user_subscriptions_list)
    				{
    					$user_subscriptions_listarr=$user_subscriptions_list->toArray();
    					if($user_subscriptions_listarr)
    					{
    						foreach($user_subscriptions_listarr as $list)
    						{
    							$usersubscriptionupdate=Usersubscriptions::find($list['id']);
    							$usersubscriptionupdate->subscription_status=0;
		            			$usersubscriptionupdate->save();
    						}
    					}
    				}

    				$currentdate=date('Y-m-d');
    				$subscription_date=$checksubscription->subscription_plan;
    				$subscription_tenure=$checksubscription->subscription_tenure;
    				
    				$subscription_end=date('Y-m-d', strtotime($currentdate. ' + '.$subscription_tenure.' '.$subscription_date.''));

    				$usersubscription = new Usersubscriptions;
		            $usersubscription->user_id=$user->id;
		            $usersubscription->subscription_id=$subscription_id;
		            $usersubscription->transaction_id=$verifytransaction_data['transactionid'];
		            $usersubscription->subscription_payment=$checksubscription->price;
		            $usersubscription->subscription_start=$currentdate;
		            $usersubscription->subscription_end=$subscription_end;
		            $usersubscription->subscription_status=1;
		            $usersubscription->save();


    				$success['reference_id'] =  $reference_id;
                	return $this::sendResponse($success, 'Payment success.');
    			}
    			else{
    				return $this::sendError('Unauthorised Exception.', ['error'=>$verifytransaction_data['message']]);
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

    public function verifytransaction($reference_id)
    {
		  $curl = curl_init();
		  
		  curl_setopt_array($curl, array(
		    CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$reference_id,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 30,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => "GET",
		    CURLOPT_HTTPHEADER => array(
		      "Authorization: Bearer ".env('Paystack_secret_key'),
		      "Cache-Control: no-cache",
		    ),
		  ));
		  
		  $response = curl_exec($curl);
		  $err = curl_error($curl);
		  curl_close($curl);

		  $resultarray=json_decode($response);
		  if($resultarray->status==1)
		  {
		  	$successdata=$resultarray->data;
		  	$transactionid=$successdata->id;

		  	$transactionstatus=$successdata->status;
		  	$returndata=array('code'=>200,'message'=>$resultarray->message,'transactionid'=>$transactionid,'transactionstatus'=>$transactionstatus);
		  }
		  else{
		  	$returndata=array('code'=>400,'message'=>$resultarray->message);
		  }
		  return $returndata;
    }


    public function checkusersubscription()
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$user_subscriptions_list=Usersubscriptions::where('user_id',$user->id)->where('subscription_status',1)->get()->first();
	        	if($user_subscriptions_list)
	        	{
	        		$user_subscriptions_listarr=$user_subscriptions_list->toArray();
	        		if($user_subscriptions_listarr)
	        		{
	        			$currentdate=date('Y-m-d');
	        			$subscription_start=$user_subscriptions_listarr['subscription_start'];
	        			$subscription_end=$user_subscriptions_listarr['subscription_end'];

	        			if($currentdate >= $subscription_start && $currentdate <= $subscription_end)
	        			{
	        				$success['check_status'] = 1;
	        				$success['subscription_start']=$subscription_start;
	        				$success['subscription_end']=$subscription_end;
                			return $this::sendResponse($success, 'Subscription success.');
	        			}
	        			else{
	        				$success['check_status'] = 0;
	        				$success['subscription_start']="";
	        				$success['subscription_end']="";
                			return $this::sendResponse($success, 'No Subscription available.');
	        			}
	        		}
	        		else{
	        			$success['check_status'] = 0;
	        			$success['subscription_start']="";
	        			$success['subscription_end']="";
                		return $this::sendResponse($success, 'No Subscription available.');
	        		}
	        	}
	        	else{
	        		$success['check_status'] = 0;
	        		$success['subscription_start']="";
	        		$success['subscription_end']="";
                	return $this::sendResponse($success, 'No Subscription available.');
	        	}
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