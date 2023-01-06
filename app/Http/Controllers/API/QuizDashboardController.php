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
	        				if($quiztopicdetaildata['questions_limit']!="")
	        				{
	        					$total_questions_limit=$quiztopicdetaildata['questions_limit'];
	        				}
	        				else{
	        					$total_questions_limit=0;
	        				}

	        			$result_date=date('Y-m-d');
                		$quizresultmarks=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->get()->first();
                		if($quizresultmarks==null)
                		{
                			$random_questionsdata = Question::where('topic_id',$quizid)->where('question_status','1')
	        						->inRandomOrder()
                						->limit($total_questions_limit)
                							->get();

                			if($random_questionsdata)
                			{
                				$random_questionsdataarray=$random_questionsdata->toArray();
                				$random_question_ids=[];
                				foreach($random_questionsdataarray as $list)
                				{
                					$random_question_ids[]=$list['id'];
                				}
                			}
                			else{
                				$random_question_ids=[];
                			}

                			if(count($random_question_ids) == 0)
            				{
            					return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
            				}

            				try{

            				$total_score=count($random_question_ids)*$quiztopicdetaildata['per_q_mark'];

			                    $resultmarks = new Resultmarks;
			                    $resultmarks->topic_id=$quiztopicdetaildata['id'];
			                    $resultmarks->user_id=$user->id;
			                    $resultmarks->marks=0;
			                    $resultmarks->result_timer=$quiztopicdetaildata['timer'];
			                    $resultmarks->total_questions=count($random_question_ids);
			                    $resultmarks->total_marks=$total_score;
			                    $resultmarks->result_marks_date=$result_date;
			                    $resultmarks->random_question_ids=implode(',',$random_question_ids);
			                    $resultmarks->save();
		                     
			                  }catch(\Exception $e){
			                    return $this::sendError('Unauthorised Exception.', ['error'=>$e->getMessage()]);     
			                 }

                		}
                		else{
                			$quizresultmarksarray=$quizresultmarks->toArray();

                			$resultid=$quizresultmarksarray['id'];
                			$newresultmarksdata = Resultmarks::find($resultid);
		        			$newresultmarksdata->question_ids="";
		        			$newresultmarksdata->marks=0;
		        			$newresultmarksdata->save();


            				$random_question_ids_db=$quizresultmarksarray['random_question_ids'];

            				$random_question_ids=explode(',',$random_question_ids_db);

            				$total_score=count($random_question_ids)*$quiztopicdetaildata['per_q_mark'];
                		}

                		$questiondata=Question::where('topic_id',$quizid)->where('id',$random_question_ids[0])->where('question_status','1')->get()->first();

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
			        				'question'=>strip_tags($questiondataarray['question']), 
			        				'a'=>strip_tags($questiondataarray['a']), 
			        				'b'=>strip_tags($questiondataarray['b']),
			        				'c'=>strip_tags($questiondataarray['c']),
			        				'd'=>strip_tags($questiondataarray['d']),
			        				'answer'=>strip_tags($questiondataarray['answer']),
			        				'answer_exp'=>strip_tags($questiondataarray['answer_exp']),
			        				'question_video_link'=>$questiondataarray['question_video_link'],
			        				'question_img'=>$question_img,
			        				'current_score'=>0,
			        				'total_score'=>$total_score,
			        				'total_questions'=>count($random_question_ids),
			        				'current_quiz'=>1
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

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','1')->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

	        		$randomquizresultmarks=Resultmarks::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_marks_date',$result_date)->get()->first();
	        		if($randomquizresultmarks)
	        		{
	        		$randomquizresultmarksarray=$randomquizresultmarks->toArray();

            		$random_question_ids_db=$randomquizresultmarksarray['random_question_ids'];
            		$random_question_ids=explode(',',$random_question_ids_db);

            		if(in_array($questionid, $random_question_ids))
            		{
    				$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->get()->first();
	        		if($questiondetail)
	        		{
	        			$questiondetaildata=$questiondetail->toArray();

	        			$currentquestionindex = array_keys($random_question_ids,$questionid);

	        			if(count($currentquestionindex) > 0)
	        			{
	        				$currentquestionindex=$currentquestionindex[0];

	        				$next_question_index=$currentquestionindex+1;
	        				$previous_question_index=$currentquestionindex-1;

	        				if(isset($random_question_ids[$previous_question_index]))
	        				{
	        					$previous_question_key=$random_question_ids[$previous_question_index];
	        				}
	        				else{
	        					$previous_question_key=0;
	        				}

	        				if(isset($random_question_ids[$next_question_index]))
	        				{
	        					$next_question_key=$random_question_ids[$next_question_index];
	        				}
	        				else{
	        					$next_question_key=0;
	        				}

	        			}
	        			else{
	        				$previous_question_key=0;
	        				$next_question_key=0;
	        			}

	        			$total_questions=count($random_question_ids);

	        			$total_marks=$total_questions*$quiztopicdetaildata['per_q_mark'];

	        			$current_score=0;
	        			if($questiondetaildata['answer']==$user_anwer)
	        			{
	        				$quizresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_date',$result_date)->get()->first();
	        				if($quizresultdetail)
	        				{
	        					$quizresultdetaildata=$quizresultdetail->toArray();
	        					$resultid=$quizresultdetaildata['id'];


	        			$quizresultmarks=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->whereRaw('FIND_IN_SET(?, question_ids)', [$questiondetaildata['id']])->get()->first();

						   if($quizresultmarks)
						   {
						   		$quizresultmarksdata=$quizresultmarks->toArray();
						   		$dbquestion_ids=$quizresultmarksdata['question_ids'];
						   		$dbmarks=$quizresultmarksdata['marks'];
						   		$question_marks=$quizresultdetaildata['marks'];
						   		$newresultmarksid=$quizresultmarksdata['id'];
						   		$dbquestionarray=explode(',',$dbquestion_ids);

						   		if (($key = array_search($questiondetaildata['id'], $dbquestionarray)) !== false) {
								    unset($dbquestionarray[$key]);

								    if(count($dbquestionarray) > 0)
								    {
								    	$newdbquestionslist=implode(',',$dbquestionarray);
								    }
								    else{
								    	$newdbquestionslist="";
								    }

								    $newdbmarks=$dbmarks-$question_marks;

								    $newresultmarksdata = Resultmarks::find($newresultmarksid);
		        					$newresultmarksdata->question_ids=$newdbquestionslist;
		        					$newresultmarksdata->marks=$newdbmarks;
		        					$newresultmarksdata->save();

								}
						   }


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
			        			'answer_exp'=>strip_tags($questiondetaildata['answer_exp']),
			        			'answer_status'=>1, //correct
			        			'previous_question_key'=>(int)$previous_question_key,
			        			'next_question_key'=>(int)$next_question_key
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
		        			'answer_exp'=>strip_tags($questiondetaildata['answer_exp']),
		        			'answer_status'=>1, //correct
		        			'previous_question_key'=>(int)$previous_question_key,
		        			'next_question_key'=>(int)$next_question_key
			        		);

		        			}

		        	$quizresultmarksdetail=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->get()->first();

				          if($quizresultmarksdetail)
					         {
					         	$quizresultmarksdetaildata=$quizresultmarksdetail->toArray();

					         	$dbquestion_ids=$quizresultmarksdetaildata['question_ids'];
					         	if($dbquestion_ids!="")
					         	{
					         		$newdbquestion_ids=$dbquestion_ids.','.$questiondetaildata['id'];
					         	}
					         	else{
					         		$newdbquestion_ids=$questiondetaildata['id'];
					         	}    	

					         	$current_score=$quizresultmarksdetaildata['marks'];

					         	$resultmarksid=$quizresultmarksdetaildata['id'];
						        $previousresultmarks=$quizresultmarksdetaildata['marks'];
						        $newresultmarks=$previousresultmarks+$quiztopicdetaildata['per_q_mark'];

						        $resultmarksupdate = Resultmarks::find($resultmarksid);

		        				$resultmarksupdate->marks=$newresultmarks;
				                $resultmarksupdate->result_timer=$quiztopicdetaildata['timer'];
				                $resultmarksupdate->question_ids=$newdbquestion_ids;

				               		try{
						            $resultmarksupdate->save();
						         }catch(\Exception $e){

						            return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
						         }

					         }
					         else{
					         	$current_score=$quiztopicdetaildata['per_q_mark'];

					         	try{
				                    $resultmarks = new Resultmarks;
				                    $resultmarks->topic_id=$quiztopicdetaildata['id'];
				                    $resultmarks->user_id=$user->id;
				                    $resultmarks->marks=$quiztopicdetaildata['per_q_mark'];
				                    $resultmarks->result_timer=$quiztopicdetaildata['timer'];
				                    $resultmarks->total_questions=$total_questions;
				                    $resultmarks->total_marks=$total_marks;
				                    $resultmarks->result_marks_date=$result_date;
				                    $resultmarks->question_ids=$questiondetaildata['id'];
				                    $resultmarks->save();
				                     
				                  }catch(\Exception $e){
				                    return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);     
				                 }
					         }
	        			}
	        			else{
	        				$quizresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_date',$result_date)->get()->first();
	        				if($quizresultdetail)
	        				{
	        					$quizresultdetaildata=$quizresultdetail->toArray();
	        					$resultid=$quizresultdetaildata['id'];

	        		$quizresultmarks=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->whereRaw('FIND_IN_SET(?, question_ids)', [$questiondetaildata['id']])->get()->first();

						   if($quizresultmarks)
						   {
						   		$quizresultmarksdata=$quizresultmarks->toArray();
						   		$dbquestion_ids=$quizresultmarksdata['question_ids'];
						   		$dbmarks=$quizresultmarksdata['marks'];
						   		$question_marks=$quizresultdetaildata['marks'];
						   		$newresultmarksid=$quizresultmarksdata['id'];
						   		$dbquestionarray=explode(',',$dbquestion_ids);

						   		if (($key = array_search($questiondetaildata['id'], $dbquestionarray)) !== false) {
								    unset($dbquestionarray[$key]);

								    if(count($dbquestionarray) > 0)
								    {
								    	$newdbquestionslist=implode(',',$dbquestionarray);
								    }
								    else{
								    	$newdbquestionslist="";
								    }

								    $newdbmarks=$dbmarks-$question_marks;

								    $newresultmarksdata = Resultmarks::find($newresultmarksid);
		        					$newresultmarksdata->question_ids=$newdbquestionslist;
		        					$newresultmarksdata->marks=$newdbmarks;
		        					$newresultmarksdata->save();

								}
						   }

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
		        			'answer_exp'=>strip_tags($questiondetaildata['answer_exp']),
		        			'answer_status'=>2, //incorrect
		        			'previous_question_key'=>(int)$previous_question_key,
		        			'next_question_key'=>(int)$next_question_key
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
		        			'answer_exp'=>strip_tags($questiondetaildata['answer_exp']),
		        			'answer_status'=>2, //incorrect
		        			'previous_question_key'=>(int)$previous_question_key,
		        			'next_question_key'=>(int)$next_question_key
			        		);
	        				}

	        				$quizresultmarksdetail=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->get()->first();

				          if($quizresultmarksdetail)
					         {
					         	$quizresultmarksdetaildata=$quizresultmarksdetail->toArray();

					         	$dbquestion_ids=$quizresultmarksdetaildata['question_ids'];

					         	if($dbquestion_ids!="")
					         	{
					         		$newdbquestion_ids=$dbquestion_ids.','.$questiondetaildata['id'];
					         	}
					         	else{
					         		$newdbquestion_ids=$questiondetaildata['id'];
					         	}

					         	$current_score=$quizresultmarksdetaildata['marks'];

					         	$resultmarksid=$quizresultmarksdetaildata['id'];
						        $previousresultmarks=$quizresultmarksdetaildata['marks'];
						        $newresultmarks=$previousresultmarks+0;

						        $resultmarksupdate = Resultmarks::find($resultmarksid);

		        				$resultmarksupdate->marks=$newresultmarks;
				                $resultmarksupdate->result_timer=$quiztopicdetaildata['timer'];
				                $resultmarksupdate->question_ids=$newdbquestion_ids;

				               		try{
						            $resultmarksupdate->save();
						         }catch(\Exception $e){

						            return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
						         }

					         }
					         else{
					         	$current_score=$quiztopicdetaildata['per_q_mark'];

					         	try{
				                    $resultmarks = new Resultmarks;
				                    $resultmarks->topic_id=$quiztopicdetaildata['id'];
				                    $resultmarks->user_id=$user->id;
				                    $resultmarks->marks=0;
				                    $resultmarks->result_timer=$quiztopicdetaildata['timer'];
				                    $resultmarks->total_questions=$total_questions;
				                    $resultmarks->total_marks=$total_marks;
				                    $resultmarks->result_marks_date=$result_date;
				                    $resultmarks->question_ids=$questiondetaildata['id'];
				                    $resultmarks->save();
				                     
				                  }catch(\Exception $e){
				                    return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);     
				                 }
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

	public function skipobjectivequizquestion(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','1')->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();
	        		$randomquizresultmarks=Resultmarks::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_marks_date',$result_date)->get()->first();
	        		if($randomquizresultmarks)
	        		{
	        		$randomquizresultmarksarray=$randomquizresultmarks->toArray();

            		$random_question_ids_db=$randomquizresultmarksarray['random_question_ids'];
            		$random_question_ids=explode(',',$random_question_ids_db);

            		if(in_array($questionid, $random_question_ids))
            		{
            			$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->get()->first();
            			if($questiondetail)
            			{
            				$questiondetaildata=$questiondetail->toArray();

            				$currentquestionindex = array_keys($random_question_ids,$questionid);

	        			if(count($currentquestionindex) > 0)
	        			{
	        				$currentquestionindex=$currentquestionindex[0];

	        				$next_question_index=$currentquestionindex+1;
	        				$previous_question_index=$currentquestionindex-1;

	        				if(isset($random_question_ids[$previous_question_index]))
	        				{
	        					$previous_question_key=$random_question_ids[$previous_question_index];
	        				}
	        				else{
	        					$previous_question_key=0;
	        				}

	        				if(isset($random_question_ids[$next_question_index]))
	        				{
	        					$next_question_key=$random_question_ids[$next_question_index];
	        				}
	        				else{
	        					$next_question_key=0;
	        				}

	        			}
	        			else{
	        				$previous_question_key=0;
	        				$next_question_key=0;
	        			}

	        			$total_questions=count($random_question_ids);

	        			$total_marks=$total_questions*$quiztopicdetaildata['per_q_mark'];
	        			$current_score=0;

	        			$quizresultdetail=Result::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_date',$result_date)->get()->first();
	        			if($quizresultdetail)
	        			{
	        				$quizresultdetaildata=$quizresultdetail->toArray();
		        			$resultid=$quizresultdetaildata['id'];

		        			$quizresultmarks=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->whereRaw('FIND_IN_SET(?, question_ids)', [$questiondetaildata['id']])->get()->first();

						   if($quizresultmarks)
						   {
						   		$quizresultmarksdata=$quizresultmarks->toArray();
						   		$dbquestion_ids=$quizresultmarksdata['question_ids'];
						   		$dbmarks=$quizresultmarksdata['marks'];

						   		$question_marks=0;
						   		$newresultmarksid=$quizresultmarksdata['id'];
						   		$dbquestionarray=explode(',',$dbquestion_ids);

						   		if (($key = array_search($questiondetaildata['id'], $dbquestionarray)) !== false) {
								    unset($dbquestionarray[$key]);

								    if(count($dbquestionarray) > 0)
								    {
								    	$newdbquestionslist=implode(',',$dbquestionarray);
								    }
								    else{
								    	$newdbquestionslist="";
								    }

								    $newdbmarks=$dbmarks-$question_marks;

								    $newresultmarksdata = Resultmarks::find($newresultmarksid);
		        					$newresultmarksdata->question_ids=$newdbquestionslist;
		        					$newresultmarksdata->marks=$newdbmarks;
		        					$newresultmarksdata->save();

								}
						   }

						   $resultuserupdate = Result::find($resultid);
				                $resultuserupdate->user_answer=$user_anwer;
				                $resultuserupdate->answer=0;
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
		        			'answer_exp'=>strip_tags($questiondetaildata['answer_exp']),
		        			'answer_status'=>0, //skip
		        			'previous_question_key'=>(int)$previous_question_key,
		        			'next_question_key'=>(int)$next_question_key
			        		);

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

		                 $questiondet=array(
		        			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
		        			'quiz_id'=>$quiztopicdetaildata['id'],
		        			'question_id'=>$questiondetaildata['id'],
		        			'answer_exp'=>strip_tags($questiondetaildata['answer_exp']),
		        			'answer_status'=>0, //skip
		        			'previous_question_key'=>(int)$previous_question_key,
		        			'next_question_key'=>(int)$next_question_key
			        		);

	        			}

	        			$quizresultmarksdetail=Resultmarks::where('user_id',$user->id)->where('topic_id',$quiztopicdetaildata['id'])->where('result_marks_date',$result_date)->get()->first();

				          if($quizresultmarksdetail)
					         {
					         	$quizresultmarksdetaildata=$quizresultmarksdetail->toArray();

					         	$dbquestion_ids=$quizresultmarksdetaildata['question_ids'];

					         	if($dbquestion_ids!="")
					         	{
					         		$newdbquestion_ids=$dbquestion_ids.','.$questiondetaildata['id'];
					         	}
					         	else{
					         		$newdbquestion_ids=$questiondetaildata['id'];
					         	}

					         	$current_score=$quizresultmarksdetaildata['marks'];

					         	$resultmarksid=$quizresultmarksdetaildata['id'];
						        $previousresultmarks=$quizresultmarksdetaildata['marks'];
						        $newresultmarks=$previousresultmarks+0;

						        $resultmarksupdate = Resultmarks::find($resultmarksid);

		        				$resultmarksupdate->marks=$newresultmarks;
				                $resultmarksupdate->result_timer=$quiztopicdetaildata['timer'];
				                $resultmarksupdate->question_ids=$newdbquestion_ids;

				               		try{
						            $resultmarksupdate->save();
						         }catch(\Exception $e){

						            return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
						         }

					         }
					         else{
					         	$current_score=$quiztopicdetaildata['per_q_mark'];

					         	try{
				                    $resultmarks = new Resultmarks;
				                    $resultmarks->topic_id=$quiztopicdetaildata['id'];
				                    $resultmarks->user_id=$user->id;
				                    $resultmarks->marks=0;
				                    $resultmarks->result_timer=$quiztopicdetaildata['timer'];
				                    $resultmarks->total_questions=$total_questions;
				                    $resultmarks->total_marks=$total_marks;
				                    $resultmarks->result_marks_date=$result_date;
				                    $resultmarks->question_ids=$questiondetaildata['id'];
				                    $resultmarks->save();
				                     
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
	}


	public function getobjectivequizquestionexplaination(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','1')->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

	        		$course_id=$quiztopicdetaildata['subject'];
	        		$topic_id=$quiztopicdetaildata['category'];
	        		$sub_topic_id=$quiztopicdetaildata['course_topic'];

	        		$result_date=date('Y-m-d');

	        		$randomquizresultmarks=Resultmarks::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_marks_date',$result_date)->get()->first();
	        		if($randomquizresultmarks)
	        		{
	        			$randomquizresultmarksarray=$randomquizresultmarks->toArray();

	        			$random_question_ids_db=$randomquizresultmarksarray['random_question_ids'];
            			$random_question_ids=explode(',',$random_question_ids_db);

            			if(in_array($questionid, $random_question_ids))
            			{
            				$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->get()->first();
            				if($questiondetail)
            				{
            					$questiondetaildata=$questiondetail->toArray();

	        					$currentquestionindex = array_keys($random_question_ids,$questionid);
	        					
	        					if(count($currentquestionindex) > 0)
	        					{
	        			$currentquestionindex=$currentquestionindex[0];
	        			$current_quiz=$currentquestionindex+1;
	        			$next_question_index=$currentquestionindex+1;
	        			$previous_question_index=$currentquestionindex-1;

	        				if(isset($random_question_ids[$previous_question_index]))
	        				{
	        					$previous_question_key=$random_question_ids[$previous_question_index];
	        				}
	        				else{
	        					$previous_question_key=0;
	        				}

	        				if(isset($random_question_ids[$next_question_index]))
	        				{
	        					$next_question_key=$random_question_ids[$next_question_index];
	        				}
	        				else{
	        					$next_question_key=0;
	        				}

	        					}	
	        					else{
	        						$current_quiz=0;
	        						$previous_question_key=0;
	        						$next_question_key=0;
	        					}

	        			if($questiondetaildata['question_img']!="")
				        {
				        	$question_img=url('/').'/images/questions/'.$questiondetaildata['question_img'];
				        }
				        else{
				        	$question_img='';
				        }


				    $result_date=date('Y-m-d');
				    $quizresultmarksdetail=Resultmarks::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_marks_date',$result_date)->get()->first();
				    if($quizresultmarksdetail)
				    {
				    	$quizresultmarksdetaildata=$quizresultmarksdetail->toArray();
					    if($quizresultmarksdetaildata)
					      {
					      	$current_score=$quizresultmarksdetaildata['marks'];
					      }
					      else{
					      	$current_score=0;
					      }
				    }
				    else{
				    	$current_score=0;
				    }

				    	$total_questions=count($random_question_ids);

	        			$total_marks=$total_questions*$quiztopicdetaildata['per_q_mark'];


	        			$questiondet=array(
		        		 			'course_id'=>$course_id,
		        		 			'topic_id'=>$topic_id,
		        		 			'sub_topic_id'=>$sub_topic_id,
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondetaildata['id'],
			        				'question'=>strip_tags($questiondetaildata['question']), 
			        				'answer_exp'=>strip_tags($questiondetaildata['answer_exp']),
			        				'question_video_link'=>$questiondetaildata['question_video_link'],
			        				'question_img'=>$question_img,
			        				'previous_question_key'=>(int)$previous_question_key,
		        					'next_question_key'=>(int)$next_question_key,
		        					'current_score'=>$current_score,
			        				'total_score'=>$total_marks,
			        				'total_questions'=>$total_questions,
			        				'current_quiz'=>$current_quiz
			        			);
	        					
            				}
            				else{
            					$questiondet=null;
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
	        		return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
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

	public function getobjectivequizquestiondetails(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;
	        	$result_date=date('Y-m-d');

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','1')->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

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

		        $randomquizresultmarks=Resultmarks::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_marks_date',$result_date)->get()->first();
		        if($randomquizresultmarks)
		        {
		        	$randomquizresultmarksarray=$randomquizresultmarks->toArray();

            		$random_question_ids_db=$randomquizresultmarksarray['random_question_ids'];
            		$random_question_ids=explode(',',$random_question_ids_db);

            		if(in_array($questionid, $random_question_ids))
            		{
            			$questiondata=Question::where('topic_id',$quizid)->where('id',$questionid)->get()->first();
            			if($questiondata)
            			{
            				$questiondataarray=$questiondata->toArray();

	        				$currentquestionindex = array_keys($random_question_ids,$questionid);

	        			if(count($currentquestionindex) > 0)
	        			{
	        			$currentquestionindex=$currentquestionindex[0];
	        			$current_quiz=$currentquestionindex+1;
	        			$next_question_index=$currentquestionindex+1;
	        			$previous_question_index=$currentquestionindex-1;

	        				if(isset($random_question_ids[$previous_question_index]))
	        				{
	        					$previous_question_key=$random_question_ids[$previous_question_index];
	        				}
	        				else{
	        					$previous_question_key=0;
	        				}

	        				if(isset($random_question_ids[$next_question_index]))
	        				{
	        					$next_question_key=$random_question_ids[$next_question_index];
	        				}
	        				else{
	        					$next_question_key=0;
	        				}

	        			}
	        			else{
	        				$current_quiz=0;
	        				$previous_question_key=0;
	        				$next_question_key=0;
	        			}

	        	$quizresultmarksdetail=Resultmarks::where('user_id',$user->id)->where('topic_id',$quizid)->where('result_marks_date',$result_date)->get()->first();
				    if($quizresultmarksdetail)
				    {
				    	$quizresultmarksdetaildata=$quizresultmarksdetail->toArray();
					    if($quizresultmarksdetaildata)
					      {
					      	$current_score=$quizresultmarksdetaildata['marks'];
					      }
					      else{
					      	$current_score=0;
					      }
				    }
				    else{
				    	$current_score=0;
				    }

				    	$total_questions=count($random_question_ids);

	        			$total_marks=$total_questions*$quiztopicdetaildata['per_q_mark'];

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
			        				'question'=>strip_tags($questiondataarray['question']), 
			        				'a'=>strip_tags($questiondataarray['a']), 
			        				'b'=>strip_tags($questiondataarray['b']),
			        				'c'=>strip_tags($questiondataarray['c']),
			        				'd'=>strip_tags($questiondataarray['d']),
			        				'answer'=>strip_tags($questiondataarray['answer']),
			        				'answer_exp'=>strip_tags($questiondataarray['answer_exp']),
			        				'question_video_link'=>$questiondataarray['question_video_link'],
			        				'question_img'=>$question_img,
			        				'current_score'=>$current_score,
			        				'total_score'=>$total_marks,
			        				'previous_question_key'=>(int)$previous_question_key,
			        				'next_question_key'=>(int)$next_question_key,
			        				'total_questions'=>$total_questions,
			        				'current_quiz'=>$current_quiz
			        			);

            			}
            			else{
            				$questionslist=null;
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
	}


	public function getsubtopictheoryquizquestions(Request $request)
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

		        		$quiztopicdetail=Quiztopic::where('subject',$courseid)->where('category',$topicid)->where('course_topic',$subtopicid)->where('quiz_type','2')->get()->first();
		        		if($quiztopicdetail)
		        		{
		        			$quiztopicdetaildata=$quiztopicdetail->toArray();
			        		$quizid=$quiztopicdetaildata['id'];

			        		$questiondata=Question::where('topic_id',$quizid)->where('question_status','1')->get()->first();
			        		if($questiondata)
			        		{
			        			$questiondataarray=$questiondata->toArray();

			        			$sort_order=$questiondataarray['sort_order'];

			        $questiondetailnext=Question::where('topic_id',$quizid)->where('sort_order','>',$sort_order)->where('question_status',1)->orderBy('sort_order','ASC')->get()->first();
				        if($questiondetailnext)
				        {
				        	$questiondetailnextdata=$questiondetailnext->toArray();
				        	$next_question_key=(int)$questiondetailnextdata['id'];
				        }
				        else{
				        	$next_question_key=0;
				        }

				        $questiondetailprevious=Question::where('topic_id',$quizid)->where('sort_order','<',$sort_order)->where('question_status',1)->orderBy('sort_order','DESC')->get()->first();
				        if($questiondetailprevious)
				        {
				        	$questiondetailpreviousdata=$questiondetailprevious->toArray();
				        	$previous_question_key=(int)$questiondetailpreviousdata['id'];
				        }
				        else{
				        	$previous_question_key=0;
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
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondataarray['id'],
			        				'question'=>strip_tags($questiondataarray['question']), 
			        				'answer_exp'=>strip_tags($questiondataarray['answer_exp']),
			        				'question_video_link'=>$questiondataarray['question_video_link'],
			        				'question_img'=>$question_img,
			        				'previous_question_key'=>(int)$previous_question_key,
		        					'next_question_key'=>(int)$next_question_key

			        			);


			        		}
			        		else{
			        			$questionslist=null;
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
	}

	public function gettheoryquizquestionexplaination(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','2')->get()->first();
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
				        	$next_question_key=(int)$questiondetailnextdata['id'];
				        }
				        else{
				        	$next_question_key=0;
				        }

				        $questiondetailprevious=Question::where('topic_id',$quizid)->where('sort_order','<',$sort_order)->where('question_status',1)->orderBy('sort_order','DESC')->get()->first();
				        if($questiondetailprevious)
				        {
				        	$questiondetailpreviousdata=$questiondetailprevious->toArray();
				        	$previous_question_key=(int)$questiondetailpreviousdata['id'];
				        }
				        else{
				        	$previous_question_key=0;
				        }

		        		if($questiondetaildata['question_img']!="")
				        {
				        	$question_img=url('/').'/images/questions/'.$questiondetaildata['question_img'];
				        }
				        else{
				        	$question_img='';
				        }

		        		 $questiondet=array(
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondetaildata['id'],
			        				'question'=>strip_tags($questiondetaildata['question']), 
			        				'answer_exp'=>strip_tags($questiondetaildata['answer_exp']),
			        				'question_video_link'=>$questiondetaildata['question_video_link'],
			        				'question_img'=>$question_img,
			        				'previous_question_key'=>(int)$previous_question_key,
		        					'next_question_key'=>(int)$next_question_key
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }

    }

    public function gettheoryquizquestiondetails(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','2')->get()->first();

	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

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

		        	$questiondata=Question::where('topic_id',$quizid)->where('id',$questionid)->get()->first();
		        	if($questiondata)
	        		{
	        			$questiondataarray=$questiondata->toArray();

	        			$sort_order=$questiondataarray['sort_order'];

				        $questiondetailnext=Question::where('topic_id',$quizid)->where('sort_order','>',$sort_order)->where('question_status',1)->orderBy('sort_order','ASC')->get()->first();
				        if($questiondetailnext)
				        {
				        	$questiondetailnextdata=$questiondetailnext->toArray();
				        	$next_question_key=(int)$questiondetailnextdata['id'];
				        }
				        else{
				        	$next_question_key=0;
				        }

				        $questiondetailprevious=Question::where('topic_id',$quizid)->where('sort_order','<',$sort_order)->where('question_status',1)->orderBy('sort_order','DESC')->get()->first();
				        if($questiondetailprevious)
				        {
				        	$questiondetailpreviousdata=$questiondetailprevious->toArray();
				        	$previous_question_key=(int)$questiondetailpreviousdata['id'];
				        }
				        else{
				        	$previous_question_key=0;
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
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondataarray['id'],
			        				'question'=>strip_tags($questiondataarray['question']), 
			        				'answer_exp'=>strip_tags($questiondataarray['answer_exp']),
			        				'question_video_link'=>$questiondataarray['question_video_link'],
			        				'question_img'=>$question_img,
			        				'next_question_key'=>(int)$next_question_key,
			        				'previous_question_key'=>(int)$previous_question_key
			        			);

	        		}
	        		else{
	        			$questionslist=null;
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }
	}






}