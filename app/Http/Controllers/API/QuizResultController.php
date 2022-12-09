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
		        $validator = Validator::make($request->all(), [
		            'quiz_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

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
		        			'result_date'=>$quizresultmarksdetaildata['result_marks_date'],
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
		        			'result_date'=>$result_date,
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }

    }

    public function viewquizresultquestionsummary(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'result_date'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$result_date=$request->result_date;

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

	        		$subtopicid=$quiztopicdetaildata['course_topic'];

	        		$coursetopic = Coursetopic::find($subtopicid);
			         if(is_null($coursetopic)){
					   $sub_topic_title="";
					}
					else{
						$sub_topic_title=$coursetopic->topic_name;
					}

	        		$quizresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_date',$result_date)->get();

	        		if($quizresultdetail)
	        		{
	        			$quizresultdetaildata=$quizresultdetail->toArray();

	        			$quiz_result=[];
	        			foreach($quizresultdetaildata as $list)
	        			{
	        				$questiondet=Question::where('topic_id',$list['topic_id'])->where('id',$list['question_id'])->get()->first();

	        				if($questiondet)
	        				{
	        					$questiondetarray=$questiondet->toArray();
	        					$question=$questiondetarray['question'];
	        					$correct_answer=$questiondetarray['answer'];
	        					$answer_explaination=$questiondetarray['answer_exp'];
	        				}
	        				else{
	        					$question="";
	        					$correct_answer="";
	        					$answer_explaination="";

	        				}

	        				$quiz_result[]=array(
	        					'question'=>$question,
	        					'correct_answer'=>$correct_answer,
	        					'answer_explaination'=>$answer_explaination,
	        					'user_answer'=>$list['user_answer'],
	        					'answer_status'=>$list['answer']
	        				);
	        			}

	        		}
	        		else{
	        			$quiz_result=[];
	        		}

	        		$success['quiz_result'] =  $quiz_result;
	        		$success['sub_topic_title']=$sub_topic_title;
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

    public function manageuserresult(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        $quizresultmarks=Resultmarks::where('user_id',$user->id)->get();
	        if($quizresultmarks)
	        {
	        	$quizresultmarksarray=$quizresultmarks->toArray();

	        	$user_result=[];
	        	foreach($quizresultmarksarray as $list)
	        	{
	        		$quizid=$list['topic_id'];
	        		$result_date=$list['result_marks_date'];

	        		$quiztopicdetail=Quiztopic::where('id',$quizid)->get()->first();
		        	if($quiztopicdetail)
		        	{
		        		$quiztopicdetaildata=$quiztopicdetail->toArray();
		        		$subtopicid=$quiztopicdetaildata['course_topic'];
		        		$category=$quiztopicdetaildata['category'];

		        		$coursetopic = Coursetopic::find($subtopicid);
				         if(is_null($coursetopic)){
						   $sub_topic_title="";
						}
						else{
							$sub_topic_title=$coursetopic->topic_name;
						}

						$subjectcategory = Subjectcategory::find($category);
				         if(is_null($subjectcategory)){
						   $topic_title="";
						}
						else{
							$topic_title=$subjectcategory->category_name;
						}

		        	}
		        	else{
		        		$sub_topic_title="";
		        		$topic_title="";
		        	}

	        		$quizcorrectresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','1')->get();

	        		if($quizcorrectresultdetail)
	        		{
	        			$correct_questions=$quizcorrectresultdetail->count();
	        		}
	        		else{
	        			$correct_questions=0;
	        		}

	        		$quizincorrectresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','2')->get();

	        		if($quizincorrectresultdetail)
	        		{
	        			$incorrect_questions=$quizincorrectresultdetail->count();
	        		}
	        		else{
	        			$incorrect_questions=0;
	        		}

	        		$quizskipresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','0')->get();

	        		if($quizskipresultdetail)
	        		{
	        			$skip_questions=$quizskipresultdetail->count();
	        		}
	        		else{
	        			$skip_questions=0;
	        		}

	        		$total_marks=$list['total_marks'];
        			$result_marks=$list['marks'];

        			if($total_marks==0)
        			{
        				$total_score=0;
        			}
        			else{
        				$total_score=($result_marks/$total_marks)*100;
        			}

        			$result_marks_date=date('d M, Y',strtotime($list['result_marks_date']));

	        		$user_result[]=array(
	        				'sub_topic_title'=>$sub_topic_title,
	        				'topic_title'=>$topic_title,
		        			'total_questions'=>$list['total_questions'],
		        			'correct_questions'=>$correct_questions,
		        			'incorrect_questions'=>$incorrect_questions,
		        			'skip_questions'=>$skip_questions,
		        			'total_score'=>$total_score,
		        			'total_time'=>$list['result_timer'],
		        			'result_date'=>$result_marks_date,
		        			'topic_id'=>$quizid
		        		);
	        	}

	        }
	        else{
	        	$user_result=[];
	        }

	        $success['user_result'] =  $user_result;
            return $this::sendResponse($success, 'Manage User Result.');

	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	        }
	    }
	    catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
               }

    }

    public function viewuserresult(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'result_date'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$result_date=date('Y-m-d',strtotime($request->result_date));

	        $quiztopicdetail=Quiztopic::where('id',$quizid)->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();
	        		$subtopicid=$quiztopicdetaildata['course_topic'];
	        		$category=$quiztopicdetaildata['category'];

	        		$coursetopic = Coursetopic::find($subtopicid);
			         if(is_null($coursetopic)){
					   $sub_topic_title="";
					}
					else{
						$sub_topic_title=$coursetopic->topic_name;
					}

					$subjectcategory = Subjectcategory::find($category);
			         if(is_null($subjectcategory)){
					   $topic_title="";
					}
					else{
						$topic_title=$subjectcategory->category_name;
					}

	        	}
	        	else{
	        		$sub_topic_title="";
	        		$topic_title="";
	        	}

	        	$quizresultmarks=Resultmarks::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_marks_date',$result_date)->get()->first();

	        	if($quizresultmarks)
	        	{
	        		$quizresultmarksarray=$quizresultmarks->toArray();

	        		$quizcorrectresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','1')->get();

	        		if($quizcorrectresultdetail)
	        		{
	        			$correct_questions=$quizcorrectresultdetail->count();
	        		}
	        		else{
	        			$correct_questions=0;
	        		}

	        		$quizincorrectresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','2')->get();

	        		if($quizincorrectresultdetail)
	        		{
	        			$incorrect_questions=$quizincorrectresultdetail->count();
	        		}
	        		else{
	        			$incorrect_questions=0;
	        		}

	        		$quizskipresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','0')->get();

	        		if($quizskipresultdetail)
	        		{
	        			$skip_questions=$quizskipresultdetail->count();
	        		}
	        		else{
	        			$skip_questions=0;
	        		}

	        		$total_marks=$quizresultmarksarray['total_marks'];
        			$result_marks=$quizresultmarksarray['marks'];

        			if($total_marks==0)
        			{
        				$total_score=0;
        			}
        			else{
        				$total_score=($result_marks/$total_marks)*100;
        			}

        			$result_marks_date=date('d M, Y',strtotime($result_date));

	        		$resultdet=array(
	        			'sub_topic_title'=>$sub_topic_title,
	        				'topic_title'=>$topic_title,
		        			'total_questions'=>$quizresultmarksarray['total_questions'],
		        			'correct_questions'=>$correct_questions,
		        			'incorrect_questions'=>$incorrect_questions,
		        			'skip_questions'=>$skip_questions,
		        			'total_score'=>$total_score,
		        			'total_time'=>$quizresultmarksarray['result_timer'],
		        			'result_date'=>$result_marks_date,
		        			'topic_id'=>$quizid
		        		);
	        	}
	        	else{

	        		$result_marks_date=date('d M, Y',strtotime($result_date));

	        		$resultdet=array(
		        			'total_questions'=>0,
		        			'correct_questions'=>0,
		        			'incorrect_questions'=>0,
		        			'skip_questions'=>0,
		        			'total_score'=>0,
		        			'total_time'=>0,
		        			'result_date'=>$result_marks_date,
		        			'topic_id'=>$quizid
		        		);
	        	}

	        $success['resultdet'] =  $resultdet;
            return $this::sendResponse($success, 'View User Result.');

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