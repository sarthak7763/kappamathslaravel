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
use Validator;
use Hash;

class PracticeDashboardController extends BaseController
{
	public function getpracticedashboardinfo(Request $request)
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
	        			$quiz_topic_array=[];
	        			foreach($subjectdataarray as $list)
	        			{

	        				$result_date=date('Y-m-d H:i:s');
	        				$randomquizresultmarks=Resultmarks::where('user_id',$user->id)->whereRaw('"'.$result_date.'" between `result_marks_date` and `result_marks_end_date`')->where('subject',$list['id'])->where('result_type','2')->get()->first();

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
	        				
	        				$quiz_topic_array=[];
	        				
	        				$courseobjectivetopicsdata=Quiztopic::where('subject',$list['id'])->where('quiz_type','1')->where('quiz_status','1')->get();

	        				if($courseobjectivetopicsdata)
	        				{
	        					$courseobjectivetopicsdataarray=$courseobjectivetopicsdata->toArray();

	        					$courseobjectivetopicsarray=[];
	        					foreach($courseobjectivetopicsdataarray as $row)
	        					{
	        						$courseobjectivetopicsarray[]=$row['category'];
	        					}

		        				if(count($courseobjectivetopicsarray) > 0)
	        					{
	        						$course_objective_topicsarray = array_unique($courseobjectivetopicsarray);
	        					}
	        					else{
	        						$course_objective_topicsarray=[];
	        					}

	        				}
	        				else{
	        					$course_objective_topicsarray=[];
	        				}

	        				$quiz_topic_array[]=array(
	        					'topics'=>count($course_objective_topicsarray),
	        					'image'=>'',
	        					'quz_type'=>1,
	        					'type'=>'Objective'
	        				);

	        				$coursetheorytopicsdata=Quiztopic::where('subject',$list['id'])->where('quiz_type','2')->where('quiz_status','1')->get();
	        				if($coursetheorytopicsdata)
	        				{
	        					$coursetheorytopicsdataarray=$coursetheorytopicsdata->toArray();

	        					$coursetheorytopicsarray=[];
	        					foreach($coursetheorytopicsdataarray as $row)
	        					{
	        						$coursetheorytopicsarray[]=$row['category'];
	        					}

	        					if(count($coursetheorytopicsarray) > 0)
	        					{
	        						$course_theory_topicsarray = array_unique($coursetheorytopicsarray);
	        					}
	        					else{
	        						$course_theory_topicsarray=[];
	        					}

	        				}
	        				else{
	        					$course_theory_topicsarray=[];
	        				}

	        				$quiz_topic_array[]=array(
	        					'topics'=>count($course_theory_topicsarray),
	        					'image'=>'',
	        					'quz_type'=>2,
	        					'type'=>'Theory'
	        				);


	        				$courselist[]=array(
	        					'course_id'=>$list['id'],
	        					'title'=>$list['title'],
	        					'quiz_topic_array'=>$quiz_topic_array,
	        					'description'=>$list['description'],
	        					'quiz_retake_time'=>$quiz_retake_time,
	        					'quiz_complete_status'=>$quiz_complete_status
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

	public function getcoursetopicsbyquiztype(Request $request)
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'course_id'=>'required',
		            'quiz_type'=>'required'
		        ]);

	        	$courseid=$request->course_id;
	        	$quiz_type=$request->quiz_type;

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $coursetopicsdata=Quiztopic::where('subject',$courseid)->where('quiz_type',$quiz_type)->where('quiz_status','1')->get();

		        if($coursetopicsdata)
				{
					$coursetopicsdataarray=$coursetopicsdata->toArray();

					$coursetopicsarray=[];
					foreach($coursetopicsdataarray as $row)
					{
						$coursetopicsarray[]=$row['category'];
					}

					if(count($coursetopicsarray) > 0)
					{
						$course_topicsarray = array_unique($coursetopicsarray);

						$course_topics=[];
						foreach($course_topicsarray  as $list)
						{
							$topicdetail=Subjectcategory::where('subject',$courseid)->where('id',$list)->where('category_status','1')->get()->first();

						if($topicdetail){

							$topicdetaildata=$topicdetail->toArray();
							
							$coursetopicsdata=Coursetopic::where('subject',$courseid)->where('category',$list)->where('topic_status','1')->get();
							if($coursetopicsdata)
							{
								$coursetopicsdataarray=$coursetopicsdata->toArray();
								if($coursetopicsdataarray)
								{
									$subtopiclist=[];
									foreach($coursetopicsdataarray as $row)
									{
										$subtopiclist[]=array(
											'subtopic_id'=>$row['id'],
											'subtopic_name'=>$row['topic_name']
										);
									}
								}
								else{
									$subtopiclist=[];
								}
							}
							else{
								$subtopiclist=[];
							}

							

					           $course_topics[]=array(
					        		'topic_id'=>$topicdetaildata['id'],
					        		'topic_name'=>$topicdetaildata['category_name'],
					        		'subtopic'=>$subtopiclist,
					        		'quiz_type'=>$quiz_type
					        	);
					        }
						}
					}
					else{
						$course_topics=[];
					}
				}
				else{
					$course_topics=[];
				}

				$success['course_topics'] =  $course_topics;
                return $this::sendResponse($success, 'Course Topics List.');

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