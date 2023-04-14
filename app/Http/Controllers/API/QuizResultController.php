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
		            'result_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $result_id=$request->result_id;
	        	$resultmarksdata=Resultmarks::where('user_id',$user->id)->where('id',$result_id)->get()->first();
	        	if($resultmarksdata)
	        	{
	        		$resultmarksdetail=$resultmarksdata->toArray();

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
		        				$quizcorrectresultdetail=Result::where('user_id',$user->id)->where('result_marks_id',$result_id)->where('answer','1')->get();

				        		if($quizcorrectresultdetail)
				        		{
				        			$correct_questions=$quizcorrectresultdetail->count();
				        		}
				        		else{
				        			$correct_questions=0;
				        		}

				        		$quizincorrectresultdetail=Result::where('user_id',$user->id)->where('result_marks_id',$result_id)->where('answer','2')->get();

				        		if($quizincorrectresultdetail)
				        		{
				        			$incorrect_questions=$quizincorrectresultdetail->count();
				        		}
				        		else{
				        			$incorrect_questions=0;
				        		}

				        		$quizskipresultdetail=Result::where('user_id',$user->id)->where('result_marks_id',$result_id)->where('answer','0')->get();

				        		if($quizskipresultdetail)
				        		{
				        			$skip_questions=$quizskipresultdetail->count();
				        		}
				        		else{
				        			$skip_questions=0;
				        		}

				        		$total_marks=$resultmarksdetail['total_marks'];
			        			$result_marks=$resultmarksdetail['marks'];

			        			if($total_marks==0)
			        			{
			        				$total_score=0;
			        			}
			        			else{
			        				$total_score=round(($result_marks/$total_marks)*100);
			        			}

			        			$result_marks_date=date('d M, Y',strtotime($resultmarksdetail['result_marks_date']));

			        			$resultarray=array(
			        			'sub_topic_title'=>'',
			        			'topic_title'=>'',
				        		'total_questions'=>$resultmarksdetail['total_questions'],
				        		'correct_questions'=>$correct_questions,
				        		'incorrect_questions'=>$incorrect_questions,
				        		'skip_questions'=>$skip_questions,
				        		'total_score'=>$total_score,
				        		'total_time'=>$resultmarksdetail['result_timer'],
				        		'result_date'=>$result_marks_date,
				        		'result_id'=>(int)$result_id,
				        		'result_type'=>(int)$resultmarksdetail['result_type'],
				        		'course_id'=>(int)$resultmarksdetail['subject']
				        		);
		        			}
		        			else{
		        				$resultarray=array(
				        			'total_questions'=>0,
				        			'correct_questions'=>0,
				        			'incorrect_questions'=>0,
				        			'skip_questions'=>0,
				        			'total_score'=>0,
				        			'total_time'=>0,
				        			'result_date'=>"",
				        			'result_id'=>0,
				        			'result_type'=>0,
				        			'course_id'=>0
				        		);
		        			}
		        		}
		        		else{
		        			$resultarray=array(
				        			'total_questions'=>0,
				        			'correct_questions'=>0,
				        			'incorrect_questions'=>0,
				        			'skip_questions'=>0,
				        			'total_score'=>0,
				        			'total_time'=>0,
				        			'result_date'=>"",
				        			'result_id'=>0,
				        			'result_type'=>0,
				        			'course_id'=>0
				        		);
		        		}
	        		}
	        		else{
	        			$resultarray=array(
				        			'total_questions'=>0,
				        			'correct_questions'=>0,
				        			'incorrect_questions'=>0,
				        			'skip_questions'=>0,
				        			'total_score'=>0,
				        			'total_time'=>0,
				        			'result_date'=>"",
				        			'result_id'=>0,
				        			'result_type'=>0,
				        			'course_id'=>0
				        		);
	        		}

	        		$success['resultarray'] =  $resultarray;
            		return $this::sendResponse($success, 'View User Result.');

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
		            'result_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $result_id=$request->result_id;
		        $resultmarksdata=Resultmarks::where('user_id',$user->id)->where('id',$result_id)->get()->first();
		        if($resultmarksdata)
		        {
		        	$resultmarksdetail=$resultmarksdata->toArray();

	        		$random_question_idsdb=$resultmarksdetail['random_question_ids'];

	        		$quiz_topiciddb=$resultmarksdetail['topic_id'];
	        		$searchForValue = ',';
					if( strpos($quiz_topiciddb, $searchForValue) !== false ) {
					     $quiz_topiciddbarray=explode(',',$quiz_topiciddb);
					}
					else{
						$quiz_topiciddbarray=array($quiz_topiciddb);
					}

					$sub_topic_titlearr=[];
					foreach($quiz_topiciddbarray as $list)
					{
						$quizdet=Quiztopic::where('id',$list)->get()->first();
						if($quizdet)
						{
							$quizdetarr=$quizdet->toArray();
							$course_topicid=$quizdetarr['course_topic'];

							$coursetopicdet=Coursetopic::where('id',$course_topicid)->get()->first();
							if($coursetopicdet)
							{
								$coursetopicarr=$coursetopicdet->toArray();
								$sub_topic_titlearr[]=$coursetopicarr['topic_name'];
							}
						}
					}

					if(count($sub_topic_titlearr) > 0)
					{
						$sub_topic_title=implode(',',$sub_topic_titlearr);
					}
					else{
						$sub_topic_title="";
					}

	        		
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
		        				$quiz_result=[];
		        				foreach($attempt_question_ids as $row)
		        				{

		        				$questiondet=Question::where('id',$row)->get()->first();
			        			if($questiondet)
		        				{
		        					$questiondetarray=$questiondet->toArray();
		        					$question_id=$questiondetarray['id'];
		        					$question=$questiondetarray['question_latex'];
		        					$correct_answer=$questiondetarray['answer'];
		        					$answer_explaination=$questiondetarray['answer_exp_latex'];
		        				}
		        				else{
		        					$question_id="";
		        					$question="";
		        					$correct_answer="";
		        					$answer_explaination="";

		        				}

		        				$quizresultdata=Result::where('user_id',$user->id)->where('result_marks_id',$result_id)->where('question_id',$question_id)->get()->first();
		        				if($quizresultdata)
		        				{
		        					$quizresultdataarray=$quizresultdata->toArray();

		        					$user_answer=$quizresultdataarray['user_answer'];
		        					$answer_status=$quizresultdataarray['answer'];
		        				}
		        				else{
		        					$user_answer="";
		        					$answer_status=0;
		        				}

		        			if($question!="")
		        			{
		        				$quizquestion='\('.$question.'\)';
		        			}
		        			else{
		        				$quizquestion="";
		        			}

		        			if($answer_explaination!="")
					        {
					        	$quiz_answer_exp='\('.$answer_explaination.'\)';
					        }
					        else{
					        	$quiz_answer_exp="";
					        }

	        				$quiz_result[]=array(
	        					'result_type'=>(int)$resultmarksdetail['result_type'],
	        					'course_id'=>"",
	        					'quiz_id'=>(int)$quiz_topiciddb,
	        					'question_id'=>(int)$question_id,
	        					'question'=>$quizquestion,
	        					'correct_answer'=>$correct_answer,
	        					'answer_explaination'=>$quiz_answer_exp,
	        					'user_answer'=>$user_answer,
	        					'answer_status'=>$answer_status,
	        					'result_id'=>(int)$result_id,
	        					'course_id'=>(int)$resultmarksdetail['subject']
	        				);

		        				}

	        				$success['quiz_result'] =  $quiz_result;
        					$success['sub_topic_title']=$sub_topic_title;

        					$success['result_id']=(int)$result_id;
            				return $this::sendResponse($success, 'Quiz Result Questions summary.');

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
	        		$random_question_idsdb=$list['random_question_ids'];

	        		$quiz_topiciddb=$list['topic_id'];
	        		$searchForValue = ',';
					if( strpos($quiz_topiciddb, $searchForValue) !== false ) {
					     $quiz_topiciddbarray=explode(',',$quiz_topiciddb);
					}
					else{
						$quiz_topiciddbarray=array($quiz_topiciddb);
					}

					$sub_topic_titlearr=[];
					$topic_tile_arr=[];
					foreach($quiz_topiciddbarray as $arrid)
					{
						$quizdet=Quiztopic::where('id',$arrid)->get()->first();
						if($quizdet)
						{
							$quizdetarr=$quizdet->toArray();
							$course_topicid=$quizdetarr['course_topic'];
							$category=$quizdetarr['category'];

							$coursetopicdet=Coursetopic::where('id',$course_topicid)->get()->first();
							if($coursetopicdet)
							{
								$coursetopicarr=$coursetopicdet->toArray();
								$sub_topic_titlearr[]=$coursetopicarr['topic_name'];
							}

							$subjectcategorydet=Subjectcategory::where('id',$category)->get()->first();
							if($subjectcategorydet)
							{
								$subjectcategoryarr=$subjectcategorydet->toArray();
								$topic_tile_arr[]=$subjectcategoryarr['category_name'];
							}
						}
					}

					if(count($sub_topic_titlearr) > 0)
					{
						$sub_topic_title=implode(',',$sub_topic_titlearr);
					}
					else{
						$sub_topic_title="";
					}

					if(count($topic_tile_arr) > 0)
					{
						$topic_title=implode(',',$topic_tile_arr);
					}
					else{
						$topic_title="";
					}

	        		$random_question_idsarray=json_decode($random_question_idsdb,true);

	        		$random_question_ids=[];
	        		foreach($random_question_idsarray as $listval)
	        		{
	        			foreach($listval as $rowval)
	        			{
	        				$random_question_ids[]=$rowval;
	        			}
	        		}

	        		$attempt_question_idsdb=$list['question_ids'];
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
		        				$quizcorrectresultdetail=Result::where('user_id',$user->id)->where('result_marks_id',$list['id'])->where('answer','1')->get();

				        		if($quizcorrectresultdetail)
				        		{
				        			$correct_questions=$quizcorrectresultdetail->count();
				        		}
				        		else{
				        			$correct_questions=0;
				        		}

				        		$quizincorrectresultdetail=Result::where('user_id',$user->id)->where('result_marks_id',$list['id'])->where('answer','2')->get();

				        		if($quizincorrectresultdetail)
				        		{
				        			$incorrect_questions=$quizincorrectresultdetail->count();
				        		}
				        		else{
				        			$incorrect_questions=0;
				        		}

				        		$quizskipresultdetail=Result::where('user_id',$user->id)->where('result_marks_id',$list['id'])->where('answer','0')->get();

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
			        				$total_score=round(($result_marks/$total_marks)*100);
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
			        			'result_id'=>(int)$list['id']
			        		);

		        			}
		        		}
	        		}
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }

    }

    public function viewuserresult(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$validator = Validator::make($request->all(), [
		            'result_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $result_id=$request->result_id;
		        $resultmarksdata=Resultmarks::where('user_id',$user->id)->where('id',$result_id)->get()->first();
		        if($resultmarksdata)
		        {
		        	$resultmarksdetail=$resultmarksdata->toArray();
		        	$random_question_idsdb=$resultmarksdetail['random_question_ids'];

		        	$quiz_topiciddb=$resultmarksdetail['topic_id'];
	        		$searchForValue = ',';
					if( strpos($quiz_topiciddb, $searchForValue) !== false ) {
					     $quiz_topiciddbarray=explode(',',$quiz_topiciddb);
					}
					else{
						$quiz_topiciddbarray=array($quiz_topiciddb);
					}

					$sub_topic_titlearr=[];
					$topic_tile_arr=[];
					foreach($quiz_topiciddbarray as $arrid)
					{
						$quizdet=Quiztopic::where('id',$arrid)->get()->first();
						if($quizdet)
						{
							$quizdetarr=$quizdet->toArray();
							$course_topicid=$quizdetarr['course_topic'];
							$category=$quizdetarr['category'];

							$coursetopicdet=Coursetopic::where('id',$course_topicid)->get()->first();
							if($coursetopicdet)
							{
								$coursetopicarr=$coursetopicdet->toArray();
								$sub_topic_titlearr[]=$coursetopicarr['topic_name'];
							}

							$subjectcategorydet=Subjectcategory::where('id',$category)->get()->first();
							if($subjectcategorydet)
							{
								$subjectcategoryarr=$subjectcategorydet->toArray();
								$topic_tile_arr[]=$subjectcategoryarr['category_name'];
							}
						}
					}

					if(count($sub_topic_titlearr) > 0)
					{
						$sub_topic_title=implode(',',$sub_topic_titlearr);
					}
					else{
						$sub_topic_title="";
					}

					if(count($topic_tile_arr) > 0)
					{
						$topic_title=implode(',',$topic_tile_arr);
					}
					else{
						$topic_title="";
					}

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
		        				$quizcorrectresultdetail=Result::where('user_id',$user->id)->where('result_marks_id',$result_id)->where('answer','1')->get();

				        		if($quizcorrectresultdetail)
				        		{
				        			$correct_questions=$quizcorrectresultdetail->count();
				        		}
				        		else{
				        			$correct_questions=0;
				        		}

				        		$quizincorrectresultdetail=Result::where('user_id',$user->id)->where('result_marks_id',$result_id)->where('answer','2')->get();

				        		if($quizincorrectresultdetail)
				        		{
				        			$incorrect_questions=$quizincorrectresultdetail->count();
				        		}
				        		else{
				        			$incorrect_questions=0;
				        		}

				        		$quizskipresultdetail=Result::where('user_id',$user->id)->where('result_marks_id',$result_id)->where('answer','0')->get();

				        		if($quizskipresultdetail)
				        		{
				        			$skip_questions=$quizskipresultdetail->count();
				        		}
				        		else{
				        			$skip_questions=0;
				        		}

				        		$total_marks=$resultmarksdetail['total_marks'];
			        			$result_marks=$resultmarksdetail['marks'];

			        			if($total_marks==0)
			        			{
			        				$total_score=0;
			        			}
			        			else{
			        				$total_score=round(($result_marks/$total_marks)*100);
			        			}

			        			$result_marks_date=date('d M, Y',strtotime($resultmarksdetail['result_marks_date']));

			        	$resultdet=array(
	        				'sub_topic_title'=>$sub_topic_title,
	        				'topic_title'=>$topic_title,
		        			'total_questions'=>$resultmarksdetail['total_questions'],
		        			'correct_questions'=>$correct_questions,
		        			'incorrect_questions'=>$incorrect_questions,
		        			'skip_questions'=>$skip_questions,
		        			'total_score'=>$total_score,
		        			'total_time'=>$resultmarksdetail['result_timer'],
		        			'result_date'=>$result_marks_date,
		        			'result_id'=>(int)$result_id
		        		);

		        			}
		        			else{
		        				$resultdet=array(
				        			'total_questions'=>0,
				        			'correct_questions'=>0,
				        			'incorrect_questions'=>0,
				        			'skip_questions'=>0,
				        			'total_score'=>0,
				        			'total_time'=>0,
				        			'result_date'=>"",
				        			'result_id'=>''
				        		);
		        			}
		        		}
		        		else{
		        			$resultdet=array(
				        			'total_questions'=>0,
				        			'correct_questions'=>0,
				        			'incorrect_questions'=>0,
				        			'skip_questions'=>0,
				        			'total_score'=>0,
				        			'total_time'=>0,
				        			'result_date'=>"",
				        			'result_id'=>''
				        		);
		        		}
	        		}
	        		else{
	        			$resultdet=array(
				        			'total_questions'=>0,
				        			'correct_questions'=>0,
				        			'incorrect_questions'=>0,
				        			'skip_questions'=>0,
				        			'total_score'=>0,
				        			'total_time'=>0,
				        			'result_date'=>"",
				        			'result_id'=>''
				        		);
	        		}

		        }
		        else{
		        	return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
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