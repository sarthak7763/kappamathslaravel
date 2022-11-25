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
use Validator;
use Hash;

class DashboardController extends BaseController
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

	        	$success['courselist'] =  $courselist;
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

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }


		        $coursetopicsdata=Subjectcategory::where('subject',$courseid)->where('category_status','1')->get();
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

		        			$topicslist[]=array(
		        				'topic_id'=>$list['id'],
		        				'topic_name'=>$list['category_name'],
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


    public function getcoursetopicslist(Request $request)
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

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }


		        $coursetopicsdata=Subjectcategory::where('subject',$courseid)->where('category_status','1')->get();
		        if($coursetopicsdata)
		        {
		        	$coursetopicsdataarray=$coursetopicsdata->toArray();
		        	if($coursetopicsdataarray)
		        	{
		        		$topicslist=[];
		        		foreach($coursetopicsdataarray as $list)
		        		{
		        			$topicslist[]=array(
		        				'topic_id'=>$list['id'],
		        				'topic_name'=>$list['category_name']
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

		        $success['topicslist'] =  $topicslist;
                return $this::sendResponse($success, 'Topics List.');
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

		        	if($coursesubtopicsdetaildata['topic_image']!="")
			        {
			        	$sub_topic_image=url('/').'/images/topics/'.$coursesubtopicsdetaildata['topic_image'];
			        }
			        else{
			        	$sub_topic_image='';
			        }

			        $sort_order=$coursesubtopicsdetaildata['sort_order'];

			        $coursesubtopicsdetailnext=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('sort_order','>',$sort_order)->where('topic_status',1)->orderBy('sort_order','ASC')->get()->first();
			        if($coursesubtopicsdetailnext)
			        {
			        	$coursetopicsdetailnextdata=$coursesubtopicsdetailnext->toArray();
			        	$next_topic_key=$coursetopicsdetailnextdata['id'];
			        }
			        else{
			        	$next_topic_key="0";
			        }

			        $coursesubtopicsdetailprevious=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('sort_order','<',$sort_order)->where('topic_status',1)->orderBy('sort_order','DESC')->get()->first();
			        if($coursesubtopicsdetailprevious)
			        {
			        	$coursetopicsdetailpreviousdata=$coursesubtopicsdetailprevious->toArray();
			        	$previous_topic_key=$coursetopicsdetailpreviousdata['id'];
			        }
			        else{
			        	$previous_topic_key="0";
			        }

			        $quiztopicdata=Quiztopic::where('subject',$courseid)->where('category',$topicid)->where('course_topic',$subtopicid)->where('quiz_status','1')->get();
			        if($quiztopicdata)
			        {
			        	$quiztopicdataarray=$quiztopicdata->toArray();
			        	if($quiztopicdataarray)
			        	{
			        		$quizarray=[];
			        		foreach($quiztopicdataarray as $key=>$list)
			        		{
			        			$quizarray[]=array(
			        				'quiz_id'=>$list['id'],
			        				'quiz_title'=>$list['title'],
			        				'quiz_type'=>$list['quiz_type']
			        			);
			        		}
			        	}
			        	else{
			        		$quizarray=[];
			        	}
			        }
			        else{
			        	$quizarray=[];
			        }

		        		$subtopicdetail=array(
		        				'course_name'=>$subject->title,
		        				'topic_name'=>$coursetopicsdetaildata['category_name'],
		        				'sub_topic_id'=>$coursesubtopicsdetaildata['id'],
		        				'sub_topic_name'=>$coursesubtopicsdetaildata['topic_name'],
		        				'sub_topic_description'=>$coursesubtopicsdetaildata['topic_description'],
		        				'sub_topic_image'=>$sub_topic_image,
		        				'sub_topic_video_id'=>$coursesubtopicsdetaildata['topic_video_id'],
		        				'quiz_topics'=>$quizarray,
		        				'previous_topic_key'=>$previous_topic_key,
		        				'next_topic_key'=>$next_topic_key

		        			);

		        	$success['subtopicdetail'] =  $subtopicdetail;
                	return $this::sendResponse($success, 'Sub Topics Detail.');

		        	}
		        	else{
		        		return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        	}

		        }
		        else{
		        	return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
               }

    }



}