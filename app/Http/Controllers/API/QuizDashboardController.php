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

class QuizDashboardController extends BaseController
{
	public function getsubtopicobjectivequizquestions(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {
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

		        		$quiztopicdetail=Quiztopic::where('subject',$courseid)->where('category',$topicid)->where('course_topic',$subtopicid)->where('quiz_type','1')->get()->first();

		        		if($quiztopicdetail)
			        	{
			        		$quiztopicdetaildata=$quiztopicdetail->toArray();
			        		$quizid=$quiztopicdetaildata['id'];

			        		$quizallquestions=Question::where('topic_id',$quizid)->where('question_status','1')->get();
	        				if($quizallquestions)
	        				{
	        					$total_questions=count($quizallquestions);
	        				}
	        				else{
	        					$total_questions=0;
	        				}

	        				$total_score=$total_questions*$quiztopicdetaildata['per_q_mark'];

	        				$questiondata=Question::where('topic_id',$quizid)->where('question_status','1')->get()->first();

	        				if($questiondata)
			        		{
			        			$questiondataarray=$questiondata->toArray();

			        			if($questiondataarray['question_img']!="")
						        {
						        	$question_img=url('/').'/images/questions/'.$questiondataarray['question_img'];
						        }
						        else{
						        	$question_img='';
						        }

						        $questionslist=array(
			        				'course_name'=>$subject->title,
			        				'topic_name'=>$coursetopicsdetaildata['category_name'],
			        				'sub_topic_name'=>$coursesubtopicsdetaildata['topic_name'],
			        				'quiz_name'=>$quiztopicdetaildata['title'],
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondataarray['id'],
			        				'question'=>$questiondataarray['question'], 
			        				'a'=>$questiondataarray['a'], 
			        				'b'=>$questiondataarray['b'],
			        				'c'=>$questiondataarray['c'],
			        				'd'=>$questiondataarray['d'],
			        				'answer'=>$questiondataarray['answer'],
			        				'answer_exp'=>$questiondataarray['answer_exp'],
			        				'question_video_link'=>$questiondataarray['question_video_link'],
			        				'question_img'=>$question_img,
			        				'current_score'=>0,
			        				'total_score'=>$total_score
			        			);
			        		}
			        		else{
			        			$questionslist=[];
			        		}

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


	    public function getobjectivequizquestions(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'sub_topic_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $subtopicid=$request->sub_topic_id;

		        $quiztopicdetail=Quiztopic::where('course_topic',$subtopicid)->where('quiz_type','1')->get()->first();

	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();
	        		$quizid=$quiztopicdetaildata['id'];

	        		$quizallquestions=Question::where('topic_id',$quizid)->where('question_status','1')->get();
    				if($quizallquestions)
    				{
    					$total_questions=count($quizallquestions);
    				}
    				else{
    					$total_questions=0;
    				}

    				$total_score=$total_questions*$quiztopicdetaildata['per_q_mark'];


	        		$courseid=$quiztopicdetaildata['subject'];
		        	$topicid=$quiztopicdetaildata['category'];
		        	$subtopicid=$quiztopicdetaildata['course_topic'];

		        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $coursetopicsdetail=Subjectcategory::where('subject',$courseid)->where('id',$topicid)->get()->first();
		        if($coursetopicsdetail)
		        {
		        	$coursetopicsdetaildata=$coursetopicsdetail->toArray();
		        }
		        else{
		        	return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $coursesubtopicsdetail=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('id',$subtopicid)->get()->first();
		        	if($coursesubtopicsdetail)
		        	{
		        		$coursesubtopicsdetaildata=$coursesubtopicsdetail->toArray();
		        	}
		        	else{
		        		return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        	}

	        		$questiondata=Question::where('topic_id',$quizid)->where('question_status','1')->get()->first();
	        		if($questiondata)
	        		{

	        			$questiondataarray=$questiondata->toArray();

	        			if($questiondataarray['question_img']!="")
				        {
				        	$question_img=url('/').'/images/questions/'.$questiondataarray['question_img'];
				        }
				        else{
				        	$question_img='';
				        }

				        $questionslist=array(
			        				'course_name'=>$subject->title,
			        				'topic_name'=>$coursetopicsdetaildata['category_name'],
			        				'sub_topic_name'=>$coursesubtopicsdetaildata['topic_name'],
			        				'quiz_name'=>$quiztopicdetaildata['title'],
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondataarray['id'],
			        				'question'=>$questiondataarray['question'], 
			        				'a'=>$questiondataarray['a'], 
			        				'b'=>$questiondataarray['b'],
			        				'c'=>$questiondataarray['c'],
			        				'd'=>$questiondataarray['d'],
			        				'answer'=>$questiondataarray['answer'],
			        				'answer_exp'=>$questiondataarray['answer_exp'],
			        				'question_video_link'=>$questiondataarray['question_video_link'],
			        				'question_img'=>$question_img,
			        				'current_score'=>0,
			        				'total_score'=>$total_score

			        			);

	        		}
	        		else{
	        			$questionslist=[];
	        		}

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



    public function submitobjectivequizquestion(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required',
		            'answer'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;
	        	$user_anwer=$request->answer;
	        	$result_date=date('Y-m-d');

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

	        		$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->get()->first();

		        	if($questiondetail)
		        	{
		        		$questiondetaildata=$questiondetail->toArray();

		        	$sort_order=$questiondetaildata['sort_order'];

			        $questiondetailnext=Question::where('topic_id',$quizid)->where('sort_order','>',$sort_order)->where('question_status',1)->orderBy('sort_order','ASC')->get()->first();
			        if($questiondetailnext)
			        {
			        	$questiondetailnextdata=$questiondetailnext->toArray();
			        	$next_question_key=base64_encode($questiondetailnextdata['id']);
			        }
			        else{
			        	$next_question_key="0";
			        }

			        $questiondetailprevious=Question::where('topic_id',$quizid)->where('sort_order','<',$sort_order)->where('question_status',1)->orderBy('sort_order','DESC')->get()->first();
			        if($questiondetailprevious)
			        {
			        	$questiondetailpreviousdata=$questiondetailprevious->toArray();
			        	$previous_question_key=base64_encode($questiondetailpreviousdata['id']);
			        }
			        else{
			        	$previous_question_key="0";
			        }

        			$quizallquestions=Question::where('topic_id',$quizid)->where('question_status','1')->get();
    				if($quizallquestions)
    				{
    					$total_questions=count($quizallquestions);
    				}
    				else{
    					$total_questions=0;
    				}

    				$total_marks=$total_questions*$quiztopicdetaildata['per_q_mark'];
    				$current_score=0;

		        		if($questiondetaildata['answer']==$user_anwer)
		        		{

		        			$quizresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_date',$result_date)->get()->first();

		        			if($quizresultdetail)
		        			{
		        				$quizresultdetaildata=$quizresultdetail->toArray();
		        				$resultid=$quizresultdetaildata['id'];

		        				$resultuserupdate = Result::find($resultid);
				                $resultuserupdate->user_answer=$user_anwer;
				                $resultuserupdate->answer=1;
				                $resultuserupdate->marks=$quiztopicdetaildata['per_q_mark'];

				                try{
						            $resultuserupdate->save();
						         }catch(\Exception $e){

						            return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
						         }

						   $questiondet=array(
		        			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
		        			'quiz_id'=>$quiztopicdetaildata['id'],
		        			'question_id'=>$questiondetaildata['id'],
		        			'answer_exp'=>$questiondetaildata['answer_exp'],
		        			'answer_status'=>1, //correct
		        			'previous_question_key'=>$previous_question_key,
		        			'next_question_key'=>$next_question_key,
		        			'current_score'=>$current_score,
			        		'total_score'=>$total_marks
			        		);

		        			}
		        			else{
		        				try{
				                    $resultuser = new Result;
				                    $resultuser->topic_id=$quiztopicdetaildata['id'];
				                    $resultuser->user_id=$user->id;
				                    $resultuser->question_id=$questiondetaildata['id'];
				                    $resultuser->user_answer=$user_anwer;
				                    $resultuser->answer=1;
				                    $resultuser->marks=$quiztopicdetaildata['per_q_mark'];
				                    $resultuser->result_date=$result_date;
				                    $resultuser->save();
				                     
				                  }catch(\Exception $e){
				                    return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);     
				                 }

				                 $questiondet=array(
		        			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
		        			'quiz_id'=>$quiztopicdetaildata['id'],
		        			'question_id'=>$questiondetaildata['id'],
		        			'answer_exp'=>$questiondetaildata['answer_exp'],
		        			'answer_status'=>1, //correct
		        			'previous_question_key'=>$previous_question_key,
		        			'next_question_key'=>$next_question_key,
		        			'current_score'=>$current_score,
			        		'total_score'=>$total_marks
			        		);
		        			}

		        			$quizresultmarksdetail=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->get()->first();

		        		
		        		}
		        		else{

			        		$quizresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_date',$result_date)->get()->first();

			        		if($quizresultdetail)
		        			{
		        				$quizresultdetaildata=$quizresultdetail->toArray();
		        				$resultid=$quizresultdetaildata['id'];

		        				$resultuserupdate = Result::find($resultid);
				                $resultuserupdate->user_answer=$user_anwer;
				                $resultuserupdate->answer=2;
				                $resultuserupdate->marks=0;

				                try{
						            $resultuserupdate->save();
						         }catch(\Exception $e){

						            return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
						         }

						         $questiondet=array(
		        			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
		        			'quiz_id'=>$quiztopicdetaildata['id'],
		        			'question_id'=>$questiondetaildata['id'],
		        			'answer_exp'=>$questiondetaildata['answer_exp'],
		        			'answer_status'=>2, //incorrect
		        			'previous_question_key'=>$previous_question_key,
		        			'next_question_key'=>$next_question_key,
		        			'current_score'=>$current_score,
			        		'total_score'=>$total_marks
			        		);

		        			}
		        			else{
		        				try{
				                    $resultuser = new Result;
				                    $resultuser->topic_id=$quiztopicdetaildata['id'];
				                    $resultuser->user_id=$user->id;
				                    $resultuser->question_id=$questiondetaildata['id'];
				                    $resultuser->user_answer=$user_anwer;
				                    $resultuser->answer=2;
				                    $resultuser->marks=0;
				                    $resultuser->result_date=$result_date;
				                    $resultuser->save();
				                     
				                  }catch(\Exception $e){
				                    return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);     
				                 }

				                 $questiondet=array(
		        			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
		        			'quiz_id'=>$quiztopicdetaildata['id'],
		        			'question_id'=>$questiondetaildata['id'],
		        			'answer_exp'=>$questiondetaildata['answer_exp'],
		        			'answer_status'=>2, //incorrect
		        			'previous_question_key'=>$previous_question_key,
		        			'next_question_key'=>$next_question_key,
		        			'current_score'=>$current_score,
			        		'total_score'=>$total_marks
			        		);
		        			}
		        		}

		        		$success['questiondet'] =  $questiondet;
        				return $this::sendResponse($success, 'Questions Details.');
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }

    }




    public function skipobjectivequizquestion(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'quiz_id'=>'required',
		            'question_id'=>'required'
		        ]);

		        $quizid=$request->quiz_id;
	        	$quizid=base64_decode($quizid);
	        	
	        	$questionid=$request->question_id;
	        	$questionid=base64_decode($questionid);

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

	        		$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->get()->first();

		        	if($questiondetail)
		        	{
		        		$questiondetaildata=$questiondetail->toArray();

		        	$sort_order=$questiondetaildata['sort_order'];

			        $questiondetailnext=Question::where('topic_id',$quizid)->where('sort_order','>',$sort_order)->where('question_status',1)->orderBy('sort_order','ASC')->get()->first();
			        if($questiondetailnext)
			        {
			        	$questiondetailnextdata=$questiondetailnext->toArray();
			        	$next_question_key=base64_encode($questiondetailnextdata['id']);
			        }
			        else{
			        	$next_question_key="0";
			        }

			        $questiondetailprevious=Question::where('topic_id',$quizid)->where('sort_order','<',$sort_order)->where('question_status',1)->orderBy('sort_order','DESC')->get()->first();
			        if($questiondetailprevious)
			        {
			        	$questiondetailpreviousdata=$questiondetailprevious->toArray();
			        	$previous_question_key=base64_encode($questiondetailpreviousdata['id']);
			        }
			        else{
			        	$previous_question_key="0";
			        }

			        $result_date=date('Y-m-d');
			        $quizresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_date',$result_date)->get()->first();

			        $quizresultmarksdetail=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->get()->first();

			        $quizresultmarksdetaildata=$quizresultmarksdetail->toArray();

				    if($quizresultmarksdetaildata)
				      {
				      	$current_score=$quizresultmarksdetaildata['marks'];
				      }
				      else{
				      	$current_score=0;
				      }

				      $quizallquestions=Question::where('topic_id',$quizid)->where('question_status','1')->get();
    				if($quizallquestions)
    				{
    					$total_questions=count($quizallquestions);
    				}
    				else{
    					$total_questions=0;
    				}

    				$total_marks=$total_questions*$quiztopicdetaildata['per_q_mark'];

				        $questiondet=array(
	        			'quiz_type'=>$quiztopicdetaildata['quiz_type']
	        			'quiz_id'=>base64_encode($quiztopicdetaildata['id']),
	        			'question_id'=>base64_encode($questiondetaildata['id']),
	        			'answer_exp'=>$questiondetaildata['answer_exp'],
	        			'answer_status'=>0 //skip,
	        			'previous_question_key'=>$previous_question_key,
	        			'next_question_key'=>$next_question_key,
	        			'current_score'=>$current_score,
			        	'total_score'=>$total_marks
		        		);


		        		if($quizresultdetail)
		        			{
		        				$quizresultdetaildata=$quizresultdetail->toArray();
		        				$resultid=$quizresultdetaildata['id'];

		        				$resultuserupdate = Result::find($resultid);
		        				$resultuserupdate->topic_id=$quiztopicdetaildata['id'];
				                $resultuserupdate->user_id=$user->id;
				                $resultuserupdate->question_id=$questiondetaildata['id'];
				                $resultuserupdate->user_answer=$user_anwer;
				                $resultuserupdate->answer=0;
				                $resultuserupdate->marks=0;
				                $resultuserupdate->result_date=$result_date;
				               	
				               		try{
						            $resultuserupdate->save();
						         }catch(\Exception $e){

						            return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
						         }

		        			}
		        			else{
			        				try{
				                    $resultuser = new Result;
				                    $resultuser->topic_id=$quiztopicdetaildata['id'];
				                    $resultuser->user_id=$user->id;
				                    $resultuser->question_id=$questiondetaildata['id'];
				                    $resultuser->user_answer=$user_anwer;
				                    $resultuser->answer=0;
				                    $resultuser->marks=0;
				                    $resultuser->result_date=$result_date;
				                    $resultuser->save();
				                     
				                  }catch(\Exception $e){
				                    return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);     
				                 }
		        			}

		        		$success['questiondet'] =  $questiondet;
        				return $this::sendResponse($success, 'Questions Details.');
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

    public function getobjectivequizquestionexplaination(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'quiz_id'=>'required',
		            'question_id'=>'required'
		        ]);

		        $quizid=$request->quiz_id;
	        	$quizid=base64_decode($quizid);
	        	
	        	$questionid=$request->question_id;
	        	$questionid=base64_decode($questionid);

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

	        		$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->get()->first();

		        	if($questiondetail)
		        	{
		        		$questiondetaildata=$questiondetail->toArray();

		        		$sort_order=$questiondetaildata['sort_order'];

				        $questiondetailnext=Question::where('topic_id',$quizid)->where('sort_order','>',$sort_order)->where('question_status',1)->orderBy('sort_order','ASC')->get()->first();
				        if($questiondetailnext)
				        {
				        	$questiondetailnextdata=$questiondetailnext->toArray();
				        	$next_question_key=base64_encode($questiondetailnextdata['id']);
				        }
				        else{
				        	$next_question_key="0";
				        }

				        $questiondetailprevious=Question::where('topic_id',$quizid)->where('sort_order','<',$sort_order)->where('question_status',1)->orderBy('sort_order','DESC')->get()->first();
				        if($questiondetailprevious)
				        {
				        	$questiondetailpreviousdata=$questiondetailprevious->toArray();
				        	$previous_question_key=base64_encode($questiondetailpreviousdata['id']);
				        }
				        else{
				        	$previous_question_key="0";
				        }

		        		if($questiondetaildata['question_img']!="")
				        {
				        	$question_img=url('/').'/images/questions/'.$questiondetaildata['question_img'];
				        }
				        else{
				        	$question_img='';
				        }

				    $result_date=date('Y-m-d');
				    $quizresultmarksdetail=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->get()->first();

			        $quizresultmarksdetaildata=$quizresultmarksdetail->toArray();

				    if($quizresultmarksdetaildata)
				      {
				      	$current_score=$quizresultmarksdetaildata['marks'];
				      }
				      else{
				      	$current_score=0;
				      }

				      $quizallquestions=Question::where('topic_id',$quizid)->where('question_status','1')->get();
    				if($quizallquestions)
    				{
    					$total_questions=count($quizallquestions);
    				}
    				else{
    					$total_questions=0;
    				}

    				$total_marks=$total_questions*$quiztopicdetaildata['per_q_mark'];

		        		 $questionslist=array(
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type']
			        				'quiz_id'=>base64_encode($quiztopicdetaildata['id']),
			        				'question_id'=>base64_encode($questiondetaildata['id']),
			        				'question'=>$questiondetaildata['question'], 
			        				'answer_exp'=>$questiondetaildata['answer_exp'],
			        				'question_video_link'=>$questiondetaildata['question_video_link'],
			        				'question_img'=>$question_img,
			        				'previous_question_key'=>$previous_question_key,
		        					'next_question_key'=>$next_question_key,
		        					'current_score'=>$current_score,
			        				'total_score'=>$total_marks
			        			);
		        		

		        		$success['questiondet'] =  $questiondet;
        				return $this::sendResponse($success, 'Questions Details.');
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


    public function getsubtopictheoryquizquestions(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'course_id'=>'required'
		            'topic_id'=>'required',
		            'sub_topic_id'=>'required'
		        ]);

	        	$courseid=$request->course_id;
	        	$courseid=base64_decode($courseid);

	        	$topicid=$request->topic_id;
	        	$topicid=base64_decode($topicid);

	        	$subtopicid=$request->sub_topic_id;
	        	$subtopicid=base64_decode($subtopicid);

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

		        		$quiztopicdetail=Quiztopic::where('subject',$courseid)->where('category',$topicid)->where('course_topic',$subtopicid)->where('quiz_type','2')->get()->first();

			        	if($quiztopicdetail)
			        	{
			        		$quiztopicdetaildata=$quiztopicdetail->toArray();
			        		$quizid=$quiztopicdetaildata['id'];

			        		$quizallquestions=Question::where('topic_id',$quizid)->where('question_status','1')->get();
	        				if($quizallquestions)
	        				{
	        					$total_questions=count($quizallquestions);
	        				}
	        				else{
	        					$total_questions=0;
	        				}

	        				$total_score=$total_questions*$quiztopicdetaildata['per_q_mark'];

			        		$questiondata=Question::where('topic_id',$quizid)->where('question_status','1')->get()->first();
			        		if($questiondata)
			        		{
			        			$questiondataarray=$questiondata->toArray();

			        	$sort_order=$questiondataarray['sort_order'];

				        $questiondetailnext=Question::where('topic_id',$quizid)->where('sort_order','>',$sort_order)->where('question_status',1)->orderBy('sort_order','ASC')->get()->first();
				        if($questiondetailnext)
				        {
				        	$questiondetailnextdata=$questiondetailnext->toArray();
				        	$next_question_key=base64_encode($questiondetailnextdata['id']);
				        }
				        else{
				        	$next_question_key="0";
				        }

				        $questiondetailprevious=Question::where('topic_id',$quizid)->where('sort_order','<',$sort_order)->where('question_status',1)->orderBy('sort_order','DESC')->get()->first();
				        if($questiondetailprevious)
				        {
				        	$questiondetailpreviousdata=$questiondetailprevious->toArray();
				        	$previous_question_key=base64_encode($questiondetailpreviousdata['id']);
				        }
				        else{
				        	$previous_question_key="0";
				        }

						        if($questiondataarray['question_img']!="")
						        {
						        	$question_img=url('/').'/images/questions/'.$questiondataarray['question_img'];
						        }
						        else{
						        	$question_img='';
						        }

			        			$questionslist=array(
			        				'course_name'=>$subject->title,
			        				'topic_name'=>$coursetopicsdetaildata['category_name'],
			        				'sub_topic_name'=>$coursesubtopicsdetaildata['topic_name'],
			        				'quiz_name'=>$quiztopicdetaildata['title'],
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type']
			        				'quiz_id'=>base64_encode($quiztopicdetaildata['id']),
			        				'question_id'=>base64_encode($questiondataarray['id']),
			        				'question'=>$questiondataarray['question'], 
			        				'answer_exp'=>$questiondataarray['answer_exp'],
			        				'question_video_link'=>$questiondataarray['question_video_link'],
			        				'question_img'=>$question_img,
			        				'previous_question_key'=>$previous_question_key,
		        					'next_question_key'=>$next_question_key

			        			);
			        		}
			        		else{
			        			$questionslist=[];
			        		}	

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

    public function gettheoryquizquestions(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'sub_topic_id'=>'required'
		        ]);

		        $subtopicid=$request->sub_topic_id;
	        	$subtopicid=base64_decode($subtopicid);

		        $quiztopicdetail=Quiztopic::where('course_topic',$subtopicid)->where('quiz_type','2')->get()->first();

	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();
	        		$quizid=$quiztopicdetaildata['id'];

	        		$quizallquestions=Question::where('topic_id',$quizid)->where('question_status','1')->get();
    				if($quizallquestions)
    				{
    					$total_questions=count($quizallquestions);
    				}
    				else{
    					$total_questions=0;
    				}

    				$total_score=$total_questions*$quiztopicdetaildata['per_q_mark'];


	        		$courseid=$quiztopicdetaildata['subject'];
		        	$topicid=$quiztopicdetaildata['category'];
		        	$subtopicid=$quiztopicdetaildata['course_topic'];

		        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $coursetopicsdetail=Subjectcategory::where('subject',$courseid)->where('id',$topicid)->get()->first();
		        if($coursetopicsdetail)
		        {
		        	$coursetopicsdetaildata=$coursetopicsdetail->toArray();
		        }
		        else{
		        	return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $coursesubtopicsdetail=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('id',$subtopicid)->get()->first();
		        	if($coursesubtopicsdetail)
		        	{
		        		$coursesubtopicsdetaildata=$coursesubtopicsdetail->toArray();
		        	}
		        	else{
		        		return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        	}

	        		$questiondata=Question::where('topic_id',$quizid)->where('question_status','1')->get()->first();
	        		if($questiondata)
	        		{

	        			$questiondataarray=$questiondata->toArray();

	        			$sort_order=$questiondataarray['sort_order'];

				        $questiondetailnext=Question::where('topic_id',$quizid)->where('sort_order','>',$sort_order)->where('question_status',1)->orderBy('sort_order','ASC')->get()->first();
				        if($questiondetailnext)
				        {
				        	$questiondetailnextdata=$questiondetailnext->toArray();
				        	$next_question_key=base64_encode($questiondetailnextdata['id']);
				        }
				        else{
				        	$next_question_key="0";
				        }

				        $questiondetailprevious=Question::where('topic_id',$quizid)->where('sort_order','<',$sort_order)->where('question_status',1)->orderBy('sort_order','DESC')->get()->first();
				        if($questiondetailprevious)
				        {
				        	$questiondetailpreviousdata=$questiondetailprevious->toArray();
				        	$previous_question_key=base64_encode($questiondetailpreviousdata['id']);
				        }
				        else{
				        	$previous_question_key="0";
				        }

	        			if($questiondataarray['question_img']!="")
				        {
				        	$question_img=url('/').'/images/questions/'.$questiondataarray['question_img'];
				        }
				        else{
				        	$question_img='';
				        }

				        $questionslist=array(
			        				'course_name'=>$subject->title,
			        				'topic_name'=>$coursetopicsdetaildata['category_name'],
			        				'sub_topic_name'=>$coursesubtopicsdetaildata['topic_name'],
			        				'quiz_name'=>$quiztopicdetaildata['title'],
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type']
			        				'quiz_id'=>base64_encode($quiztopicdetaildata['id']),
			        				'question_id'=>base64_encode($questiondataarray['id']),
			        				'question'=>$questiondataarray['question'], 
			        				'answer_exp'=>$questiondataarray['answer_exp'],
			        				'question_video_link'=>$questiondataarray['question_video_link'],
			        				'question_img'=>$question_img,
			        				'previous_question_key'=>$previous_question_key,
		        					'next_question_key'=>$next_question_key

			        			);

	        		}
	        		else{
	        			$questionslist=[];
	        		}

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