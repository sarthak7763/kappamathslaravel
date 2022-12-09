<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\User;
use App\Subject;
use App\Subjectcategory;
use App\Coursetopic;
use App\Quiztopic;
use App\Question;
use App\Result;
use App\Resultmarks;

class AllReportController extends Controller
{
    public function index(Request $request)
    {
    	if(count($request->all()) > 0)
    	{
    		if($request->filter_result_date!="" && $request->filter_user!="")
    		{
    			$filter_result_date=date('Y-m-d',strtotime($request->filter_result_date));

    			$resultmarksdata=Resultmarks::where('user_id',$request->filter_user)->where('result_marks_date',$filter_result_date)->orderBy('result_marks_date','desc')->get();

    		}
    		elseif($request->filter_result_date!="" && $request->filter_user=="")
    		{
    			$filter_result_date=date('Y-m-d',strtotime($request->filter_result_date));

    			$resultmarksdata=Resultmarks::where('result_marks_date',$filter_result_date)->orderBy('result_marks_date','desc')->get();
    		}
    		elseif($request->filter_result_date=="" && $request->filter_user!="")
    		{
    			$resultmarksdata=Resultmarks::where('user_id',$request->filter_user)->orderBy('result_marks_date','desc')->get();
    		}
    		else{
    			$resultmarksdata=Resultmarks::orderBy('result_marks_date','desc')->get();
    		}
    		
    	}
    	else{
    		$resultmarksdata=Resultmarks::orderBy('result_marks_date','desc')->get();
    	}

    	if($resultmarksdata)
    	{
    		$resultmarksdataarray=$resultmarksdata->toArray();

    		$result_data=[];
    		$usernamelist=[];
    		foreach($resultmarksdataarray as $list)
    		{
    			$userid=$list['user_id'];
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
						$sub_topic_id=$coursetopic->id;
					}

					$subjectcategory = Subjectcategory::find($category);
			         if(is_null($subjectcategory)){
					   $topic_title="";
					}
					else{
						$topic_title=$subjectcategory->category_name;
						$topic_id=$subjectcategory->id;
					}

	        	}
	        	else{
	        		$sub_topic_title="";
	        		$topic_title="";
	        	}

	        	$userdet=User::where('id',$userid)->get()->first();
	        	if($userdet)
	        	{
	        		$userdetdata=$userdet->toArray();
	        		$name=$userdetdata['name'];
	        		$username=$userdetdata['username'];

	        		$userkey = array_search($userid, array_column($usernamelist, 'id'));
	        		if(!$userkey)
	        		{
	        			$usernamelist[]=array('id'=>$userid,'name'=>$userdetdata['name'],'username'=>$userdetdata['username']);
	        		}
	        		
	        	}
	        	else{
	        		$username="";
	        		$name="";
	        	}

	        	$quizcorrectresultdetail=Result::where('user_id',$userid)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','1')->get();

	        		if($quizcorrectresultdetail)
	        		{
	        			$correct_questions=$quizcorrectresultdetail->count();
	        		}
	        		else{
	        			$correct_questions=0;
	        		}

	        		$quizincorrectresultdetail=Result::where('user_id',$userid)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','2')->get();

	        		if($quizincorrectresultdetail)
	        		{
	        			$incorrect_questions=$quizincorrectresultdetail->count();
	        		}
	        		else{
	        			$incorrect_questions=0;
	        		}

	        		$quizskipresultdetail=Result::where('user_id',$userid)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','0')->get();

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

        			$result_data[]=array(
        					'name'=>$name,
        					'username'=>$username,
        					'userid'=>$userid,
        					'sub_topic_id'=>$sub_topic_id,
	        				'sub_topic_title'=>$sub_topic_title,
	        				'topic_id'=>$topic_id,
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
    		$result_data=[];
    		$usernamelist=[];
    	}

        return view('admin.all_reports.index',compact('result_data','usernamelist'));
    }



    public function index_new(Request $request)
    {
    	$resultmarksdata=Resultmarks::orderBy('result_marks_date','desc')->get();
    	if($resultmarksdata)
    	{
    		$resultmarksdataarray=$resultmarksdata->toArray();

    		$result_final_data=[];
    		$usernamelist=[];
    		$subtopiclist=[];
    		$topiclist=[];
    		foreach($resultmarksdataarray as $list)
    		{
    			$userid=$list['user_id'];
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
						$sub_topic_id=$coursetopic->id;

						$subtopickey = array_search($sub_topic_id, array_column($subtopiclist, 'id'));
		        		if(!$subtopickey)
		        		{
		        			$subtopiclist[]=array('id'=>$sub_topic_id,'name'=>$sub_topic_title);
		        		}
					}

					$subjectcategory = Subjectcategory::find($category);
			         if(is_null($subjectcategory)){
					   $topic_title="";
					}
					else{
						$topic_title=$subjectcategory->category_name;
						$topic_id=$subjectcategory->id;

						$topickey = array_search($topic_id, array_column($topiclist, 'id'));
		        		if(!$topickey)
		        		{
		        			$topiclist[]=array('id'=>$topic_id,'name'=>$topic_title);
		        		}
					}

	        	}
	        	else{
	        		$sub_topic_title="";
	        		$topic_title="";
	        	}

	        	$userdet=User::where('id',$userid)->get()->first();
	        	if($userdet)
	        	{
	        		$userdetdata=$userdet->toArray();
	        		$name=$userdetdata['name'];
	        		$username=$userdetdata['username'];

	        		$userkey = array_search($userid, array_column($usernamelist, 'id'));
	        		if(!$userkey)
	        		{
	        			$usernamelist[]=array('id'=>$userid,'name'=>$userdetdata['name'],'username'=>$userdetdata['username']);
	        		}
	        		
	        	}
	        	else{
	        		$username="";
	        		$name="";
	        	}

	        	$quizcorrectresultdetail=Result::where('user_id',$userid)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','1')->get();

	        		if($quizcorrectresultdetail)
	        		{
	        			$correct_questions=$quizcorrectresultdetail->count();
	        		}
	        		else{
	        			$correct_questions=0;
	        		}

	        		$quizincorrectresultdetail=Result::where('user_id',$userid)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','2')->get();

	        		if($quizincorrectresultdetail)
	        		{
	        			$incorrect_questions=$quizincorrectresultdetail->count();
	        		}
	        		else{
	        			$incorrect_questions=0;
	        		}

	        		$quizskipresultdetail=Result::where('user_id',$userid)->where('topic_id',$quizid)->where('result_date',$result_date)->where('answer','0')->get();

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

        			$result_final_data[]=array(
        					'name'=>$name,
        					'username'=>$username,
        					'userid'=>$userid,
        					'sub_topic_id'=>$sub_topic_id,
	        				'sub_topic_title'=>$sub_topic_title,
	        				'topic_id'=>$topic_id,
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

    		if(count($request->all()) > 0)
    		{
    			$filteruserkeys=[];
    			$filtersubtopickeys=[];
    			$filtertopickeys=[];
    			$filterresultdatekeys=[];

	    		if(isset($request->filter_user) && $request->filter_user!="")
	    		{
	    			$userfilterdet=User::where('id',$request->filter_user)->get()->first();
		        	if($userfilterdet)
		        	{
		        		$filteruserkeys = array_keys(array_column($result_final_data, 'userid'), $request->filter_user);
		        	}
	    		}

	    		if(isset($request->filter_sub_topic) && $request->filter_sub_topic!="")
	    		{
	    			$subtopicfilterdet=Coursetopic::where('id',$request->filter_sub_topic)->get()->first();
		        	if($subtopicfilterdet)
		        	{
		        		$filtersubtopickeys = array_keys(array_column($result_final_data, 'sub_topic_id'), $request->filter_sub_topic);
		        	}
	    		}

	    		if(isset($request->filter_topic) && $request->filter_topic!="")
	    		{
	    			$topicfilterdet=Subjectcategory::where('id',$request->filter_topic)->get()->first();
		        	if($topicfilterdet)
		        	{
		        		$filtertopickeys = array_keys(array_column($result_final_data, 'topic_id'), $request->filter_topic);
		        	}
	    		}

	    		if(isset($request->filter_result_date) && $request->filter_result_date!="")
	    		{
	    			$filterresultdatekeys = array_keys(array_column($result_final_data, 'result_date'), $request->filter_result_date);
	    		}

	    		$finalarraykeys=array_merge($filteruserkeys,$filtersubtopickeys,$filtertopickeys,$filterresultdatekeys);

	    		$finalarraykeys=array_unique($finalarraykeys);

	    		$result_data=[];
	    		foreach($finalarraykeys as $arr)
	    		{
	    			$result_data[]=$result_final_data[$arr];
	    		}

    		}
    		else{
    			$result_data=$result_final_data;
    		}

    	}
    	else{
    		$result_data=[];
    		$usernamelist=[];
    	}

        return view('admin.all_reports.index',compact('result_data','usernamelist','subtopiclist','topiclist'));
    }


}
