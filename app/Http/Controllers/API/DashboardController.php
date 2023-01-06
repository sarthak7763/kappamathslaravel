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
use App\Homebanner;
use Validator;
use Hash;

class DashboardController extends BaseController
{

    public function getallcourseslist()
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

	        				$coursetopicsdata=Subjectcategory::where('subject',$list['id'])->where('category_status','1')->get();
	        				if($coursetopicsdata)
	        				{
	        					$coursetopics=count($coursetopicsdata);
	        				}
	        				else{
	        					$coursetopics=0;
	        				}

	        				if($list['image']!="")
					        {
					        	$courseimage=url('/').'/images/subjects/'.$list['image'];
					        }
					        else{
					        	$courseimage=url('/').'/images/user/profile.png';
					        }

	        				$courselist[]=array(
	        					'course_id'=>$list['id'],
	        					'title'=>$list['title'],
	        					'image'=>$courseimage,
	        					'topics'=>$coursetopics,
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

	        	$homebannerdata=Homebanner::orderBy('id','DESC')->get()->first();
		          if($homebannerdata)
		          {
		            $homebannerdataarray=$homebannerdata->toArray();
		            $home_banner=array(
		              'banner_type'=>$homebannerdataarray['banner_type'],
		              'title'=>$homebannerdataarray['title'],
		              'sub_title'=>$homebannerdataarray['sub_title'],
		              'event_date'=>$homebannerdataarray['event_date'],
		              'event_link'=>$homebannerdataarray['event_link']
		            );
		          }
		          else{
		            $home_banner=[];
		          }

		          $package_info=array(
		          	'name'=>'free trial',
		          	'days'=>'10'
		          );

	        	$success['courselist'] =  $courselist;
	        	$success['home_banner'] =  $home_banner;
	        	$success['package_info'] =  $package_info;
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


    public function getcoursetopicsandsubtopicslist(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'course_id' => 'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

	        	$courseid=$request->course_id;
	        	$search=$request->search;

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        if(isset($search) && $search!="")
		        {
		        	$coursetopicsdata=Subjectcategory::where('subject',$courseid)->where('category_name', 'like', '%'.$search.'%')->where('category_status','1')->get();
		        }
		        else{
		        	$coursetopicsdata=Subjectcategory::where('subject',$courseid)->where('category_status','1')->get();
		        }
		        
		        if($coursetopicsdata)
		        {
		        	$coursetopicsdataarray=$coursetopicsdata->toArray();
		        	if($coursetopicsdataarray)
		        	{
		        		$topicslist=[];
		        		foreach($coursetopicsdataarray as $key=>$list)
		        		{
		        			$coursesubtopicsdata=Coursetopic::where('subject',$courseid)->where('category',$list['id'])->where('topic_status','1')->get();

		        			if($coursesubtopicsdata)
		        			{
		        				$coursesubtopicsdataarray=$coursesubtopicsdata->toArray();
		        				if($coursesubtopicsdataarray)
		        				{
		        					$subtopicslist=[];
		        					foreach($coursesubtopicsdataarray as $row)
		        					{
			        						$subtopicslist[]=array(
					        				'sub_topic_id'=>$row['id'],
					        				'sub_topic_name'=>$row['topic_name']
					        			);
		        					}
		        				}
		        				else{
		        					$subtopicslist=[];
		        				}
		        			}
		        			else{
		        				$subtopicslist=[];
		        			}

		        	if($list['category_image']!="")
			        {
			        	$topic_image=url('/').'/images/subjectcategory/'.$list['category_image'];
			        }
			        else{
			        	$topic_image='';
			        }

		        			$topicslist[]=array(
		        				'course_name'=>$subject->title,
		        				'topic_id'=>$list['id'],
		        				'topic_name'=>$list['category_name'],
		        				'topic_description'=>$list['category_description'],
		        				'topic_image'=>$topic_image,
		        				'sub_topics'=>$subtopicslist
		        			);
		        		}
		        	}
		        	else{
		        		$topicslist=[];
		        	}
		        }
		        else{
		        	$topicslist=[];
		        }

		        $success['course_name']=$subject->title;
		        $success['topicslist'] =  $topicslist;
                return $this::sendResponse($success, 'Sub Topics List.');
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }  
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
               }
    }


    public function getcoursetopicssubtopicsearchlist(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'search' => 'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

	        	$search=$request->search;

		        $coursetopicsdata=Subjectcategory::where('category_status','1')->where('category_name', 'like', '%'.$search.'%')->get();
		        if($coursetopicsdata)
		        {
		        	$coursetopicsdataarray=$coursetopicsdata->toArray();
		        	if($coursetopicsdataarray)
		        	{
		        		$topicslist=[];
		        		foreach($coursetopicsdataarray as $list)
		        		{
		        			if($list['category_description']!="")
		        			{
		        				$textstatus=1;
		        			}
		        			else{
		        				$textstatus=0;
		        			}

		        			$topicslist[]=array(
		        				'id'=>$list['id'],
		        				'name'=>$list['category_name'],
		        				'type'=>'topic',
		        				'video'=>0,
		        				'text'=>$textstatus
		        			);
		        		}
		        	}
		        	else{
		        		$topicslist=[];
		        	}
		        }
		        else{
		        	$topicslist=[];
		        }

		        $coursesubtopicsdata=Coursetopic::where('topic_status','1')->where('topic_name', 'like', '%'.$search.'%')->get();
		        if($coursesubtopicsdata)
		        {
		        	$coursesubtopicsdataarray=$coursesubtopicsdata->toArray();
		        	if($coursesubtopicsdataarray)
		        	{
		        		$subtopicslist=[];
		        		foreach($coursesubtopicsdataarray as $row)
		        		{
		        			if($row['topic_video_id']!="")
		        			{
		        				$videostatus=1;
		        			}
		        			else{
		        				$videostatus=0;
		        			}

		        			if($row['topic_description']!="")
		        			{
		        				$textstatus=1;
		        			}
		        			else{
		        				$textstatus=0;
		        			}
		        			
		        			$subtopicslist[]=array(
		        				'id'=>$row['id'],
		        				'name'=>$row['topic_name'],
		        				'type'=>'subtopic',
		        				'video'=>$videostatus,
		        				'text'=>$textstatus
		        			);
		        		}
		        	}
		        	else{
		        		$subtopicslist=[];
		        	}
		        }
		        else{
		        	$subtopicslist=[];
		        }

		        $finalsearch_array=array_merge($topicslist,$subtopicslist);


		        $success['search_array'] =  $finalsearch_array;
                return $this::sendResponse($success, 'Topics and Subtopics Search List.');
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }  
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
               }
    }

    public function gettopicdetailpage(Request $request)
    {
    	try{
    		$user=auth()->user();
    		if($user)
    		{

		        $validator = Validator::make($request->all(), [
		            'course_id'=>'required',
		            'topic_id'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

		        $courseid=$request->course_id;
	        	$topicid=$request->topic_id;

	        	$subject = Subject::find($courseid);
		          if(is_null($subject)){
		           return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

		        $coursetopicsdetail=Subjectcategory::where('subject',$courseid)->where('id',$topicid)->get()->first();

		        if($coursetopicsdetail)
		        {
		        	$coursetopicsdetaildata=$coursetopicsdetail->toArray();

		        	if($coursetopicsdetaildata['category_image']!="")
			        {
			        	$topic_image=url('/').'/images/subjectcategory/'.$coursetopicsdetaildata['category_image'];
			        }
			        else{
			        	$topic_image='';
			        }


			        $coursesubtopicsdata=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('topic_status','1')->get();

		        			if($coursesubtopicsdata)
		        			{
		        				$coursesubtopicsdataarray=$coursesubtopicsdata->toArray();
		        				if($coursesubtopicsdataarray)
		        				{
		        					$subtopicslist=[];
		        					foreach($coursesubtopicsdataarray as $row)
		        					{
			        						$subtopicslist[]=array(
					        				'sub_topic_id'=>$row['id'],
					        				'sub_topic_name'=>$row['topic_name']
					        			);
		        					}
		        				}
		        				else{
		        					$subtopicslist=[];
		        				}
		        			}
		        			else{
		        				$subtopicslist=[];
		        			}

		        	$topicdetail=array(
		        				'course_name'=>$subject->title,
		        				'topic_id'=>$coursetopicsdetaildata['id'],
		        				'topic_name'=>$coursetopicsdetaildata['category_name'],
		        				'topic_description'=>$coursetopicsdetaildata['category_description'],
		        				'topic_image'=>$topic_image,
		        				'sub_topics'=>$subtopicslist
		        			);

		        	$success['topicdetail'] =  $topicdetail;
                	return $this::sendResponse($success, 'Topics Detail.');
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


    public function getsubtopicdetails(Request $request)
    {
    	try{

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

			        $sort_order=$coursesubtopicsdetaildata['sort_order'];

			        $coursesubtopicsdetailnext=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('sort_order','>',$sort_order)->where('topic_status',1)->orderBy('sort_order','ASC')->get()->first();
			        if($coursesubtopicsdetailnext)
			        {
			        	$coursetopicsdetailnextdata=$coursesubtopicsdetailnext->toArray();
			        	$next_topic_key=(int)$coursetopicsdetailnextdata['id'];
			        }
			        else{
			        	$next_topic_key=0;
			        }

			        $coursesubtopicsdetailprevious=Coursetopic::where('subject',$courseid)->where('category',$topicid)->where('sort_order','<',$sort_order)->where('topic_status',1)->orderBy('sort_order','DESC')->get()->first();
			        if($coursesubtopicsdetailprevious)
			        {
			        	$coursetopicsdetailpreviousdata=$coursesubtopicsdetailprevious->toArray();
			        	$previous_topic_key=(int)$coursetopicsdetailpreviousdata['id'];
			        }
			        else{
			        	$previous_topic_key=0;
			        }

			        $quiztopicdata=Quiztopic::where('subject',$courseid)->where('category',$topicid)->where('course_topic',$subtopicid)->where('quiz_status','1')->get();
			        if($quiztopicdata)
			        {
			        	$quiztopicdataarray=$quiztopicdata->toArray();
			        	if($quiztopicdataarray)
			        	{
			        		$quizarray=[];
			        		foreach($quiztopicdataarray as $key=>$list)
			        		{
			        			$quizarray[]=array(
			        				'quiz_id'=>$list['id'],
			        				'quiz_title'=>$list['title'],
			        				'quiz_type'=>$list['quiz_type']
			        			);
			        		}
			        	}
			        	else{
			        		$quizarray=[];
			        	}
			        }
			        else{
			        	$quizarray=[];
			        }

			        if($coursesubtopicsdetaildata['topic_video_id']!="")
			        {
			        	$curlSession = curl_init();
					    curl_setopt($curlSession, CURLOPT_URL, 'https://player.vimeo.com/video/'.$coursesubtopicsdetaildata['topic_video_id'].'/config');
					    curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
					    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

				    	$jsonData = json_decode(curl_exec($curlSession));
				    	curl_close($curlSession);

					    if(isset($jsonData->message))
					    {
					    	return $this::sendError('Unauthorised Exception.', ['error'=>$jsonData->message]);
					    }
					    else{
					    	$subtopicvideourl=$jsonData->request->files->progressive[0]->url;
					    }
			        }
			        else{
			        	$subtopicvideourl="";
			        }

			        if($coursesubtopicsdetaildata['topic_image']!="")
			        {
			        	$sub_topic_image=url('/').'/images/topics/'.$coursesubtopicsdetaildata['topic_image'];
			        }
			        else{
			        	$sub_topic_image=$jsonData->video->thumbs->base;
			        }  
				    
		        		$subtopicdetail=array(
		        				'course_name'=>$subject->title,
		        				'topic_name'=>$coursetopicsdetaildata['category_name'],
		        				'sub_topic_id'=>$coursesubtopicsdetaildata['id'],
		        				'sub_topic_name'=>$coursesubtopicsdetaildata['topic_name'],
		        				'sub_topic_description'=>$coursesubtopicsdetaildata['topic_description'],
		        				'sub_topic_image'=>$sub_topic_image,
		        				'sub_topic_video_id'=>$subtopicvideourl,
		        				'quiz_topics'=>$quizarray,
		        				'previous_topic_key'=>$previous_topic_key,
		        				'next_topic_key'=>$next_topic_key

		        			);

		        	$success['subtopicdetail'] =  $subtopicdetail;
                	return $this::sendResponse($success, 'Sub Topics Detail.');

		        	}
		        	else{
		        		return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        	}

		        }
		        else{
		        	return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong.']);    
               }

    }



}