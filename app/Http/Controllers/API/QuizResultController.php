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
use App\Result;
use App\Resultmarks;
use Validator;
use Hash;

class QuizResultController extends BaseController
{
	public function getquizresultminisummary(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'quiz_id'=>'required'
		        ]);

		        $quizid=$request->quiz_id;

	        	$result_date=date('Y-m-d');

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

	        		$quizresultmarksdetail=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->get()->first();


	        		$quizcorrectresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_date',$result_date)->where('answer','1')->get();

	        		if($quizcorrectresultdetail)
	        		{
	        			$correct_questions=$quizcorrectresultdetail->count();
	        		}
	        		else{
	        			$correct_questions=0;
	        		}

	        		$quizincorrectresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_date',$result_date)->where('answer','2')->get();

	        		if($quizincorrectresultdetail)
	        		{
	        			$incorrect_questions=$quizincorrectresultdetail->count();
	        		}
	        		else{
	        			$incorrect_questions=0;
	        		}

	        		$quizskipresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_date',$result_date)->where('answer','0')->get();

	        		if($quizskipresultdetail)
	        		{
	        			$skip_questions=$quizskipresultdetail->count();
	        		}
	        		else{
	        			$skip_questions=0;
	        		}

	        		if($quizresultmarksdetail)
	        		{
	        			$quizresultmarksdetaildata=$quizresultmarksdetail->toArray();
	        			$total_marks=$quizresultmarksdetaildata['total_marks'];
	        			$result_marks=$quizresultmarksdetaildata['marks'];

	        			if($total_marks==0)
	        			{
	        				$total_score=0;
	        			}
	        			else{
	        				$total_score=($result_marks/$total_marks)*100;
	        			}

		        		$resultarray=array(
		        			'total_questions'=>$quizresultmarksdetaildata['total_questions'],
		        			'correct_questions'=>$correct_questions,
		        			'incorrect_questions'=>$incorrect_questions,
		        			'skip_questions'=>$skip_questions,
		        			'total_score'=>$total_score,
		        			'total_time'=>$quizresultmarksdetaildata['result_timer'],
		        			'topic_id'=>$quizid
		        		);
	        		}
	        		else{
		        		$resultarray=array(
		        			'total_questions'=>0,
		        			'correct_questions'=>$correct_questions,
		        			'incorrect_questions'=>$incorrect_questions,
		        			'skip_questions'=>$skip_questions,
		        			'total_score'=>0,
		        			'total_time'=>0,
		        			'topic_id'=>$quizid
		        		);
	        		}

	        		$success['resultarray'] =  $resultarray;
                	return $this::sendResponse($success, 'Quiz Result Mini summary.');
	        		

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

    public function viewquizresultquestionsummary(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'quiz_id'=>'required'
		        ]);

		        $quizid=$request->quiz_id;

	        	$result_date=date('Y-m-d');

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

	        		$quizresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_date',$result_date)->get();

	        		if($quizresultdetail)
	        		{
	        			$quizresultdetaildata=$quizresultdetail->toArray();
	        		}
	        		else{
	        			$quiz_result=[];
	        		}

	        		$success['quiz_result'] =  $quiz_result;
                	return $this::sendResponse($success, 'Quiz Result Questions summary.');

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