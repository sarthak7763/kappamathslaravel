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
use App\Resultmarks;
use App\Result;
use App\Theoryquizresult;
use Validator;
use Hash;

class PracticeQuizDashboardController extends BaseController
{

	public function getpracticeobjectivequiz(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'course_id' => 'required',
		            'subtopic_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }


		        $courseid=$request->course_id;
		        $subtopic_id=$request->subtopic_id;

		        $subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        if(isset($subtopic_id) && $subtopic_id!="")
		        {
		        	$newsubtopic_id=json_decode($subtopic_id);
		        	if(count($newsubtopic_id)==0)
	        		{
	        			return $this::sendError('Unauthorised.', ['error'=>'Please choose at least one sub_topic to start the test.']);
	        		}
		        }
		        else{
		        	return $this::sendError('Unauthorised.', ['error'=>'Please choose at least one sub_topic to start the test.']);
		        }

	        	$result_date=date('Y-m-d H:i:s');
	        	$result_end_date = date("Y-m-d H:i:s", strtotime('+1 day', strtotime($result_date)));

	        	$quizresultmarks=Resultmarks::where('user_id',$user->id)->whereRaw('"'.$result_date.'" between `result_marks_date` and `result_marks_end_date`')->where('subject',$courseid)->where('result_type','2')->get()->first();

	        	if($quizresultmarks==null)
	        	{
	        		$resultdata=$this->insertpracticequizresult($user->id,$courseid,$result_date,$result_end_date,$newsubtopic_id);
	        		if($resultdata)
	        		{
	        			if($resultdata['code']=="200")
	        			{
	        				$success['questionslist'] =$resultdata['message'];
                			return $this::sendResponse($success, 'Questions List.');
	        			}
	        			else{
	        				return $this::sendError('Unauthorised Exception.', ['error'=>$resultdata['message']]);
	        			}
	        		}
	        		else{
	        			return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
	        		}
	        	}
	        	else{
	        		$quizresultmarksarray=$quizresultmarks->toArray();

	        		$resultdata=$this->updatepracticequizresult($user->id,$courseid,$result_date,$result_end_date,$newsubtopic_id);
	        		if($resultdata)
	        		{
	        			if($resultdata['code']=="200")
	        			{
	        				$success['questionslist'] =$resultdata['message'];
                			return $this::sendResponse($success, 'Questions List.');
	        			}
	        			else{
	        				return $this::sendError('Unauthorised Exception.', ['error'=>$resultdata['message']]);
	        			}
	        		}
	        		else{
	        			return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
	        		}
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

	public function insertpracticequizresult($userid,$courseid,$result_date,$result_end_date,$newsubtopic_id)
	{
		$quiztopicsid=[];
		foreach ($newsubtopic_id as $key => $value) {

		$coursesubtopicsdetail=Coursetopic::where('id',$value)->get()->first();
		if($coursesubtopicsdetail)
		{
			$quiztopicdata=Quiztopic::where('course_topic',$value)->where('quiz_type','1')->where('quiz_status','1')->get()->first();
			if($quiztopicdata)
			{
				$quiztopicdataarray=$quiztopicdata->toArray();
				if($quiztopicdataarray)
				{
					$quiztopicsid[]=$quiztopicdataarray['id'];
				}
			}
		}
	  }

	  if(count($quiztopicsid) > 0)
	  {
	  	$random_question_ids_arr=[];
	    $random_question_ids=[];
	    $total_score=0;
	    $result_timer=0;
	    $total_questions=0;

	    foreach($quiztopicsid as $row)
	    {
	    	$quiztopicdetail=Quiztopic::where('id',$row)->where('quiz_type','1')->get()->first();
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

				$random_questionsdata = Question::where('topic_id',$quiztopicdetaildata['id'])->where('question_status','1')->inRandomOrder()->limit($total_questions_limit)->get();

				if($random_questionsdata)
				{
					$random_questionsdataarray=$random_questionsdata->toArray();

					foreach($random_questionsdataarray as $list)
    				{
    					$random_question_ids_arr[$quiztopicdetaildata['id']][]=$list['id'];

    					$random_question_ids[]=$list['id'];
    				}

				}

				$random_question_ids_arr =  array_map("unserialize", array_unique(array_map("serialize", $random_question_ids_arr)));

				$random_question_ids=array_unique($random_question_ids);

				if(isset($random_question_ids_arr[$quiztopicdetaildata['id']]))
				{
                	$random_questions_final_list=$random_question_ids_arr[$quiztopicdetaildata['id']];

                	$total_score+=count($random_questions_final_list)*$quiztopicdetaildata['per_q_mark'];

                	$total_questions+=count($random_questions_final_list);

                	if($quiztopicdetaildata['timer']!="")
                	{
                		$dbtimer=$quiztopicdetaildata['timer'];
                	}
                	else{
                		$dbtimer=0;
                	}

                	$result_timer+=$dbtimer;

                }
                else{
                	$total_score+=0;
                	$result_timer+=0;
                	$total_questions+=0;
                }

	    	}
	    }

	    $finalquiztopicids=implode(',',$quiztopicsid);
		$current_score=0;
		$current_quiz=1;
		$newfinalquestionid=$random_question_ids[0];

		try{
			$resultmarks = new Resultmarks;
            $resultmarks->topic_id=$finalquiztopicids;
            $resultmarks->subject=$courseid;
            $resultmarks->user_id=$userid;
            $resultmarks->marks=0;
            $resultmarks->result_timer=$result_timer;
            $resultmarks->total_questions=$total_questions;
            $resultmarks->total_marks=$total_score;
            $resultmarks->result_marks_date=$result_date;
            $resultmarks->result_marks_end_date=$result_end_date;
            $resultmarks->result_type=2;
            $resultmarks->random_question_ids=json_encode($random_question_ids_arr);
            $resultmarks->save();
            $result_id=$resultmarks->id;
		}
		catch(\Exception $e){
            $data=array('code'=>'400','message'=>'Something went wrong');
	  		return $data;    
         }

         $questiondata=Question::where('id',$newfinalquestionid)->where('question_status','1')->get()->first();
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
				'course_name'=>"",
				'topic_name'=>"",
				'sub_topic_name'=>"",
				'quiz_name'=>"",
				'quiz_type'=>1,
				'quiz_id'=>$questiondataarray['topic_id'],
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
				'total_questions'=>$total_questions,
				'current_quiz'=>$current_quiz,
				'result_id'=>$result_id,
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

         $data=array('code'=>'200','message'=>$questionslist);
	  		return $data;

	  }
	  else{
	  	$data=array('code'=>'400','message'=>'Please choose at least one sub_topic to start the test');
	  	return $data;
	  }
	}

	public function updatepracticequizresult($userid,$courseid,$result_date,$result_end_date,$newsubtopic_id)
	{

		$quiztopicsid=[];
		foreach ($newsubtopic_id as $key => $value) {

		$coursesubtopicsdetail=Coursetopic::where('id',$value)->get()->first();
		if($coursesubtopicsdetail)
		{
			$quiztopicdata=Quiztopic::where('course_topic',$value)->where('quiz_type','1')->where('quiz_status','1')->get()->first();
			if($quiztopicdata)
			{
				$quiztopicdataarray=$quiztopicdata->toArray();
				if($quiztopicdataarray)
				{
					$quiztopicsid[]=$quiztopicdataarray['id'];
				}
			}
		}
	  }

	  if(count($quiztopicsid) > 0)
	  {
	  	$random_question_ids_arr=[];
	    $random_question_ids=[];
	    $total_score=0;
	    $result_timer=0;
	    $total_questions=0;

	    foreach($quiztopicsid as $row)
	    {
	    	$quiztopicdetail=Quiztopic::where('id',$row)->where('quiz_type','1')->get()->first();
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

				$random_questionsdata = Question::where('topic_id',$quiztopicdetaildata['id'])->where('question_status','1')->inRandomOrder()->limit($total_questions_limit)->get();

				if($random_questionsdata)
				{
					$random_questionsdataarray=$random_questionsdata->toArray();

					foreach($random_questionsdataarray as $list)
    				{
    					$random_question_ids_arr[$quiztopicdetaildata['id']][]=$list['id'];

    					$random_question_ids[]=$list['id'];
    				}			
				}

				$random_question_ids_arr =  array_map("unserialize", array_unique(array_map("serialize", $random_question_ids_arr)));

				$random_question_ids=array_unique($random_question_ids);

				if(isset($random_question_ids_arr[$quiztopicdetaildata['id']]))
				{
					$random_questions_final_list=$random_question_ids_arr[$quiztopicdetaildata['id']];

					$total_score+=count($random_questions_final_list)*$quiztopicdetaildata['per_q_mark'];

					$total_questions+=count($random_questions_final_list);

					if($quiztopicdetaildata['timer']!="")
                	{
                		$dbtimer=$quiztopicdetaildata['timer'];
                	}
                	else{
                		$dbtimer=0;
                	}

					$result_timer+=$dbtimer;

				}
				else{
					$total_score+=0;
                	$result_timer+=0;
                	$total_questions+=0;
				}
	    	}
	    }

	    $finalquiztopicids=implode(',',$quiztopicsid);
	    $current_score=0;
	    $current_quiz=1;
	    $newfinalquestionid=$random_question_ids[0];

	    try{
			$resultmarks = new Resultmarks;
            $resultmarks->topic_id=$finalquiztopicids;
            $resultmarks->subject=$courseid;
            $resultmarks->user_id=$userid;
            $resultmarks->marks=0;
            $resultmarks->result_timer=$result_timer;
            $resultmarks->total_questions=$total_questions;
            $resultmarks->total_marks=$total_score;
            $resultmarks->result_marks_date=$result_date;
            $resultmarks->result_marks_end_date=$result_end_date;
            $resultmarks->result_type=2;
            $resultmarks->random_question_ids=json_encode($random_question_ids_arr);
            $resultmarks->save();
            $result_id=$resultmarks->id;
		}
		catch(\Exception $e){
            $data=array('code'=>'400','message'=>'Something went wrong.');
	  		return $data;    
         }


         $questiondata=Question::where('id',$newfinalquestionid)->where('question_status','1')->get()->first();
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
				'course_name'=>"",
				'topic_name'=>"",
				'sub_topic_name'=>"",
				'quiz_name'=>"",
				'quiz_type'=>1,
				'quiz_id'=>$questiondataarray['topic_id'],
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
				'total_questions'=>$total_questions,
				'current_quiz'=>$current_quiz,
				'result_id'=>$result_id,
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

         $data=array('code'=>'200','message'=>$questionslist);
	  		return $data;

	  }
	  else{
	  	$data=array('code'=>'400','message'=>'Please choose at least one sub_topic to start the test');
	  	return $data;
	  }
	}


	public function submitpracticeobjectivequizquestion(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required',
		            'answer'=>'required',
		            'course_id'=>'required',
		            'result_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;
	        	$user_anwer=$request->answer;
	        	$courseid=$request->course_id;
	        	$result_id=$request->result_id;

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $result_date=date('Y-m-d H:i:s');

		        $quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','1')->get()->first();
		        if($quiztopicdetail)
		        {
		        	$quiztopicdetaildata=$quiztopicdetail->toArray();

		        	$randomquizresultmarks=Resultmarks::where('id',$result_id)->where('result_type','2')->get()->first();
		        	if($randomquizresultmarks)
		        	{
		        		$randomquizresultmarksarray=$randomquizresultmarks->toArray();

		        		$random_question_ids_db=$randomquizresultmarksarray['random_question_ids'];

		        		$random_question_ids_arr=json_decode($random_question_ids_db,true);

		        		$random_questions_final_list=[];
	        			foreach($random_question_ids_arr as $arrval)
	        			{
	        				foreach($arrval as $value)
	        				{
	        					$random_questions_final_list[]=$value;
	        				}
	        			}

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

	        					$previous_question_data=Question::find($previous_question_key);
	        					if(is_null($previous_question_data)){
						           $previous_quiz_id=0;
						        }
						        else{
						        	$previous_quiz_id=$previous_question_data->topic_id;
						        }

	        				}
	        				else{
	        					$previous_question_key=0;
	        					$previous_quiz_id=0;
	        				}

	        				if(isset($random_questions_final_list[$next_question_index]))
	        				{
	        					$next_question_key=$random_questions_final_list[$next_question_index];

	        					$next_question_data=Question::find($next_question_key);
	        					if(is_null($next_question_data)){
						           $next_quiz_id=0;
						        }
						        else{
						        	$next_quiz_id=$next_question_data->topic_id;
						        }
	        				}
	        				else{
	        					$next_question_key=0;
	        					$next_quiz_id=0;
	        				}

	        			}
	        			else{
	        				$previous_question_key=0;
	        				$previous_quiz_id=0;
	        				$next_question_key=0;
	        				$next_quiz_id=0;
	        			}

	        			$total_questions=count($random_questions_final_list);

	        			$total_marks=$total_questions*$quiztopicdetaildata['per_q_mark'];

	        			if($questiondetaildata['answer']==$user_anwer)
	        			{
	        				$submitdata=$this->submitcorrectquizanswer($courseid,$user->id,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer,$previous_quiz_id,$next_quiz_id);
	        				if($submitdata['status']==400)
	        				{
	        					return $this::sendError('Unauthorised Exception.', ['error'=>$submitdata['message']]);
	        				}
	        				else{
	        					$success['questiondet'] =  $submitdata['message'];
        						return $this::sendResponse($success, 'Questions Details.');
	        				}
	        			}
	        			elseif($questiondetaildata['answer']!=$user_anwer)
	        			{
	        				$submitdata=$this->submitwrongquizanswer($courseid,$user->id,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer,$previous_quiz_id,$next_quiz_id);
	        				if($submitdata['status']==400)
	        				{
	        					return $this::sendError('Unauthorised Exception.', ['error'=>$submitdata['message']]);
	        				}
	        				else{
	        					$success['questiondet'] =  $submitdata['message'];
        						return $this::sendResponse($success, 'Questions Details.');
	        				}
	        			}
	        			else{
	        				$submitdata=$this->skipquizanswer($courseid,$user->id,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer,$previous_quiz_id,$next_quiz_id);
	        				if($submitdata['status']==400)
	        				{
	        					return $this::sendError('Unauthorised Exception.', ['error'=>$submitdata['message']]);
	        				}
	        				else{
	        					$success['questiondet'] =  $submitdata['message'];
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



	public function submitcorrectquizanswer($courseid,$userid,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer,$previous_quiz_id,$next_quiz_id)
	{
		try{
		$quizresultdetail=Result::where('user_id',$userid)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_marks_id',$randomquizresultmarksarray['id'])->get()->first();
		if($quizresultdetail)
		{
			$data=array('status'=>400,'message'=>'Answer already submitted.');
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
         	'course_id'=>$courseid,
			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			'quiz_id'=>$quiztopicdetaildata['id'],
			'question_id'=>$questiondetaildata['id'],
			'answer_exp'=>$quiz_answer_exp,
			'answer_status'=>1, //correct
			'previous_question_key'=>(int)$previous_question_key,
			'next_question_key'=>(int)$next_question_key,
			'previous_quiz_id'=>(int)$previous_quiz_id,
			'next_quiz_id'=>(int)$next_quiz_id,
			'result_id'=>(int)$randomquizresultmarksarray['id']
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
         			if(isset($dbquestion_idsarray[$quiztopicdetaildata['id']]))
         			{
         				array_push($dbquestion_idsarray[$quiztopicdetaildata['id']], $questiondetaildata['id']);

         				$finalnewdbquestion_ids=json_encode($dbquestion_idsarray);
         			}
         			else{
         				$dbquestion_idsarray[$quiztopicdetaildata['id']][]=$questiondetaildata['id'];

         				$finalnewdbquestion_ids=json_encode($dbquestion_idsarray);

         			}	
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

	public function  submitwrongquizanswer($courseid,$userid,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer,$previous_quiz_id,$next_quiz_id)
	{
		try{
		$quizresultdetail=Result::where('user_id',$userid)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_marks_id',$randomquizresultmarksarray['id'])->get()->first();

		if($quizresultdetail)
		{
			$data=array('status'=>400,'message'=>'Answer already submitted.');
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
         	'course_id'=>$courseid,
			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			'quiz_id'=>$quiztopicdetaildata['id'],
			'question_id'=>$questiondetaildata['id'],
			'answer_exp'=>$quiz_answer_exp,
			'answer_status'=>2, //correct
			'previous_question_key'=>(int)$previous_question_key,
			'next_question_key'=>(int)$next_question_key,
			'previous_quiz_id'=>(int)$previous_quiz_id,
			'next_quiz_id'=>(int)$next_quiz_id,
			'result_id'=>(int)$randomquizresultmarksarray['id']
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
         			if(isset($dbquestion_idsarray[$quiztopicdetaildata['id']]))
         			{
         				array_push($dbquestion_idsarray[$quiztopicdetaildata['id']], $questiondetaildata['id']);

         				$finalnewdbquestion_ids=json_encode($dbquestion_idsarray);
         			}
         			else{
         				$dbquestion_idsarray[$quiztopicdetaildata['id']][]=$questiondetaildata['id'];

         				$finalnewdbquestion_ids=json_encode($dbquestion_idsarray);

         			}	
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


	public function  skipquizanswer($courseid,$userid,$quiztopicdetaildata,$questiondetaildata,$result_date,$randomquizresultmarksarray,$previous_question_key,$next_question_key,$user_anwer,$previous_quiz_id,$next_quiz_id)
	{
		try{
		$quizresultdetail=Result::where('user_id',$userid)->where('topic_id',$quiztopicdetaildata['id'])->where('question_id',$questiondetaildata['id'])->where('result_marks_id',$randomquizresultmarksarray['id'])->get()->first();

		if($quizresultdetail)
		{
			$data=array('status'=>400,'message'=>'Answer already submitted.');
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
         	'course_id'=>$courseid,
			'quiz_type'=>$quiztopicdetaildata['quiz_type'],
			'quiz_id'=>$quiztopicdetaildata['id'],
			'question_id'=>$questiondetaildata['id'],
			'answer_exp'=>$quiz_answer_exp,
			'answer_status'=>2, //correct
			'previous_question_key'=>(int)$previous_question_key,
			'next_question_key'=>(int)$next_question_key,
			'previous_quiz_id'=>(int)$previous_quiz_id,
			'next_quiz_id'=>(int)$next_quiz_id,
			'result_id'=>(int)$randomquizresultmarksarray['id']
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
         			if(isset($dbquestion_idsarray[$quiztopicdetaildata['id']]))
         			{
         				array_push($dbquestion_idsarray[$quiztopicdetaildata['id']], $questiondetaildata['id']);

         				$finalnewdbquestion_ids=json_encode($dbquestion_idsarray);
         			}
         			else{
         				$dbquestion_idsarray[$quiztopicdetaildata['id']][]=$questiondetaildata['id'];

         				$finalnewdbquestion_ids=json_encode($dbquestion_idsarray);

         			}	
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


	public function getpracticeobjectivequizquestionexplaination(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required',
		            'course_id'=>'required',
		            'result_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;
	        	$courseid=$request->course_id;
	        	$result_id=$request->result_id;
	        	$result_date=date('Y-m-d H:i:s');

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

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

	        		$randomquizresultmarks=Resultmarks::where('id',$result_id)->where('result_type','2')->get()->first();
	        		if($randomquizresultmarks)
	        		{
	        			$randomquizresultmarksarray=$randomquizresultmarks->toArray();

	        			$random_question_ids_db=$randomquizresultmarksarray['random_question_ids'];

		        		$random_question_ids_arr=json_decode($random_question_ids_db,true);

		        		$random_questions_final_list=[];
	        			foreach($random_question_ids_arr as $arrval)
	        			{
	        				foreach($arrval as $value)
	        				{
	        					$random_questions_final_list[]=$value;
	        				}
	        			}

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

	        				$current_quiz=$currentquestionindex+1;

	        				$next_question_index=$currentquestionindex+1;

	        				$previous_question_index=$currentquestionindex-1;

        					if(isset($random_questions_final_list[$previous_question_index]))
        					{
        						$previous_question_key=$random_questions_final_list[$previous_question_index];

	        					$previous_question_data=Question::find($previous_question_key);
	        					if(is_null($previous_question_data)){
						           $previous_quiz_id=0;
						        }
						        else{
						        	$previous_quiz_id=$previous_question_data->topic_id;
						        }
        					}
        					else{
        						$previous_question_key=0;
	        					$previous_quiz_id=0;
        					}

        					if(isset($random_questions_final_list[$next_question_index]))
        					{
        						$next_question_key=$random_questions_final_list[$next_question_index];

	        					$next_question_data=Question::find($next_question_key);
	        					if(is_null($next_question_data)){
						           $next_quiz_id=0;
						        }
						        else{
						        	$next_quiz_id=$next_question_data->topic_id;
						        }
        					}
        					else{
        						$next_question_key=0;
	        					$next_quiz_id=0;
        					}

	        				}
	        				else{
	        					$current_quiz=0;
	        					$previous_question_key=0;
	        					$previous_quiz_id=0;
	        					$next_question_key=0;
	        					$next_quiz_id=0;
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

	        			$questiondet=array(
		        		 			'course_id'=>$courseid,
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
			        				'previous_quiz_id'=>(int)$previous_quiz_id,
		        					'next_question_key'=>(int)$next_question_key,
		        					'next_quiz_id'=>(int)$next_quiz_id,
		        					'current_score'=>$current_score,
			        				'total_score'=>$total_marks,
			        				'total_questions'=>$total_questions,
			        				'current_quiz'=>$current_quiz,
			        				'result_id'=>(int)$result_id
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

	public function getpracticeobjectivequizquestiondetails(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required',
		            'course_id'=>'required',
		            'result_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;
	        	$courseid=$request->course_id;
	        	$result_id=$request->result_id;
	        	$result_date=date('Y-m-d H:i:s');

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','1')->get()->first();
		        if($quiztopicdetail)
		        {
		        	$quiztopicdetaildata=$quiztopicdetail->toArray();
	        		$course_id=$quiztopicdetaildata['subject'];
	        		$topic_id=$quiztopicdetaildata['category'];
	        		$sub_topic_id=$quiztopicdetaildata['course_topic'];

	        		$randomquizresultmarks=Resultmarks::where('id',$result_id)->where('result_type','2')->get()->first();
	        		if($randomquizresultmarks)
	        		{
	        			$randomquizresultmarksarray=$randomquizresultmarks->toArray();

	        			$random_question_ids_db=$randomquizresultmarksarray['random_question_ids'];

		        		$random_question_ids_arr=json_decode($random_question_ids_db,true);

		        		$random_questions_final_list=[];
	        			foreach($random_question_ids_arr as $arrval)
	        			{
	        				foreach($arrval as $value)
	        				{
	        					$random_questions_final_list[]=$value;
	        				}
	        			}

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

	        					$previous_question_data=Question::find($previous_question_key);
	        					if(is_null($previous_question_data)){
						           $previous_quiz_id=0;
						        }
						        else{
						        	$previous_quiz_id=$previous_question_data->topic_id;
						        }

	        				}
	        				else{
	        					$previous_question_key=0;
	        					$previous_quiz_id=0;
	        				}

	        				if(isset($random_questions_final_list[$next_question_index]))
	        				{
	        					$next_question_key=$random_questions_final_list[$next_question_index];

	        					$next_question_data=Question::find($next_question_key);
	        					if(is_null($next_question_data)){
						           $next_quiz_id=0;
						        }
						        else{
						        	$next_quiz_id=$next_question_data->topic_id;
						        }
	        				}
	        				else{
	        					$next_question_key=0;
	        					$next_quiz_id=0;
	        				}

	        			}
	        			else{
	        				$current_quiz=0;
	        				$previous_question_key=0;
	        				$previous_quiz_id=0;
	        				$next_question_key=0;
	        				$next_quiz_id=0;
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

	        			$questionslist=array(
		        		 			'course_id'=>$courseid,
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
			        				'previous_quiz_id'=>(int)$previous_quiz_id,
		        					'next_question_key'=>(int)$next_question_key,
		        					'next_quiz_id'=>(int)$next_quiz_id,
		        					'current_score'=>$current_score,
			        				'total_score'=>$total_marks,
			        				'total_questions'=>$total_questions,
			        				'current_quiz'=>$current_quiz,
			        				'result_id'=>(int)$result_id,
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


	public function deleteresultanswer(Request $request)
	{
		$user=auth()->user();
		$quizid=$request->quiz_id;
	    $questionid=$request->question_id;
	    $courseid=$request->course_id;
	    $result_id=$request->result_id;

	    Result::where('topic_id',$quizid)->where('user_id',$user->id)->where('question_id',$questionid)->where('result_marks_id',$result_id)->delete();

	}

	public function getpracticetheoryquizquestions(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$validator = Validator::make($request->all(), [
		            'course_id' => 'required',
		            'subtopic_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $courseid=$request->course_id;
		        $subtopic_id=$request->subtopic_id;

		        $subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        if(isset($subtopic_id) && $subtopic_id!="")
		        {
		        	$theorynewsubtopic_id=json_decode($subtopic_id);
		        	if(count($theorynewsubtopic_id)==0)
	        		{
	        			return $this::sendError('Unauthorised.', ['error'=>'Please choose at least one sub_topic to start the test.']);
	        		}
		        }
		        else{
		        	return $this::sendError('Unauthorised.', ['error'=>'Please choose at least one sub_topic to start the test.']);
		        }

		        $result_date=date('Y-m-d');

	        	$theoryquizresultdata=Theoryquizresult::where('user_id',$user->id)->where('result_type','2')->where('result_date',$result_date)->get()->first();

	        	if($theoryquizresultdata==null)
	        	{
	        		$theoryresultdata=$this->insertpracticetheoryquizresult($user->id,$courseid,$result_date,$theorynewsubtopic_id);

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
	        		$theoryquizresultdataarray=$theoryquizresultdata->toArray();

	        		$theoryresultdata=$this->updatepracticetheoryquizresult($user->id,$courseid,$result_date,$theorynewsubtopic_id);

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
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
	        }
	    }
	    catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
               }
	}

	public function insertpracticetheoryquizresult($userid,$courseid,$result_date,$theorynewsubtopic_id)
	{
		$theoryquiztopicsid=[];
		foreach ($theorynewsubtopic_id as $key => $value) {

			$coursesubtopicsdetail=Coursetopic::where('id',$value)->get()->first();
			if($coursesubtopicsdetail)
			{
				$quiztopicdata=Quiztopic::where('course_topic',$value)->where('quiz_type','2')->where('quiz_status','1')->get()->first();
    			if($quiztopicdata)
    			{
    				$quiztopicdataarray=$quiztopicdata->toArray();
    				if($quiztopicdataarray)
    				{
    					$theoryquiztopicsid[]=$quiztopicdataarray['id'];
    				}
    			}
			}
		}

		if(count($theoryquiztopicsid) > 0)
		{
			$random_question_ids_arr=[];
	    	$random_question_ids=[];
	    	$result_timer=0;
	    	$total_questions=0;

	    	foreach($theoryquiztopicsid as $row)
	    	{
	    		$quiztopicdetail=Quiztopic::where('id',$row)->where('quiz_type','2')->get()->first();
	    		if($quiztopicdetail)
	    		{
	    			$quiztopicdetaildata=$quiztopicdetail->toArray();

	    			if($quiztopicdetaildata['timer']!="")
		        	{
		        		$dbtimer=$quiztopicdetaildata['timer'];
		        	}
		        	else{
		        		$dbtimer=0;
		        	}

	    			$result_timer+=$dbtimer;

	    			$quiztopicdetaildata=$quiztopicdetail->toArray();

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

						foreach($random_questionsdataarray as $list)
        				{
        					$random_question_ids[]=$list['id'];
        				}		
					}


	    		}
	    	}

	    	if(count($random_question_ids) > 0)
	    	{

				$random_question_ids=array_unique($random_question_ids);

	    		$finalquiztopicids=implode(',',$theoryquiztopicsid);
	        	$newfinalquestionid=$random_question_ids[0];

	        	$total_questions=count($random_question_ids);

	        	try{
				$theoryquizresult = new Theoryquizresult;
	            $theoryquizresult->topic_id=$finalquiztopicids;
	            $theoryquizresult->user_id=$userid;
	            $theoryquizresult->result_timer=$result_timer;
	            $theoryquizresult->total_questions=$total_questions;
	            $theoryquizresult->result_date=$result_date;
	            $theoryquizresult->result_type=2;
	            $theoryquizresult->random_questions=json_encode($random_question_ids);
	            $theoryquizresult->save();
	            $result_id=$theoryquizresult->id;
			}
			catch(\Exception $e){
	            $data=array('code'=>'400','message'=>'Something went wrong.');
		  		return $data;    
	         }

	         $questiondata=Question::where('id',$newfinalquestionid)->where('question_status','1')->get()->first();
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

    					$previous_question_data=Question::find($previous_question_key);
    					if(is_null($previous_question_data)){
				           $previous_quiz_id=0;
				        }
				        else{
				        	$previous_quiz_id=$previous_question_data->topic_id;
				        }

    				}
    				else{
    					$previous_quiz_id=0;
    					$previous_question_key=0;
    				}

    				if(isset($random_question_ids[$next_question_index]))
    				{
    					$next_question_key=$random_question_ids[$next_question_index];

    					$next_question_data=Question::find($next_question_key);
    					if(is_null($next_question_data)){
				           $next_quiz_id=0;
				        }
				        else{
				        	$next_quiz_id=$next_question_data->topic_id;
				        }
    				}
    				else{
    					$next_quiz_id=0;
    					$next_question_key=0;
    				}

    			}
    			else{
    				$previous_quiz_id=0;
    				$previous_question_key=0;
    				$next_question_key=0;
    				$next_quiz_id=0;
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
	        		'course_id'=>(int)$courseid,
					'course_name'=>"",
					'topic_name'=>"",
					'sub_topic_name'=>"",
					'quiz_name'=>"",
					'quiz_type'=>2,
					'quiz_id'=>$questiondataarray['topic_id'],
					'question_id'=>$questiondataarray['id'],
					'question'=>$quizquestion, 
					'answer_exp'=>$quiz_answer_exp,
					'question_img'=>$question_img,
					'question_video_link'=>$question_video_link,
					'answer_explaination_img'=>$answer_explaination_img,
					'answer_explaination_video_link'=>$answer_explaination_video_link,
					'previous_question_key'=>(int)$previous_question_key,
					'previous_quiz_id'=>(int)$previous_quiz_id,
		        	'next_question_key'=>(int)$next_question_key,
		        	'next_quiz_id'=>(int)$next_quiz_id,
					'result_id'=>(int)$result_id,
				);

	         }
	         else{
	         	$questionslist=[];
	         }

         	$data=array('code'=>'200','message'=>$questionslist);
	  		return $data;

	    	}
	    	else{
	    		$data=array('code'=>'400','message'=>'No more questions available');
	  			return $data;
	    	}
		}
		else{
			$data=array('code'=>'400','message'=>'Please choose at least one sub_topic to start the test');
	  		return $data;
		}
	}


	public function updatepracticetheoryquizresult($userid,$courseid,$result_date,$theorynewsubtopic_id)
	{
		$theoryquiztopicsid=[];
		foreach ($theorynewsubtopic_id as $key => $value) {

			$coursesubtopicsdetail=Coursetopic::where('id',$value)->get()->first();
			if($coursesubtopicsdetail)
			{
				$quiztopicdata=Quiztopic::where('course_topic',$value)->where('quiz_type','2')->where('quiz_status','1')->get()->first();
    			if($quiztopicdata)
    			{
    				$quiztopicdataarray=$quiztopicdata->toArray();
    				if($quiztopicdataarray)
    				{
    					$theoryquiztopicsid[]=$quiztopicdataarray['id'];
    				}
    			}
			}
		}

		if(count($theoryquiztopicsid) > 0)
		{
			$random_question_ids_arr=[];
	    	$random_question_ids=[];
	    	$result_timer=0;
	    	$total_questions=0;

	    	foreach($theoryquiztopicsid as $row)
	    	{
	    		$quiztopicdetail=Quiztopic::where('id',$row)->where('quiz_type','2')->get()->first();
	    		if($quiztopicdetail)
	    		{
	    			$quiztopicdetaildata=$quiztopicdetail->toArray();

	    			if($quiztopicdetaildata['timer']!="")
		        	{
		        		$dbtimer=$quiztopicdetaildata['timer'];
		        	}
		        	else{
		        		$dbtimer=0;
		        	}

	    			$result_timer+=$dbtimer;

	    			$quiztopicdetaildata=$quiztopicdetail->toArray();

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

						foreach($random_questionsdataarray as $list)
        				{
        					$random_question_ids[]=$list['id'];
        				}		
					}


	    		}
	    	}

	    	if(count($random_question_ids) > 0)
	    	{
	    		$random_question_ids=array_unique($random_question_ids);
	    		
	    		$finalquiztopicids=implode(',',$theoryquiztopicsid);
	        	$newfinalquestionid=$random_question_ids[0];

	        	$total_questions=count($random_question_ids);

	        	try{
				$theoryquizresult = new Theoryquizresult;
	            $theoryquizresult->topic_id=$finalquiztopicids;
	            $theoryquizresult->user_id=$userid;
	            $theoryquizresult->result_timer=$result_timer;
	            $theoryquizresult->total_questions=$total_questions;
	            $theoryquizresult->result_date=$result_date;
	            $theoryquizresult->result_type=2;
	            $theoryquizresult->random_questions=json_encode($random_question_ids);
	            $theoryquizresult->save();
	            $result_id=$theoryquizresult->id;
			}
			catch(\Exception $e){
	            $data=array('code'=>'400','message'=>'Something went wrong');
		  		return $data;    
	         }

	         $questiondata=Question::where('id',$newfinalquestionid)->where('question_status','1')->get()->first();
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

    					$previous_question_data=Question::find($previous_question_key);
    					if(is_null($previous_question_data)){
				           $previous_quiz_id=0;
				        }
				        else{
				        	$previous_quiz_id=$previous_question_data->topic_id;
				        }

    				}
    				else{
    					$previous_quiz_id=0;
    					$previous_question_key=0;
    				}

    				if(isset($random_question_ids[$next_question_index]))
    				{
    					$next_question_key=$random_question_ids[$next_question_index];

    					$next_question_data=Question::find($next_question_key);
    					if(is_null($next_question_data)){
				           $next_quiz_id=0;
				        }
				        else{
				        	$next_quiz_id=$next_question_data->topic_id;
				        }
    				}
    				else{
    					$next_quiz_id=0;
    					$next_question_key=0;
    				}

    			}
    			else{
    				$previous_quiz_id=0;
    				$previous_question_key=0;
    				$next_question_key=0;
    				$next_quiz_id=0;
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
	        		'course_id'=>(int)$courseid,
					'course_name'=>"",
					'topic_name'=>"",
					'sub_topic_name'=>"",
					'quiz_name'=>"",
					'quiz_type'=>2,
					'quiz_id'=>$questiondataarray['topic_id'],
					'question_id'=>$questiondataarray['id'],
					'question'=>$quizquestion, 
					'answer_exp'=>$quiz_answer_exp,
					'question_img'=>$question_img,
					'question_video_link'=>$question_video_link,
					'answer_explaination_img'=>$answer_explaination_img,
					'answer_explaination_video_link'=>$answer_explaination_video_link,
					'previous_question_key'=>(int)$previous_question_key,
					'previous_quiz_id'=>(int)$previous_quiz_id,
		        	'next_question_key'=>(int)$next_question_key,
		        	'next_quiz_id'=>(int)$next_quiz_id,
					'result_id'=>(int)$result_id,
				);

	         }
	         else{
	         	$questionslist=[];
	         }

         	$data=array('code'=>'200','message'=>$questionslist);
	  		return $data;

	    	}
	    	else{
	    		$data=array('code'=>'400','message'=>'No more questions available');
	  			return $data;
	    	}
		}
		else{
			$data=array('code'=>'400','message'=>'Please choose at least one sub_topic to start the test');
	  		return $data;
		}
	}

	public function getpracticetheoryquizquestionexplaination(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required',
		            'result_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;
	        	$result_date=date('Y-m-d');
	        	$result_id=$request->result_id;

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','2')->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();
	        		$course_id=$quiztopicdetaildata['subject'];
	        		$topic_id=$quiztopicdetaildata['category'];
	        		$sub_topic_id=$quiztopicdetaildata['course_topic'];

	        		$theoryquizresultdata=Theoryquizresult::where('id',$result_id)->where('result_type','2')->get()->first();
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

	        					$previous_question_data=Question::find($previous_question_key);
	        					if(is_null($previous_question_data)){
						           $previous_quiz_id=0;
						        }
						        else{
						        	$previous_quiz_id=$previous_question_data->topic_id;
						        }

	        				}
	        				else{
	        					$previous_question_key=0;
	        					$previous_quiz_id=0;
	        				}


	        				if(isset($random_questions_final_list[$next_question_index]))
	        				{
	        					$next_question_key=$random_questions_final_list[$next_question_index];

	        					$next_question_data=Question::find($next_question_key);
	        					if(is_null($next_question_data)){
						           $next_quiz_id=0;
						        }
						        else{
						        	$next_quiz_id=$next_question_data->topic_id;
						        }
	        				}
	        				else{
	        					$next_question_key=0;
	        					$next_quiz_id=0;
	        				}

        					}
        					else{
        						$previous_question_key=0;
        						$previous_quiz_id=0;
        						$next_question_key=0;
        						$next_quiz_id=0;
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
				        			'course_id'=>(int)$course_id,
				        			'course_name'=>"",
									'topic_name'=>"",
									'sub_topic_name'=>"",
									'quiz_name'=>"",
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
			        				'previous_quiz_id'=>(int)$previous_quiz_id,
		        					'next_question_key'=>(int)$next_question_key,
		        					'next_quiz_id'=>(int)$next_quiz_id,
		        					'result_id'=>(int)$result_id
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
	        			return $this::sendError('Unauthorised.', ['error'=>'Something went wrong.']);
	        		}

	        	}
	        	else{
	        		return $this::sendError('Unauthorised.', ['error'=>'Something went wrong.']);
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


	public function getpracticetheoryquizquestiondetails(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$validator = Validator::make($request->all(), [
		            'quiz_id'=>'required',
		            'question_id'=>'required',
		            'result_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $quizid=$request->quiz_id;
	        	$questionid=$request->question_id;
	        	$result_date=date('Y-m-d');
	        	$result_id=$request->result_id;

	        	$quiztopicdetail=Quiztopic::where('id',$quizid)->where('quiz_type','2')->get()->first();
	        	if($quiztopicdetail)
	        	{
	        		$quiztopicdetaildata=$quiztopicdetail->toArray();
	        		$course_id=$quiztopicdetaildata['subject'];
	        		$topic_id=$quiztopicdetaildata['category'];
	        		$sub_topic_id=$quiztopicdetaildata['course_topic'];

	        		$theoryquizresultdata=Theoryquizresult::where('id',$result_id)->where('result_type','2')->get()->first();
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

	        					$previous_question_data=Question::find($previous_question_key);
	        					if(is_null($previous_question_data)){
						           $previous_quiz_id=0;
						        }
						        else{
						        	$previous_quiz_id=$previous_question_data->topic_id;
						        }

	        				}
	        				else{
	        					$previous_question_key=0;
	        					$previous_quiz_id=0;
	        				}


	        				if(isset($random_questions_final_list[$next_question_index]))
	        				{
	        					$next_question_key=$random_questions_final_list[$next_question_index];

	        					$next_question_data=Question::find($next_question_key);
	        					if(is_null($next_question_data)){
						           $next_quiz_id=0;
						        }
						        else{
						        	$next_quiz_id=$next_question_data->topic_id;
						        }
	        				}
	        				else{
	        					$next_question_key=0;
	        					$next_quiz_id=0;
	        				}

        					}
        					else{
        						$previous_question_key=0;
        						$previous_quiz_id=0;
        						$next_question_key=0;
        						$next_quiz_id=0;
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
				        			'course_id'=>(int)$course_id,
				        			'course_name'=>"",
									'topic_name'=>"",
									'sub_topic_name'=>"",
									'quiz_name'=>"",
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
			        				'previous_quiz_id'=>(int)$previous_quiz_id,
		        					'next_question_key'=>(int)$next_question_key,
		        					'next_quiz_id'=>(int)$next_quiz_id,
		        					'result_id'=>(int)$result_id
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
	        			return $this::sendError('Unauthorised.', ['error'=>'Something went wrong.']);
	        		}

	        	}
	        	else{
	        		return $this::sendError('Unauthorised.', ['error'=>'Something went wrong.']);
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