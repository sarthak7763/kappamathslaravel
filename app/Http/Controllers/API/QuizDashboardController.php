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
use App\Theoryquizresult;
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

		        		$userid=$user->id;
		        		$userdet=User::find($userid);

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
		        		}

		        		$quiztopicdetail=Quiztopic::where('subject',$courseid)->where('category',$topicid)->where('course_topic',$subtopicid)->where('quiz_type','1')->get()->first();
		        		if($quiztopicdetail)
		        		{
		        			$quiztopicdetaildata=$quiztopicdetail->toArray();

		        			if($quiztopicdetaildata['questions_limit']!="")
	        				{
	        					$total_questions_limit=$quiztopicdetaildata['questions_limit'];
	        				}
	        				else{
	        					$total_questions_limit=0;
	        				}

	        			$result_date=date('Y-m-d H:i:s');
	        			$result_end_date = date("Y-m-d H:i:s", strtotime('+12 hours', strtotime($result_date)));

                		$quizresultmarks=Resultmarks::where('user_id',$user->id)->whereRaw('"'.$result_date.'" between `result_marks_date` and `result_marks_end_date`')->whereRaw('FIND_IN_SET(?, topic_id)', $quiztopicdetaildata['id'])->where('result_type','1')->get()->first();
                		if($quizresultmarks==null)
                		{
                			$random_questionsdata = Question::where('topic_id',$quiztopicdetaildata['id'])->where('question_status','1')
	        						->inRandomOrder()
                						->limit($total_questions_limit)
                							->get();

                			if($random_questionsdata)
                			{
                				$random_questionsdataarray=$random_questionsdata->toArray();
                				$random_question_ids_arr=[];
                				foreach($random_questionsdataarray as $list)
                				{
                					$random_question_ids_arr[$quiztopicdetaildata['id']][]=$list['id'];

                					$random_question_ids[]=$list['id'];

                				}
                			}
                			else{
                				$random_question_ids_arr=[];
                			}

                			if(count($random_question_ids_arr) == 0)
            				{
            					return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
            				}
            				else{

            	$random_question_ids_arr =  array_map("unserialize", array_unique(array_map("serialize", $random_question_ids_arr)));

				$random_question_ids=array_unique($random_question_ids);

            				$random_questions_final_list=$random_question_ids_arr[$quiztopicdetaildata['id']];

            					if(count($random_questions_final_list)==0)
            					{
            						return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
            					}
            				}

            				try{

            			$newfinalquestionid=$random_questions_final_list[0];

            				$total_score=count($random_questions_final_list)*$quiztopicdetaildata['per_q_mark'];

            					$current_score=0;
            					$current_quiz=1;

            					if($quiztopicdetaildata['timer']!="")
            					{
            						$result_timer=$quiztopicdetaildata['timer'];
            					}
            					else{
            						$result_timer=0;
            					}

			                    $resultmarks = new Resultmarks;
			                    $resultmarks->topic_id=$quiztopicdetaildata['id'];
			                    $resultmarks->user_id=$user->id;
			                    $resultmarks->subject=$courseid;
			                    $resultmarks->marks=0;
			                    $resultmarks->result_timer=$result_timer;
			                    $resultmarks->total_questions=count($random_questions_final_list);
			                    $resultmarks->total_marks=$total_score;
			                    $resultmarks->result_marks_date=$result_date;
			                    $resultmarks->result_marks_end_date=$result_end_date;
			                    $resultmarks->result_type=1;
			                    $resultmarks->random_question_ids=json_encode($random_question_ids_arr);
			                    $resultmarks->save();
		                     
			                  }catch(\Exception $e){
			                    return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong.']);     
			                 }
                		}
                		else{
                			$quizresultmarksarray=$quizresultmarks->toArray();

                			$resultid=$quizresultmarksarray['id'];
                			$newresultmarksdata = Resultmarks::find($resultid);

		        			$random_question_ids_db=$quizresultmarksarray['random_question_ids'];

		        			$random_question_ids_arr=json_decode($random_question_ids_db,true);

		        			$random_questions_final_list=$random_question_ids_arr[$quiztopicdetaildata['id']];

		        			$attempt_question_ids_db=$quizresultmarksarray['question_ids'];
		        			if($attempt_question_ids_db!="")
		        			{
		        			$attempt_question_ids_arr=json_decode($attempt_question_ids_db,true);

		        			$attempt_questions_final_list=$attempt_question_ids_arr[$quiztopicdetaildata['id']];

		        			$questions_array_diff=array_values(array_diff($random_questions_final_list, $attempt_questions_final_list));
		        			if(count($questions_array_diff) > 0)
		        			{
		        				$newfinalquestionid=$questions_array_diff[0];
		        			}
		        			else{
		        				$newfinalquestionid="";
		        			}
		        			
		        			}
		        			else{
		        			$newfinalquestionid=$random_questions_final_list[0];
		        			}

		        			$total_score=count($random_questions_final_list)*$quiztopicdetaildata['per_q_mark'];

		        			$current_score=$quizresultmarksarray['marks'];

		        			$currentquestionindex = array_keys($random_questions_final_list,$newfinalquestionid);

		        			if(count($currentquestionindex) > 0)
		        			{
		        				$current_quiz=$currentquestionindex[0]+1;
		        			}
		        			else{
		        				$current_quiz=1;
		        			}
		        			
                		}

                		$questiondata=Question::where('topic_id',$quiztopicdetaildata['id'])->where('id',$newfinalquestionid)->where('question_status','1')->get()->first();
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

					if($questiondataarray['question_video_link']!="")
			        {
			        	$checkquestionvideo=getVideoDetails($questiondataarray['question_video_link']);

			        	if($checkquestionvideo['code']=="400")
			            {
			              $question_video_link="";
			            }
			            else{
			            	$question_video_link=$checkquestionvideo['subtopicvideourl'];
			            }
			        }
			        else{
			        	$question_video_link="";
			        }

			        if($questiondataarray['answer_explaination_img']!="")
			        {
			        	$answer_explaination_img=url('/').'/images/questions/'.$questiondataarray['answer_explaination_img'];
			        }
			        else{
			        	$answer_explaination_img='';
			        }

			        if($questiondataarray['answer_explaination_video_link']!="")
			        {
			        	$checkanswervideo=getVideoDetails($questiondataarray['answer_explaination_video_link']);

			        	if($checkanswervideo['code']=="400")
			            {
			              $answer_explaination_video_link="";
			            }
			            else{
			            	$answer_explaination_video_link=$checkanswervideo['subtopicvideourl'];
			            }
			        }
			        else{
			        	$answer_explaination_video_link="";
			        }


					        $quizquestion=html_entity_decode($questiondataarray['question']);

					        $quiz_option_a=html_entity_decode($questiondataarray['a']);

					        $quiz_option_b=html_entity_decode($questiondataarray['b']);

					        $quiz_option_c=html_entity_decode($questiondataarray['c']);

					        $quiz_option_d=html_entity_decode($questiondataarray['d']);

					        if($questiondataarray['answer_exp']!="")
					        {
					        	$quiz_answer_exp=html_entity_decode($questiondataarray['answer_exp']);
					        }
					        else{
					        	$quiz_answer_exp="";
					        }

					if($questiondataarray['a_image']!="")
			        {
			        	$option_image_a=url('/').'/images/questions/options/'.$questiondataarray['a_image'];
			        }
			        else{
			        	$option_image_a='';
			        }

			        if($questiondataarray['b_image']!="")
			        {
			        	$option_image_b=url('/').'/images/questions/options/'.$questiondataarray['b_image'];
			        }
			        else{
			        	$option_image_b='';
			        }

			        if($questiondataarray['c_image']!="")
			        {
			        	$option_image_c=url('/').'/images/questions/options/'.$questiondataarray['c_image'];
			        }
			        else{
			        	$option_image_c='';
			        }

			        if($questiondataarray['d_image']!="")
			        {
			        	$option_image_d=url('/').'/images/questions/options/'.$questiondataarray['d_image'];
			        }
			        else{
			        	$option_image_d='';
			        }
					        
					        $questionslist=array(
					        		'course_id'=>(int)$courseid,
			        				'course_name'=>$subject->title,
			        				'topic_name'=>$coursetopicsdetaildata['category_name'],
			        				'sub_topic_name'=>$coursesubtopicsdetaildata['topic_name'],
			        				'quiz_name'=>$quiztopicdetaildata['title'],
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondataarray['id'],
			        				'question'=>$quizquestion, 
			        				'a'=>$quiz_option_a, 
			        				'b'=>$quiz_option_b,
			        				'c'=>$quiz_option_c,
			        				'd'=>$quiz_option_d,
			        				'answer'=>strip_tags($questiondataarray['answer']),
			        				'answer_exp'=>$quiz_answer_exp,
			        				'question_img'=>$question_img,
			        				'question_video_link'=>$question_video_link,
			        				'answer_explaination_img'=>$answer_explaination_img,
			        				'answer_explaination_video_link'=>$answer_explaination_video_link,
			        				'current_score'=>$current_score,
			        				'total_score'=>$total_score,
			        				'total_questions'=>count($random_questions_final_list),
			        				'current_quiz'=>$current_quiz,
			        				'option_status'=>$questiondataarray['option_status'],
			        				'a_image'=>$option_image_a,
			        				'b_image'=>$option_image_b,
			        				'c_image'=>$option_image_c,
			        				'd_image'=>$option_image_d
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
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
	        	$result_date=date('Y-m-d H:i:s');

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','1')->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();
	        		$randomquizresultmarks=Resultmarks::where('user_id',$user->id)->whereRaw('"'.$result_date.'" between `result_marks_date` and `result_marks_end_date`')->whereRaw('FIND_IN_SET(?, topic_id)', $quizid)->where('result_type','1')->get()->first();

	        		if($randomquizresultmarks)
	        		{
	        			$randomquizresultmarksarray=$randomquizresultmarks->toArray();

	        			$result_id=$randomquizresultmarksarray['id'];

	        			$random_question_ids_db=$randomquizresultmarksarray['random_question_ids'];

		        		$random_question_ids_arr=json_decode($random_question_ids_db,true);

		        		$random_questions_final_list=$random_question_ids_arr[$quiztopicdetaildata['id']];

		        		if(in_array($questionid, $random_questions_final_list))
		        		{
		        			$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->where('question_status','!=','0')->get()->first();
		        			if($questiondetail)
		        			{
		        				$questiondetaildata=$questiondetail->toArray();

		        				$currentquestionindex = array_keys($random_questions_final_list,$questionid);

		        		if(count($currentquestionindex) > 0)
	        			{
	        				$currentquestionindex=$currentquestionindex[0];

	        				$next_question_index=$currentquestionindex+1;
	        				$previous_question_index=$currentquestionindex-1;

	        				if(isset($random_questions_final_list[$previous_question_index]))
	        				{
	        					$previous_question_key=$random_questions_final_list[$previous_question_index];
	        				}
	        				else{
	        					$previous_question_key=0;
	        				}

	        				if(isset($random_questions_final_list[$next_question_index]))
	        				{
	        					$next_question_key=$random_questions_final_list[$next_question_index];
	        				}
	        				else{
	        					$next_question_key=0;
	        				}

	        			}
	        			else{
	        				$previous_question_key=0;
	        				$next_question_key=0;
	        			}

	        			$total_questions=count($random_questions_final_list);

	        			$total_marks=$total_questions*$quiztopicdetaildata['per_q_mark'];

	        			if($questiondetaildata['answer']==$user_anwer)
	        			{
	        				$data=$this->submitcorrectquizanswer($result_id,$user->id,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer);
	        				if($data['status']==400)
	        				{
	        					return $this::sendError('Unauthorised Exception.', ['error'=>$data['message']]);
	        				}
	        				else{
	        					$success['questiondet'] =  $data['message'];
        						return $this::sendResponse($success, 'Questions Details.');
	        				}
	        			}
	        			elseif($questiondetaildata['answer']!=$user_anwer){
	        				$data=$this->submitwrongquizanswer($result_id,$user->id,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer);
	        				if($data['status']==400)
	        				{
	        					return $this::sendError('Unauthorised Exception.', ['error'=>$data['message']]);
	        				}
	        				else{
	        					$success['questiondet'] =  $data['message'];
        						return $this::sendResponse($success, 'Questions Details.');
	        				}
	        			}
	        			else{
	        				$data=$this->skipquizanswer($result_id,$user->id,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer);
	        				if($data['status']==400)
	        				{
	        					return $this::sendError('Unauthorised Exception.', ['error'=>$data['message']]);
	        				}
	        				else{
	        					$success['questiondet'] =  $data['message'];
        						return $this::sendResponse($success, 'Questions Details.');
	        				}
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

	public function submitcorrectquizanswer($result_id,$userid,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer)
	{
		try{
		$quizresultdetail=Result::where('user_id',$userid)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_marks_id',$randomquizresultmarksarray['id'])->get()->first();
		if($quizresultdetail)
		{
			$data=array('status'=>400,'message'=>'Answer already submitted');
            return $data; 
		}
		else{
			try{
				$resultuser = new Result;
				$resultuser->topic_id=$quiztopicdetaildata['id'];
				$resultuser->user_id=$userid;
				$resultuser->question_id=$questiondetaildata['id'];
				$resultuser->result_marks_id=$randomquizresultmarksarray['id'];
				$resultuser->user_answer=$user_anwer;
            	$resultuser->answer=1;
				$resultuser->marks=$quiztopicdetaildata['per_q_mark'];
				$resultuser->result_date=$result_date;
				$resultuser->save();
             
          }catch(\Exception $e){
            $data=array('status'=>400,'message'=>'Something went wrong');
            return $data;     
         }

        if($questiondetaildata['answer_exp']!="")
        {
        	$quiz_answer_exp=html_entity_decode($questiondetaildata['answer_exp']);
        }
        else{
        	$quiz_answer_exp="";
        }

         $questiondet=array(
			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			'quiz_id'=>$quiztopicdetaildata['id'],
			'question_id'=>$questiondetaildata['id'],
			'answer_exp'=>$quiz_answer_exp,
			'answer_status'=>1, //correct
			'previous_question_key'=>(int)$previous_question_key,
			'next_question_key'=>(int)$next_question_key,
			'result_id'=>(int)$result_id,
			'next_quiz_id'=>$quiztopicdetaildata['id'],
			'previous_quiz_id'=>$quiztopicdetaildata['id']
		);

		}

		$quizresultmarks=Resultmarks::find($randomquizresultmarksarray['id']);
         if($quizresultmarks)
         {
         	$dbquestion_ids=$quizresultmarks->question_ids;
         	if($dbquestion_ids!="")
         	{
         		$dbquestion_idsarray=json_decode($dbquestion_ids,true);
         		if(count($dbquestion_idsarray) > 0)
         		{
         			$dbquizquestionsarray=$dbquestion_idsarray[$quiztopicdetaildata['id']];
	         		array_push($dbquizquestionsarray,$questiondetaildata['id']);

	         		$newdbquestion_ids=[];
	         		foreach($dbquizquestionsarray as $listval)
	         		{
	         			$newdbquestion_ids[$quiztopicdetaildata['id']][]=$listval;
	         		}

	         		$finalnewdbquestion_ids=json_encode($newdbquestion_ids);
         		}
         		else{
         			$newdbquestion_ids[$quiztopicdetaildata['id']][]=$questiondetaildata['id'];
         			$finalnewdbquestion_ids=json_encode($newdbquestion_ids);
         		}
         	}
         	else{
         		$newdbquestion_ids[$quiztopicdetaildata['id']][]=$questiondetaildata['id'];
         		$finalnewdbquestion_ids=json_encode($newdbquestion_ids);
         	}

         	$previousresultmarks=$quizresultmarks->marks;
         	$newresultmarks=$previousresultmarks+$quiztopicdetaildata['per_q_mark'];

         	$quizresultmarks->marks=$newresultmarks;

         	if($quiztopicdetaildata['timer']!="")
         	{
         		$result_timer=$quiztopicdetaildata['timer'];
         	}
         	else{
         		$result_timer=0;
         	}

			$quizresultmarks->result_timer=$result_timer;
			$quizresultmarks->question_ids=$finalnewdbquestion_ids;

			try{
	            $quizresultmarks->save();
	         }catch(\Exception $e){
	            $data=array('status'=>400,'message'=>'Something went wrong');
            	return $data;
	         }			         
         }
         else{
         	$data=array('status'=>400,'message'=>'Something went wrong');
            return $data; 
         }

        $data=array('status'=>200,'message'=>$questiondet);
        return $data; 
    }
    catch(\Exception $e){
                  $data=array('status'=>400,'message'=>'Something went wrong');
                  return $data;   
               }

	}

	public function  submitwrongquizanswer($result_id,$userid,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer)
	{
		try{
		$quizresultdetail=Result::where('user_id',$userid)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_marks_id',$randomquizresultmarksarray['id'])->get()->first();

		if($quizresultdetail)
		{
			$data=array('status'=>400,'message'=>'Answer already submitted');
            return $data;
		}
		else{
			try{
            $resultuser = new Result;
            $resultuser->topic_id=$quiztopicdetaildata['id'];
            $resultuser->user_id=$userid;
            $resultuser->question_id=$questiondetaildata['id'];
            $resultuser->result_marks_id=$randomquizresultmarksarray['id'];
            $resultuser->user_answer=$user_anwer;
            $resultuser->answer=2;
            $resultuser->marks=0;
            $resultuser->result_date=$result_date;
            $resultuser->save();
             
          }catch(\Exception $e){
            $data=array('status'=>400,'message'=>'Something went wrong');
            return $data;     
         }

         if($questiondetaildata['answer_exp']!="")
        {
        	$quiz_answer_exp=html_entity_decode($questiondetaildata['answer_exp']);
        }
        else{
        	$quiz_answer_exp="";
        }

         $questiondet=array(
			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			'quiz_id'=>$quiztopicdetaildata['id'],
			'question_id'=>$questiondetaildata['id'],
			'answer_exp'=>$quiz_answer_exp,
			'answer_status'=>2, //correct
			'previous_question_key'=>(int)$previous_question_key,
			'next_question_key'=>(int)$next_question_key,
			'result_id'=>(int)$result_id,
			'next_quiz_id'=>$quiztopicdetaildata['id'],
			'previous_quiz_id'=>$quiztopicdetaildata['id']
		);

		}

		$quizresultmarks=Resultmarks::find($randomquizresultmarksarray['id']);
         if($quizresultmarks)
         {
         	$dbquestion_ids=$quizresultmarks->question_ids;
         	if($dbquestion_ids!="")
         	{
         		$dbquestion_idsarray=json_decode($dbquestion_ids,true);
         		if(count($dbquestion_idsarray) > 0)
         		{
         			$dbquizquestionsarray=$dbquestion_idsarray[$quiztopicdetaildata['id']];
	         		array_push($dbquizquestionsarray,$questiondetaildata['id']);

	         		$newdbquestion_ids=[];
	         		foreach($dbquizquestionsarray as $listval)
	         		{
	         			$newdbquestion_ids[$quiztopicdetaildata['id']][]=$listval;
	         		}

	         		$finalnewdbquestion_ids=json_encode($newdbquestion_ids);
         		}
         		else{
         			$newdbquestion_ids[$quiztopicdetaildata['id']][]=$questiondetaildata['id'];
         			$finalnewdbquestion_ids=json_encode($newdbquestion_ids);
         		}

         	}
         	else{
         		$newdbquestion_ids[$quiztopicdetaildata['id']][]=$questiondetaildata['id'];
         		$finalnewdbquestion_ids=json_encode($newdbquestion_ids);
         	}

         	$previousresultmarks=$quizresultmarks->marks;
         	$newresultmarks=$previousresultmarks+0;


         	if($quiztopicdetaildata['timer']!="")
         	{
         		$result_timer=$quiztopicdetaildata['timer'];
         	}
         	else{
         		$result_timer=0;
         	}

         	$quizresultmarks->marks=$newresultmarks;
			$quizresultmarks->result_timer=$result_timer;
			$quizresultmarks->question_ids=$finalnewdbquestion_ids;

			try{
	            $quizresultmarks->save();
	         }catch(\Exception $e){
	            $data=array('status'=>400,'message'=>'Something went wrong');
            	return $data;
	         }			         
         }
         else{
         	$data=array('status'=>400,'message'=>'Something went wrong');
            return $data; 
         }

		$data=array('status'=>200,'message'=>$questiondet);
        return $data; 
    }
    catch(\Exception $e){
                  $data=array('status'=>400,'message'=>'Something went wrong');
                  return $data;    
               }

	}


	public function  skipquizanswer($result_id,$userid,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer)
	{
		try{
		$quizresultdetail=Result::where('user_id',$userid)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_marks_id',$randomquizresultmarksarray['id'])->get()->first();

		if($quizresultdetail)
		{
			$data=array('status'=>400,'message'=>'Answer already submitted');
            return $data;
		}
		else{
			try{
            $resultuser = new Result;
            $resultuser->topic_id=$quiztopicdetaildata['id'];
            $resultuser->user_id=$userid;
            $resultuser->question_id=$questiondetaildata['id'];
            $resultuser->result_marks_id=$randomquizresultmarksarray['id'];
            $resultuser->user_answer=$user_anwer;
            $resultuser->answer=0;
            $resultuser->marks=0;
            $resultuser->result_date=$result_date;
            $resultuser->save();
             
          }catch(\Exception $e){
            $data=array('status'=>400,'message'=>'Something went wrong');
            return $data;     
         }

         if($questiondetaildata['answer_exp']!="")
        {
        	$quiz_answer_exp=html_entity_decode($questiondetaildata['answer_exp']);
        }
        else{
        	$quiz_answer_exp="";
        }

         $questiondet=array(
			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			'quiz_id'=>$quiztopicdetaildata['id'],
			'question_id'=>$questiondetaildata['id'],
			'answer_exp'=>$quiz_answer_exp,
			'answer_status'=>2, //correct
			'previous_question_key'=>(int)$previous_question_key,
			'next_question_key'=>(int)$next_question_key,
			'result_id'=>(int)$result_id,
			'next_quiz_id'=>$quiztopicdetaildata['id'],
			'previous_quiz_id'=>$quiztopicdetaildata['id']
		);

		}

		$quizresultmarks=Resultmarks::find($randomquizresultmarksarray['id']);
         if($quizresultmarks)
         {
         	$dbquestion_ids=$quizresultmarks->question_ids;
         	if($dbquestion_ids!="")
         	{
         		$dbquestion_idsarray=json_decode($dbquestion_ids,true);
         		if(count($dbquestion_idsarray) > 0)
         		{
         			$dbquizquestionsarray=$dbquestion_idsarray[$quiztopicdetaildata['id']];
	         		array_push($dbquizquestionsarray,$questiondetaildata['id']);

	         		$newdbquestion_ids=[];
	         		foreach($dbquizquestionsarray as $listval)
	         		{
	         			$newdbquestion_ids[$quiztopicdetaildata['id']][]=$listval;
	         		}

	         		$finalnewdbquestion_ids=json_encode($newdbquestion_ids);
         		}
         		else{
         			$newdbquestion_ids[$quiztopicdetaildata['id']][]=$questiondetaildata['id'];
         			$finalnewdbquestion_ids=json_encode($newdbquestion_ids);
         		}

         	}
         	else{
         		$newdbquestion_ids[$quiztopicdetaildata['id']][]=$questiondetaildata['id'];
         		$finalnewdbquestion_ids=json_encode($newdbquestion_ids);
         	}

         	$previousresultmarks=$quizresultmarks->marks;
         	$newresultmarks=$previousresultmarks+0;

         	if($quiztopicdetaildata['timer']!="")
         	{
         		$result_timer=$quiztopicdetaildata['timer'];
         	}
         	else{
         		$result_timer=0;
         	}

         	$quizresultmarks->marks=$newresultmarks;
			$quizresultmarks->result_timer=$result_timer;
			$quizresultmarks->question_ids=$finalnewdbquestion_ids;

			try{
	            $quizresultmarks->save();
	         }catch(\Exception $e){
	            $data=array('status'=>400,'message'=>'Something went wrong');
            	return $data;
	         }			         
         }
         else{
         	$data=array('status'=>400,'message'=>'Something went wrong');
            return $data; 
         }

		$data=array('status'=>200,'message'=>$questiondet);
        return $data; 
    }
    catch(\Exception $e){
                  $data=array('status'=>400,'message'=>'Something went wrong');
                  return $data;    
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
	        	$result_date=date('Y-m-d H:i:s');

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','1')->get()->first();

	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();

	        		$course_id=$quiztopicdetaildata['subject'];
	        		$topic_id=$quiztopicdetaildata['category'];

	        		$coursetopicsdetail=Subjectcategory::where('subject',$course_id)->where('id',$topic_id)->get()->first();
	        		if($coursetopicsdetail)
	        		{
	        			$topic_name=$coursetopicsdetail->category_name;
	        		}
	        		else{
	        			$topic_name="";
	        		}

	        		$sub_topic_id=$quiztopicdetaildata['course_topic'];

	        		if(isset($request->result_id) && $request->result_id!="")
	        		{
	        		$randomquizresultmarks=Resultmarks::where('id',$request->result_id)->where('result_type','1')->get()->first();
	        		}
	        		else{
	        		$randomquizresultmarks=Resultmarks::where('user_id',$user->id)->whereRaw('"'.$result_date.'" between `result_marks_date` and `result_marks_end_date`')->whereRaw('FIND_IN_SET(?, topic_id)', $quizid)->where('result_type','1')->get()->first();
	        		}

	        		if($randomquizresultmarks)
	        		{
	        			$randomquizresultmarksarray=$randomquizresultmarks->toArray();

	        			$result_id=$randomquizresultmarksarray['id'];

	        			$random_question_ids_db=$randomquizresultmarksarray['random_question_ids'];

		        		$random_question_ids_arr=json_decode($random_question_ids_db,true);

		        		$random_questions_final_list=$random_question_ids_arr[$quiztopicdetaildata['id']];

		        		if(in_array($questionid, $random_questions_final_list))
		        		{
		        			$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->where('question_status','!=','0')->get()->first();
		        			if($questiondetail)
		        			{
		        				$questiondetaildata=$questiondetail->toArray();

		        				$questiondetaildata=$questiondetail->toArray();

		        				$currentquestionindex = array_keys($random_questions_final_list,$questionid);

		        		if(count($currentquestionindex) > 0)
	        			{
	        				$currentquestionindex=$currentquestionindex[0];
	        				$current_quiz=$currentquestionindex+1;
	        				$next_question_index=$currentquestionindex+1;
	        				$previous_question_index=$currentquestionindex-1;

	        				if(isset($random_questions_final_list[$previous_question_index]))
	        				{
	        					$previous_question_key=$random_questions_final_list[$previous_question_index];
	        				}
	        				else{
	        					$previous_question_key=0;
	        				}

	        				if(isset($random_questions_final_list[$next_question_index]))
	        				{
	        					$next_question_key=$random_questions_final_list[$next_question_index];
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

				    if($questiondetaildata['question_video_link']!="")
			        {
			        	$checkquestionvideo=getVideoDetails($questiondetaildata['question_video_link']);

			        	if($checkquestionvideo['code']=="400")
			            {
			              $question_video_link="";
			            }
			            else{
			            	$question_video_link=$checkquestionvideo['subtopicvideourl'];
			            }
			        }
			        else{
			        	$question_video_link="";
			        }

			        if($questiondetaildata['answer_explaination_img']!="")
			        {
			        	$answer_explaination_img=url('/').'/images/questions/'.$questiondetaildata['answer_explaination_img'];
			        }
			        else{
			        	$answer_explaination_img='';
			        }


			        if($questiondetaildata['answer_explaination_video_link']!="")
			        {
			        	$checkanswervideo=getVideoDetails($questiondetaildata['answer_explaination_video_link']);

			        	if($checkanswervideo['code']=="400")
			            {
			              $answer_explaination_video_link="";
			            }
			            else{
			            	$answer_explaination_video_link=$checkanswervideo['subtopicvideourl'];
			            }
			        }
			        else{
			        	$answer_explaination_video_link="";
			        }


			        if($questiondetaildata['a_image']!="")
			        {
			        	$option_image_a=url('/').'/images/questions/options/'.$questiondetaildata['a_image'];
			        }
			        else{
			        	$option_image_a='';
			        }

			        if($questiondetaildata['b_image']!="")
			        {
			        	$option_image_b=url('/').'/images/questions/options/'.$questiondetaildata['b_image'];
			        }
			        else{
			        	$option_image_b='';
			        }

			        if($questiondetaildata['c_image']!="")
			        {
			        	$option_image_c=url('/').'/images/questions/options/'.$questiondetaildata['c_image'];
			        }
			        else{
			        	$option_image_c='';
			        }

			        if($questiondetaildata['d_image']!="")
			        {
			        	$option_image_d=url('/').'/images/questions/options/'.$questiondetaildata['d_image'];
			        }
			        else{
			        	$option_image_d='';
			        }

				        $current_score=$randomquizresultmarksarray['marks'];
				        $total_questions=count($random_questions_final_list);

	        			$total_marks=$randomquizresultmarksarray['total_marks'];

				        $quizquestion=html_entity_decode($questiondetaildata['question']);

						$quiz_option_a=html_entity_decode($questiondetaildata['a']);

						$quiz_option_b=html_entity_decode($questiondetaildata['b']);

						$quiz_option_c=html_entity_decode($questiondetaildata['c']);

						$quiz_option_d=html_entity_decode($questiondetaildata['d']);

						if($questiondetaildata['answer_exp']!="")
						{
							$quiz_answer_exp=html_entity_decode($questiondetaildata['answer_exp']);
						}
						else{
							$quiz_answer_exp="";
						}

	        			$questiondet=array(
		        		 			'course_id'=>$course_id,
		        		 			'topic_id'=>$topic_id,
		        		 			'topic_name'=>$topic_name,
		        		 			'sub_topic_id'=>$sub_topic_id,
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondetaildata['id'],
			        				'question'=>$quizquestion, 
			        				'answer_exp'=>$quiz_answer_exp,
			        				'question_img'=>$question_img,
			        				'question_video_link'=>$question_video_link,
			        				'answer_explaination_img'=>$answer_explaination_img,
			        				'answer_explaination_video_link'=>$answer_explaination_video_link,
			        				'previous_question_key'=>(int)$previous_question_key,
		        					'next_question_key'=>(int)$next_question_key,
		        					'current_score'=>$current_score,
			        				'total_score'=>$total_marks,
			        				'total_questions'=>$total_questions,
			        				'current_quiz'=>$current_quiz,
			        				'result_id'=>(int)$result_id,
			        				'next_quiz_id'=>$quiztopicdetaildata['id'],
			        				'previous_quiz_id'=>$quiztopicdetaildata['id']
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
	        	$result_date=date('Y-m-d H:i:s');

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','1')->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();
	        		$course_id=$quiztopicdetaildata['subject'];
	        		$topic_id=$quiztopicdetaildata['category'];
	        		$sub_topic_id=$quiztopicdetaildata['course_topic'];
	        		
	        		$randomquizresultmarks=Resultmarks::where('user_id',$user->id)->whereRaw('"'.$result_date.'" between `result_marks_date` and `result_marks_end_date`')->whereRaw('FIND_IN_SET(?, topic_id)', $quizid)->where('result_type','1')->get()->first();
	        		if($randomquizresultmarks)
	        		{
	        			$randomquizresultmarksarray=$randomquizresultmarks->toArray();

	        			$result_id=$randomquizresultmarksarray['id'];

	        			$random_question_ids_db=$randomquizresultmarksarray['random_question_ids'];

		        		$random_question_ids_arr=json_decode($random_question_ids_db,true);

		        		$random_questions_final_list=$random_question_ids_arr[$quiztopicdetaildata['id']];

		        		if(in_array($questionid, $random_questions_final_list))
		        		{
		        			$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->where('question_status','!=','0')->get()->first();
		        			if($questiondetail)
		        			{
		        				$questiondetaildata=$questiondetail->toArray();

		        				$questiondetaildata=$questiondetail->toArray();

		        				$currentquestionindex = array_keys($random_questions_final_list,$questionid);

		        		if(count($currentquestionindex) > 0)
	        			{
	        				$currentquestionindex=$currentquestionindex[0];
	        				$current_quiz=$currentquestionindex+1;
	        				$next_question_index=$currentquestionindex+1;
	        				$previous_question_index=$currentquestionindex-1;

	        				if(isset($random_questions_final_list[$previous_question_index]))
	        				{
	        					$previous_question_key=$random_questions_final_list[$previous_question_index];
	        				}
	        				else{
	        					$previous_question_key=0;
	        				}

	        				if(isset($random_questions_final_list[$next_question_index]))
	        				{
	        					$next_question_key=$random_questions_final_list[$next_question_index];
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

				    if($questiondetaildata['question_video_link']!="")
			        {
			        	$checkquestionvideo=getVideoDetails($questiondetaildata['question_video_link']);

			        	if($checkquestionvideo['code']=="400")
			            {
			              $question_video_link="";
			            }
			            else{
			            	$question_video_link=$checkquestionvideo['subtopicvideourl'];
			            }
			        }
			        else{
			        	$question_video_link="";
			        }

			        if($questiondetaildata['answer_explaination_img']!="")
			        {
			        	$answer_explaination_img=url('/').'/images/questions/'.$questiondetaildata['answer_explaination_img'];
			        }
			        else{
			        	$answer_explaination_img='';
			        }

			        if($questiondetaildata['answer_explaination_video_link']!="")
			        {
			        	$checkanswervideo=getVideoDetails($questiondetaildata['answer_explaination_video_link']);

			        	if($checkanswervideo['code']=="400")
			            {
			              $answer_explaination_video_link="";
			            }
			            else{
			            	$answer_explaination_video_link=$checkanswervideo['subtopicvideourl'];
			            }
			        }
			        else{
			        	$answer_explaination_video_link="";
			        }


			        if($questiondetaildata['a_image']!="")
			        {
			        	$option_image_a=url('/').'/images/questions/options/'.$questiondetaildata['a_image'];
			        }
			        else{
			        	$option_image_a='';
			        }

			        if($questiondetaildata['b_image']!="")
			        {
			        	$option_image_b=url('/').'/images/questions/options/'.$questiondetaildata['b_image'];
			        }
			        else{
			        	$option_image_b='';
			        }

			        if($questiondetaildata['c_image']!="")
			        {
			        	$option_image_c=url('/').'/images/questions/options/'.$questiondetaildata['c_image'];
			        }
			        else{
			        	$option_image_c='';
			        }

			        if($questiondetaildata['d_image']!="")
			        {
			        	$option_image_d=url('/').'/images/questions/options/'.$questiondetaildata['d_image'];
			        }
			        else{
			        	$option_image_d='';
			        }


				        $current_score=$randomquizresultmarksarray['marks'];
				        $total_questions=count($random_questions_final_list);

	        			$total_marks=$randomquizresultmarksarray['total_marks'];

				        $quizquestion=html_entity_decode($questiondetaildata['question']);

						$quiz_option_a=html_entity_decode($questiondetaildata['a']);

						$quiz_option_b=html_entity_decode($questiondetaildata['b']);

						$quiz_option_c=html_entity_decode($questiondetaildata['c']);

						$quiz_option_d=html_entity_decode($questiondetaildata['d']);

						if($questiondetaildata['answer_exp']!="")
						{
							$quiz_answer_exp=html_entity_decode($questiondetaildata['answer_exp']);
						}
						else{
							$quiz_answer_exp="";
						}
				        

	        			$questionslist=array(
		        		 			'course_id'=>$course_id,
		        		 			'topic_id'=>$topic_id,
		        		 			'sub_topic_id'=>$sub_topic_id,
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondetaildata['id'],
			        				'question'=>$quizquestion,
			        				'a'=>$quiz_option_a, 
			        				'b'=>$quiz_option_b,
			        				'c'=>$quiz_option_c,
			        				'd'=>$quiz_option_d,
			        				'answer'=>strip_tags($questiondetaildata['answer']), 
			        				'answer_exp'=>$quiz_answer_exp,
			        				'question_img'=>$question_img,
			        				'question_video_link'=>$question_video_link,
			        				'answer_explaination_img'=>$answer_explaination_img,
			        				'answer_explaination_video_link'=>$answer_explaination_video_link,
			        				'previous_question_key'=>(int)$previous_question_key,
		        					'next_question_key'=>(int)$next_question_key,
		        					'current_score'=>$current_score,
			        				'total_score'=>$total_marks,
			        				'total_questions'=>$total_questions,
			        				'current_quiz'=>$current_quiz,
			        				'result_id'=>(int)$result_id,
			        				'next_quiz_id'=>$quiztopicdetaildata['id'],
			        				'previous_quiz_id'=>$quiztopicdetaildata['id'],
			        				'option_status'=>$questiondetaildata['option_status'],
			        				'a_image'=>$option_image_a,
			        				'b_image'=>$option_image_b,
			        				'c_image'=>$option_image_c,
			        				'd_image'=>$option_image_d
			        			);


		        			}
		        			else{
		        				$questionslist=null;
		        			}

		        			$success['questionslist'] =  $questionslist;
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
	        	$result_date=date('Y-m-d');

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

			        		if($quiztopicdetaildata['questions_limit']!="")
	        				{
	        					$total_questions_limit=$quiztopicdetaildata['questions_limit'];
	        				}
	        				else{
	        					$total_questions_limit=0;
	        				}

	        				$random_questionsdata = Question::where('topic_id',$quiztopicdetaildata['id'])->where('question_status','1')->inRandomOrder()->limit($total_questions_limit)->get();

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

                			if(count($random_question_ids) > 0)
                			{

                			$random_question_ids=array_unique($random_question_ids);

                			$theoryquizresultdata=Theoryquizresult::where('user_id',$user->id)->whereRaw('FIND_IN_SET(?, topic_id)', $quiztopicdetaildata['id'])->where('result_type','1')->where('result_date',$result_date)->get()->first();
                			if($theoryquizresultdata==null)
                			{
                				$theoryresultdata=$this->inserttheoryquizresult($quiztopicdetaildata,$user->id,$random_question_ids,$result_date,$subject,$coursetopicsdetaildata,$coursesubtopicsdetaildata);

                	if($theoryresultdata)
	        		{
	        			if($theoryresultdata['code']=="200")
	        			{
	        				$success['questionslist'] =$theoryresultdata['message'];
                			return $this::sendResponse($success, 'Questions List.');
	        			}
	        			else{
	        				return $this::sendError('Unauthorised Exception.', ['error'=>$theoryresultdata['message']]);
	        			}
	        		}
	        		else{
	        			return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
	        		}

                			}
                			else{
                				$theoryresultdata=$this->updatetheoryquizresult($quiztopicdetaildata,$user->id,$random_question_ids,$result_date,$subject,$coursetopicsdetaildata,$coursesubtopicsdetaildata);

                	if($theoryresultdata)
	        		{
	        			if($theoryresultdata['code']=="200")
	        			{
	        				$success['questionslist'] =$theoryresultdata['message'];
                			return $this::sendResponse($success, 'Questions List.');
	        			}
	        			else{
	        				return $this::sendError('Unauthorised Exception.', ['error'=>$theoryresultdata['message']]);
	        			}
	        		}
	        		else{
	        			return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
	        		}
                			}

                			}
                			else{
                				return $this::sendError('Unauthorised Exception.', ['error'=>'No more questions available.']);
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

	public function inserttheoryquizresult($quiztopicdetaildata,$userid,$random_question_ids,$result_date,$subject,$coursetopicsdetaildata,$coursesubtopicsdetaildata)
	{
		try{
			$total_questions=count($random_question_ids);

			$theoryquizresult = new Theoryquizresult;
	        $theoryquizresult->topic_id=$quiztopicdetaildata['id'];

	        $theoryquizresult->user_id=$userid;

	        if($quiztopicdetaildata['timer']!="")
         	{
         		$result_timer=$quiztopicdetaildata['timer'];
         	}
         	else{
         		$result_timer=0;
         	}

	        $theoryquizresult->result_timer=$result_timer;
	        $theoryquizresult->total_questions=$total_questions;
	        
	        $theoryquizresult->result_type=1;
	        $theoryquizresult->result_date=$result_date;
	        $theoryquizresult->random_questions=json_encode($random_question_ids);
	        $theoryquizresult->save();
			}
			catch(\Exception $e){
	          $data=array('code'=>'400','message'=>'Something went wrong.');
		  		return $data;    
	       }

	       $newfinalquestionid=$random_question_ids[0];
	       $questiondata=Question::where('topic_id',$quiztopicdetaildata['id'])->where('id',$newfinalquestionid)->get()->first();

	       if($questiondata)
	       {
	       		$questiondataarray=$questiondata->toArray();

	       		$currentquestionindex = array_keys($random_question_ids,$newfinalquestionid);

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

    			if($questiondataarray['question_img']!="")
		        {
		        	$question_img=url('/').'/images/questions/'.$questiondataarray['question_img'];
		        }
		        else{
		        	$question_img='';
		        }

		        if($questiondataarray['question_video_link']!="")
		        {
		        	$checkquestionvideo=getVideoDetails($questiondataarray['question_video_link']);

		        	if($checkquestionvideo['code']=="400")
		            {
		              $question_video_link="";
		            }
		            else{
		            	$question_video_link=$checkquestionvideo['subtopicvideourl'];
		            }
		        }
		        else{
		        	$question_video_link="";
		        }

		        if($questiondataarray['answer_explaination_img']!="")
		        {
		        	$answer_explaination_img=url('/').'/images/questions/'.$questiondataarray['answer_explaination_img'];
		        }
		        else{
		        	$answer_explaination_img='';
		        }

		        if($questiondataarray['answer_explaination_video_link']!="")
		        {
		        	$checkanswervideo=getVideoDetails($questiondataarray['answer_explaination_video_link']);

		        	if($checkanswervideo['code']=="400")
		            {
		              $answer_explaination_video_link="";
		            }
		            else{
		            	$answer_explaination_video_link=$checkanswervideo['subtopicvideourl'];
		            }
		        }
		        else{
		        	$answer_explaination_video_link="";
		        }

		        $quizquestion=html_entity_decode($questiondataarray['question']);
		        if($questiondataarray['answer_exp']!="")
				{
					$quiz_answer_exp=html_entity_decode($questiondataarray['answer_exp']);
				}
				else{
					$quiz_answer_exp="";
				}

		        $questionslist=array(
        				'course_name'=>$subject->title,
        				'topic_name'=>$coursetopicsdetaildata['category_name'],
        				'sub_topic_name'=>$coursesubtopicsdetaildata['topic_name'],
        				'quiz_name'=>$quiztopicdetaildata['title'],
        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
        				'quiz_id'=>$quiztopicdetaildata['id'],
        				'question_id'=>$questiondataarray['id'],
        				'question'=>$quizquestion, 
        				'answer_exp'=>$quiz_answer_exp,
        				'question_img'=>$question_img,
        				'question_video_link'=>$question_video_link,
        				'answer_explaination_img'=>$answer_explaination_img,
        				'answer_explaination_video_link'=>$answer_explaination_video_link,
        				'previous_question_key'=>(int)$previous_question_key,
    					'next_question_key'=>(int)$next_question_key

        			);

	       }
	       else{
	       	$questionslist=null;
	       }

	       	$data=array('code'=>'200','message'=>$questionslist);
	  		return $data;
	}

	public function updatetheoryquizresult($quiztopicdetaildata,$userid,$random_question_ids,$result_date,$subject,$coursetopicsdetaildata,$coursesubtopicsdetaildata)
	{
		try{
			$total_questions=count($random_question_ids);

			$theoryquizresult = new Theoryquizresult;
	        $theoryquizresult->topic_id=$quiztopicdetaildata['id'];

	        $theoryquizresult->user_id=$userid;

	        if($quiztopicdetaildata['timer']!="")
         	{
         		$result_timer=$quiztopicdetaildata['timer'];
         	}
         	else{
         		$result_timer=0;
         	}

	        $theoryquizresult->result_timer=$result_timer;
	        $theoryquizresult->total_questions=$total_questions;
	        
	        $theoryquizresult->result_type=1;
	        $theoryquizresult->result_date=$result_date;
	        $theoryquizresult->random_questions=json_encode($random_question_ids);
	        $theoryquizresult->save();
			}
			catch(\Exception $e){
	          $data=array('code'=>'400','message'=>'Something went wrong.');
		  		return $data;    
	       }

	       $newfinalquestionid=$random_question_ids[0];
	       $questiondata=Question::where('topic_id',$quiztopicdetaildata['id'])->where('id',$newfinalquestionid)->get()->first();

	       if($questiondata)
	       {
	       		$questiondataarray=$questiondata->toArray();

	       		$currentquestionindex = array_keys($random_question_ids,$newfinalquestionid);

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

    			if($questiondataarray['question_img']!="")
		        {
		        	$question_img=url('/').'/images/questions/'.$questiondataarray['question_img'];
		        }
		        else{
		        	$question_img='';
		        }

		        if($questiondataarray['question_video_link']!="")
		        {
		        	$checkquestionvideo=getVideoDetails($questiondataarray['question_video_link']);

		        	if($checkquestionvideo['code']=="400")
		            {
		              $question_video_link="";
		            }
		            else{
		            	$question_video_link=$checkquestionvideo['subtopicvideourl'];
		            }
		        }
		        else{
		        	$question_video_link="";
		        }

		        if($questiondataarray['answer_explaination_img']!="")
		        {
		        	$answer_explaination_img=url('/').'/images/questions/'.$questiondataarray['answer_explaination_img'];
		        }
		        else{
		        	$answer_explaination_img='';
		        }

		        if($questiondataarray['answer_explaination_video_link']!="")
		        {
		        	$checkanswervideo=getVideoDetails($questiondataarray['answer_explaination_video_link']);

		        	if($checkanswervideo['code']=="400")
		            {
		              $answer_explaination_video_link="";
		            }
		            else{
		            	$answer_explaination_video_link=$checkanswervideo['subtopicvideourl'];
		            }
		        }
		        else{
		        	$answer_explaination_video_link="";
		        }

		        $quizquestion=html_entity_decode($questiondataarray['question']);

		        if($questiondataarray['answer_exp']!="")
				{
					$quiz_answer_exp=html_entity_decode($questiondataarray['answer_exp']);
				}
				else{
					$quiz_answer_exp="";
				}

		        $questionslist=array(
        				'course_name'=>$subject->title,
        				'topic_name'=>$coursetopicsdetaildata['category_name'],
        				'sub_topic_name'=>$coursesubtopicsdetaildata['topic_name'],
        				'quiz_name'=>$quiztopicdetaildata['title'],
        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
        				'quiz_id'=>$quiztopicdetaildata['id'],
        				'question_id'=>$questiondataarray['id'],
        				'question'=>$quizquestion, 
        				'answer_exp'=>$quiz_answer_exp,
        				'question_img'=>$question_img,
        				'question_video_link'=>$question_video_link,
        				'answer_explaination_img'=>$answer_explaination_img,
        				'answer_explaination_video_link'=>$answer_explaination_video_link,
        				'previous_question_key'=>(int)$previous_question_key,
    					'next_question_key'=>(int)$next_question_key

        			);

	       }
	       else{
	       	$questionslist=null;
	       }

	       	$data=array('code'=>'200','message'=>$questionslist);
	  		return $data;
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

	        		$course_id=$quiztopicdetaildata['subject'];
	        		$topic_id=$quiztopicdetaildata['category'];
	        		$sub_topic_id=$quiztopicdetaildata['course_topic'];

	        		$result_date=date('Y-m-d');

	        		$theoryquizresultdata=Theoryquizresult::where('user_id',$user->id)->where('result_date',$result_date)->whereRaw('FIND_IN_SET(?, topic_id)', $quizid)->where('result_type','1')->orderBy('id', 'DESC')->get()->first();

	        		if($theoryquizresultdata)
	        		{
	        			$theoryquizresultarray=$theoryquizresultdata->toArray();
	        			$random_question_ids_db=$theoryquizresultarray['random_questions'];

	        			$random_questions_final_list=json_decode($random_question_ids_db,true);
	        			if(in_array($questionid, $random_questions_final_list))
	        			{
	        				$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->where('question_status','!=','0')->get()->first();
	        				if($questiondetail)
	        				{
	        					$questiondetaildata=$questiondetail->toArray();

	        					$currentquestionindex = array_keys($random_questions_final_list,$questionid);

	        			if(count($currentquestionindex) > 0)
	        			{
	        				$currentquestionindex=$currentquestionindex[0];
	        				$next_question_index=$currentquestionindex+1;
	        				$previous_question_index=$currentquestionindex-1;

	        				if(isset($random_questions_final_list[$previous_question_index]))
	        				{
	        					$previous_question_key=$random_questions_final_list[$previous_question_index];
	        				}
	        				else{
	        					$previous_question_key=0;
	        				}

	        				if(isset($random_questions_final_list[$next_question_index]))
	        				{
	        					$next_question_key=$random_questions_final_list[$next_question_index];
	        				}
	        				else{
	        					$next_question_key=0;
	        				}

	        			}
	        			else{
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

				    if($questiondetaildata['question_video_link']!="")
			        {
			        	$checkquestionvideo=getVideoDetails($questiondetaildata['question_video_link']);

			        	if($checkquestionvideo['code']=="400")
			            {
			              $question_video_link="";
			            }
			            else{
			            	$question_video_link=$checkquestionvideo['subtopicvideourl'];
			            }
			        }
			        else{
			        	$question_video_link="";
			        }

			        if($questiondetaildata['answer_explaination_img']!="")
			        {
			        	$answer_explaination_img=url('/').'/images/questions/'.$questiondetaildata['answer_explaination_img'];
			        }
			        else{
			        	$answer_explaination_img='';
			        }


			        if($questiondetaildata['answer_explaination_video_link']!="")
			        {
			        	$checkanswervideo=getVideoDetails($questiondetaildata['answer_explaination_video_link']);

			        	if($checkanswervideo['code']=="400")
			            {
			              $answer_explaination_video_link="";
			            }
			            else{
			            	$answer_explaination_video_link=$checkanswervideo['subtopicvideourl'];
			            }
			        }
			        else{
			        	$answer_explaination_video_link="";
			        }

				        $quizquestion=html_entity_decode($questiondetaildata['question']);

				        if($questiondetaildata['answer_exp']!="")
						{
							$quiz_answer_exp=html_entity_decode($questiondetaildata['answer_exp']);
						}
						else{
							$quiz_answer_exp="";
						}

				        $questiondet=array(
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondetaildata['id'],
			        				'question'=>$quizquestion, 
			        				'answer_exp'=>$quiz_answer_exp,
			        				'question_img'=>$question_img,
			        				'question_video_link'=>$question_video_link,
			        				'answer_explaination_img'=>$answer_explaination_img,
			        				'answer_explaination_video_link'=>$answer_explaination_video_link,
			        				'previous_question_key'=>(int)$previous_question_key,
		        					'next_question_key'=>(int)$next_question_key
			        			);

				        	$success['questiondet'] =  $questiondet;
        					return $this::sendResponse($success, 'Questions Details.');

	        				}
	        				else{
	        					return $this::sendError('Unauthorised Exception.', ['error'=>'No more questions']);
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

	        		$course_id=$quiztopicdetaildata['subject'];
	        		$topic_id=$quiztopicdetaildata['category'];
	        		$sub_topic_id=$quiztopicdetaildata['course_topic'];

	        		$result_date=date('Y-m-d');

	        		$theoryquizresult=Theoryquizresult::where('user_id',$user->id)->where('result_date',$result_date)->whereRaw('FIND_IN_SET(?, topic_id)', $quizid)->where('result_type','1')->orderBy('id', 'DESC')->get()->first();
	        		if($theoryquizresult)
	        		{
	        			$theoryquizresultarray=$theoryquizresult->toArray();
	        			$random_question_ids_db=$theoryquizresultarray['random_questions'];

	        			$random_questions_final_list=json_decode($random_question_ids_db,true);
	        			if(in_array($questionid, $random_questions_final_list))
	        			{
	        				$questiondetail=Question::where('topic_id',$quizid)->where('id',$questionid)->where('question_status','!=','0')->get()->first();
	        				if($questiondetail)
	        				{
	        					$questiondetaildata=$questiondetail->toArray();

	        					$currentquestionindex = array_keys($random_questions_final_list,$questionid);

	        			if(count($currentquestionindex) > 0)
	        			{
	        				$currentquestionindex=$currentquestionindex[0];
	        				$next_question_index=$currentquestionindex+1;
	        				$previous_question_index=$currentquestionindex-1;

	        				if(isset($random_questions_final_list[$previous_question_index]))
	        				{
	        					$previous_question_key=$random_questions_final_list[$previous_question_index];
	        				}
	        				else{
	        					$previous_question_key=0;
	        				}

	        				if(isset($random_questions_final_list[$next_question_index]))
	        				{
	        					$next_question_key=$random_questions_final_list[$next_question_index];
	        				}
	        				else{
	        					$next_question_key=0;
	        				}

	        			}
	        			else{
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

				    if($questiondetaildata['question_video_link']!="")
			        {
			        	$checkquestionvideo=getVideoDetails($questiondetaildata['question_video_link']);

			        	if($checkquestionvideo['code']=="400")
			            {
			              $question_video_link="";
			            }
			            else{
			            	$question_video_link=$checkquestionvideo['subtopicvideourl'];
			            }
			        }
			        else{
			        	$question_video_link="";
			        }

			        if($questiondetaildata['answer_explaination_img']!="")
			        {
			        	$answer_explaination_img=url('/').'/images/questions/'.$questiondetaildata['answer_explaination_img'];
			        }
			        else{
			        	$answer_explaination_img='';
			        }

			        if($questiondetaildata['answer_explaination_video_link']!="")
			        {
			        	$checkanswervideo=getVideoDetails($questiondetaildata['answer_explaination_video_link']);

			        	if($checkanswervideo['code']=="400")
			            {
			              $answer_explaination_video_link="";
			            }
			            else{
			            	$answer_explaination_video_link=$checkanswervideo['subtopicvideourl'];
			            }
			        }
			        else{
			        	$answer_explaination_video_link="";
			        }

				        $quizquestion=html_entity_decode($questiondetaildata['question']);

				        if($questiondetaildata['answer_exp']!="")
						{
							$quiz_answer_exp=html_entity_decode($questiondetaildata['answer_exp']);
						}
						else{
							$quiz_answer_exp="";
						}

				        $questionslist=array(
			        				'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			        				'quiz_id'=>$quiztopicdetaildata['id'],
			        				'question_id'=>$questiondetaildata['id'],
			        				'question'=>$quizquestion, 
			        				'answer_exp'=>$quiz_answer_exp,
			        				'question_img'=>$question_img,
			        				'question_video_link'=>$question_video_link,
			        				'answer_explaination_img'=>$answer_explaination_img,
			        				'answer_explaination_video_link'=>$answer_explaination_video_link,
			        				'previous_question_key'=>(int)$previous_question_key,
		        					'next_question_key'=>(int)$next_question_key
			        			);

				        	$success['questionslist'] =  $questionslist;
        					return $this::sendResponse($success, 'Questions Details.');

	        				}
	        				else{
	        					return $this::sendError('Unauthorised Exception.', ['error'=>'No more questions']);
	        				}	
	        			}
	        			else{
		        			return $this::sendError('Unauthorised Exception.', ['error'=>'No more questions.']);
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


}