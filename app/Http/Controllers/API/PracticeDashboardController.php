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

class PracticeDashboardController extends BaseController
{

	public function getpracticedashboardinfo()
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

	        			$courseobjectivetopics=count($course_objective_topicsarray);

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

	        			$coursetheorytopics=count($course_theory_topicsarray);

	        				$courselist[]=array(
	        					'course_id'=>base64_encode($list['id']),
	        					'title'=>$list['title'],
	        					'objective_topics'=>$courseobjectivetopics,
	        					'theory_topics'=>$coursetheorytopics,
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

	public function getcoursetopicsbyquiztype()
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
	        	$courseid=base64_decode($courseid);

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
							$topicdetail=Subjectcategory::where('subject',$courseid)->where('id',$list)->where('category_status','1')->get();

						if($topicdetail){

							$topicdetaildata=$topicdetail->toArray();
					           $course_topics[]=array(
					        		'topic_id'=>base64_encode($topicdetaildata['id']),
					        		'topic_name'=>$topicdetaildata['category_name']
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
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }

	}

	public function getcoursesubtopicslistbyquiztype()
	{
		try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'course_id'=>'required',
		            'topics_id'=>'required'
		        ]);
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