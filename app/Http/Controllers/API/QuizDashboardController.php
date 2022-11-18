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
use App\Question;
use Validator;
use Hash;

class QuizDashboardController extends BaseController
{
    public function getsubtopicquizquestions(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'course_id'=>'required'
		            'topic_id'=>'required',
		            'sub_topic_id'=>'required',
		            'quiz_id'=>'required'
		        ]);

	        	$courseid=$request->course_id;
	        	$courseid=base64_decode($courseid);

	        	$topicid=$request->topic_id;
	        	$topicid=base64_decode($topicid);

	        	$subtopicid=$request->sub_topic_id;
	        	$subtopicid=base64_decode($subtopicid);

	        	$quizid=$request->quiz_id;
	        	$quizid=base64_decode($quizid);

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

		        		$quiztopicdetail=Quiztopic::where('subject',$courseid)->where('category',$topicid)->where('course_topic',$subtopicid)->where('id',$quizid)->get()->first();

			        	if($quiztopicdetail)
			        	{
			        		$quiztopicdetaildata=$quiztopicdetail->toArray();

			        		$questionslist=[];

			        		$success['questionslist'] =  $questionslist;
                			return $this::sendResponse($success, 'Questions List.');
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

    public function getquizquestions(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'quiz_id'=>'required'
		        ]);

		        $quiztopicdetail=Quiztopic::where('id',$quizid)->get()->first();

	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

	        		$questionslist=[];

	        		$success['questionslist'] =  $questionslist;
        			return $this::sendResponse($success, 'Questions List.');
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



}