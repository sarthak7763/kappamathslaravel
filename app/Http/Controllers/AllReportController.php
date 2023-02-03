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
        try{

        if(count($request->all()) > 0)
        {
        	if(!empty(array_filter($request->filter_result_date)) && $request->filter_user!="")
        	{
        		$clear_filter=1;
        		$filter_user=$request->filter_user;
        		$current_date=date('Y-m-d');

        		$resultmarksdata=Resultmarks::where('user_id',$request->filter_user);

        		if(isset($request->filter_result_date[0]))
        		{
        			$filter_start_date=date('Y-m-d',strtotime($request->filter_result_date[0]));

        			if($filter_start_date > $current_date)
        			{
        				return back()->with('error','Alert! cannot select future dates.');
        			}
        			else{
        				$resultmarksdata=$resultmarksdata->where('result_marks_date','>=',$filter_start_date);
        			}
        		}

        		if(isset($request->filter_result_date[1]))
        		{
        			$filter_end_date=date('Y-m-d',strtotime($request->filter_result_date[1]));

                    if(isset($request->filter_result_date[0]))
                    {
                        if($filter_end_date >= $filter_start_date)
                        {
                            $resultmarksdata=$resultmarksdata->where('result_marks_date','<=',$filter_end_date);
                        }
                        else{
                            return back()->with('error','End date must be greater than or equal to start date.');
                        }
                    }
                    else{
                        $resultmarksdata=$resultmarksdata->where('result_marks_date','<=',$filter_end_date);
                    }
        		}

        		$resultmarksdata=$resultmarksdata->orderBy('result_marks_date','desc')->get();

        	}
        	elseif(!empty(array_filter($request->filter_result_date)) && $request->filter_user=="")
        	{
        		$clear_filter=1;
        		$filter_user="";
        		$current_date=date('Y-m-d');

        		$resultmarksdata=Resultmarks::where('result_type','1');

        		if(isset($request->filter_result_date[0]))
        		{
        			$filter_start_date=date('Y-m-d',strtotime($request->filter_result_date[0]));

        			if($filter_start_date > $current_date)
        			{
        				return back()->with('error','Alert! cannot select future dates.');
        			}
        			else{
        				$resultmarksdata=$resultmarksdata->where('result_marks_date','>=',$filter_start_date);
        			}

        		}

        		if(isset($request->filter_result_date[1]))
        		{
        			$filter_end_date=date('Y-m-d',strtotime($request->filter_result_date[1]));

        			if(isset($request->filter_result_date[0]))
        			{
        				if($filter_end_date >= $filter_start_date)
        				{
        					$resultmarksdata=$resultmarksdata->where('result_marks_date','<=',$filter_end_date);
        				}
        				else{
        					return back()->with('error','End date must be greater than or equal to start date.');
        				}
        			}
        			else{
        				$resultmarksdata=$resultmarksdata->where('result_marks_date','<=',$filter_end_date);
        			}	
        		}

        		$resultmarksdata=$resultmarksdata->orderBy('result_marks_date','desc')->get();

        	}
        	elseif(empty(array_filter($request->filter_result_date)) && $request->filter_user!="")
        	{
        		$clear_filter=1;
        		$filter_user=$request->filter_user;
    			$filter_start_date="";
    			$filter_end_date="";

    			$resultmarksdata=Resultmarks::where('user_id',$request->filter_user)->orderBy('result_marks_date','desc')->get();
        	}
        	else{
        		$clear_filter=0;
        		$filter_user="";
        		$filter_start_date="";
        		$filter_end_date="";
        		$resultmarksdata=Resultmarks::orderBy('result_marks_date','desc')->get();
        	}
        } 
        else{
        	$clear_filter=0;
        	$filter_user="";
        	$filter_start_date="";
        	$filter_end_date="";
        	$resultmarksdata=Resultmarks::orderBy('result_marks_date','desc')->get();
        }
        
    	if($resultmarksdata)
    	{
    		$resultmarksdataarray=$resultmarksdata->toArray();

    		$result_data=[];
    		$finalusernamelist=[];
    		foreach($resultmarksdataarray as $list)
    		{
    			$userid=$list['user_id'];
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

          $random_question_idsdb=$list['random_question_ids'];
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
                    $quizcorrectresultdetail=Result::where('user_id',$userid)->where('result_marks_id',$list['id'])->where('answer','1')->get();

                    if($quizcorrectresultdetail)
                    {
                      $correct_questions=$quizcorrectresultdetail->count();
                    }
                    else{
                      $correct_questions=0;
                    }

                    $quizincorrectresultdetail=Result::where('user_id',$userid)->where('result_marks_id',$list['id'])->where('answer','2')->get();

                    if($quizincorrectresultdetail)
                    {
                      $incorrect_questions=$quizincorrectresultdetail->count();
                    }
                    else{
                      $incorrect_questions=0;
                    }

                    $quizskipresultdetail=Result::where('user_id',$userid)->where('result_marks_id',$list['id'])->where('answer','0')->get();

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

                    $finalusernamelist[]=array('id'=>$userid,'name'=>$userdetdata['name'],'username'=>$userdetdata['username']);

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
                            'result_date'=>$result_marks_date,
                            'result_id'=>(int)$list['id'],
                            'result_type'=>$list['result_type']
                        );
                    }
                }
              }
    		}
            
    	}
    	else{
    		$result_data=[];
    		$finalusernamelist=[];
    	}

    	if(count($finalusernamelist) > 0)
    	{
    		$usernamelist =  array_map("unserialize", array_unique(array_map("serialize", $finalusernamelist)));
    	}
    	else{
    		$usernamelist=[];
    	}
    	

        return view('admin.all_reports.index',compact('result_data','usernamelist','filter_user','filter_start_date','filter_end_date','clear_filter'));

    }
    catch(\Exception $e){
                  return back()->with('error',$e->getMessage());     
               }
    }


}
