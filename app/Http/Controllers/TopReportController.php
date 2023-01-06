<?php

namespace App\Http\Controllers;

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

class TopReportController extends Controller
{
     public function index(Request $request)
    {
    	if(count($request->all()) > 0)
    	{
			if($request->filter_date!="")
			{
				$filter_date=date('Y-m-d',strtotime($request->filter_date));

				$resultmarksdata=Resultmarks::where('result_marks_date',$filter_date)->orderBy('marks','desc')->limit('5')->get();

			}
			else{
				$currentdate=date('Y-m-d');
				$filter_start_date="";
				$filter_end_date="";
				$resultmarksdata=Resultmarks::where('result_marks_date',$currentdate)->orderBy('marks','desc')->limit('5')->get();
			}
    	}
    	else{
    		$currentdate=date('Y-m-d');
    		$filter_start_date="";
    		$filter_end_date="";
    		$resultmarksdata=Resultmarks::where('result_marks_date',$currentdate)->orderBy('marks','desc')->limit('5')->get();
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
	        				'sub_topic_title'=>$sub_topic_title,
	        				'topic_title'=>$topic_title,
		        			'total_questions'=>$list['total_questions'],
		        			'correct_questions'=>$correct_questions,
		        			'incorrect_questions'=>$incorrect_questions,
		        			'skip_questions'=>$skip_questions,
		        			'total_score'=>$total_score,
		        			'total_time'=>$list['result_timer'],
		        			'result_date'=>$result_marks_date
		        		);
    		}

    	}
    	else{
    		$result_data=[];
    	}

        return view('admin.top_reports.index',compact('result_data','filter_start_date','filter_end_date'));
    }

}
