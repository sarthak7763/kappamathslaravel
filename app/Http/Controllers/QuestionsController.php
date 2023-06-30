<?php

namespace App\Http\Controllers;

use App\Imports\TheoryQuestionsImport;
use App\Imports\ObjectiveQuestionsImport;
use App\Imports\ObjectiveQuestionsImageImport;
use Illuminate\Http\Request;
use App\Question;
use App\Quiztopic;
use App\Coursetopic;
use App\Subject;
use App\Subjectcategory;
use App\User;
use App\Theoryexcelinstructions;
use App\Objectiveexcelinstructions;
use App\Tempquestions;

use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ObjectiveQuestionSampleExport;
use App\Exports\ObjectiveQuestionImageSampleExport;
use App\Exports\TheoryQuestionSampleExport;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

      try{

        $subjectalldata=Subject::where('status','1')->get();
        if(!empty($subjectalldata))
        {
          $subjectlist=$subjectalldata->toArray();
        }
        else{
          $subjectlist=[];
        }

        if(count($request->all()) > 0)
        {
          $clear_filter=1;
          $subject=$request->course;
          $category=$request->topic;
          $course=$request->sub_topic;
          $quiz_type=$request->quiz_type;

          $topicsdata = Quiztopic::where('quiz_status','1');

          if($subject!="")
          {
            $subjectdata=Subject::find($subject);
            if(is_null($subjectdata)){
          
            }
            else{
              $topicsdata = $topicsdata->where('subject',$subject);
            }
          }

        if($category!="")
        {
          $subjectcategorydata=Subjectcategory::find($category);
          if(is_null($subjectcategorydata))
          {

          }
          else{
            $topicsdata = $topicsdata->where('category',$category);
          }
          
        }

      if($course!="")
        {
          $coursetopicdata=Coursetopic::find($course);
          if(is_null($coursetopicdata))
          {

          }
          else{
            $topicsdata = $topicsdata->where('course_topic',$course);
          }
        }

        if($quiz_type!="")
        {
          $topicsdata = $topicsdata->where('quiz_type',$quiz_type);
        }
        else{
          return redirect('/admin/questions/')->with('error','Quiz type is required.');
        }

        $topicsdata=$topicsdata->get();
        if($topicsdata)
        {
          $topics=$topicsdata->toArray();
          foreach($topics as $key=>$list)
          {
            $topic_id=$list['id'];
            $counttopicquestions=Question::where('topic_id',$topic_id)->count();
            if($counttopicquestions)
            {
              $topics[$key]['qu_count']=$counttopicquestions;
            }
            else{
              $topics[$key]['qu_count']=0;
            }
          }
        }
        else{
          $topics=[];
        }


        }
        else{
          $clear_filter=0;
          $subject="";
          $category="";
          $course="";
          $quiz_type="";

          $topicsdata = Quiztopic::where('quiz_status','1')->get();
          if(!empty($topicsdata))
          {
            $topics=$topicsdata->toArray();
            foreach($topics as $key=>$list)
            {
              $topic_id=$list['id'];
            $counttopicquestions=Question::where('topic_id',$topic_id)->count();
              if($counttopicquestions)
              {
                $topics[$key]['qu_count']=$counttopicquestions;
              }
              else{
                $topics[$key]['qu_count']=0;
              }
            }
          }
          else{
            $topics=[];
          }
        }  

        return view('admin.questions.index', compact('topics','subjectlist','subject','category','course','quiz_type','clear_filter'));
      }
      catch(\Exception $e){
                  return redirect('admin/questions/')->with('deleted','Something went wrong.');     
               }
    }

    public function getquizlist(Request $request)
    {
    try{
      $subject=$request->course;
      $category=$request->topic;
      $course=$request->sub_topic;
      $quiz_type=$request->quiz_type;

      $subjectname="-";
      $categoryname="-";
      $coursename="-";
      $quiz_typename="-";

      	$topicsdata = Quiztopic::where('quiz_status','1');

	      if($subject!="")
	      {
	      	$subjectdata=Subject::find($subject);
	      	if(is_null($subjectdata)){
        
      		}
      		else{
      			$subjectname=$subjectdata->title;
      			$topicsdata = $topicsdata->where('subject',$subject);
      		}
	      }

	      if($category!="")
	      {
	      	$subjectcategorydata=Subjectcategory::find($category);
	      	if(is_null($subjectcategorydata))
	      	{

	      	}
	      	else{
	      		$categoryname=$subjectcategorydata->category_name;
	      		$topicsdata = $topicsdata->where('category',$category);
	      	}
	        
	      }

		  if($course!="")
	      {
	      	$coursetopicdata=Coursetopic::find($course);
	      	if(is_null($coursetopicdata))
	      	{

	      	}
	      	else{
	      		$coursename=$coursetopicdata->topic_name;
	      		$topicsdata = $topicsdata->where('course_topic',$course);
	      	}
	      }

	      if($quiz_type!="")
	      {
	      	if($quiz_type=="1")
	      	{
	      		$quiz_typename="Objective Quiz";
	      	}
	      	elseif($quiz_type=="2")
	      	{
	      		$quiz_typename="Theory Quiz";
	      	}
	      	else{
	      		$quiz_typename="-";
	      	}
	        $topicsdata = $topicsdata->where('quiz_type',$quiz_type);
	      }

		  $topicsdata=$topicsdata->get();
	      if($topicsdata)
	      {
	        $topics=$topicsdata->toArray();
	        foreach($topics as $key=>$list)
	        {
	          $topic_id=$list['id'];
	          $counttopicquestions=Question::where('topic_id',$topic_id)->count();
	          if($counttopicquestions)
	          {
	            $topics[$key]['qu_count']=$counttopicquestions;
	          }
	          else{
	            $topics[$key]['qu_count']=0;
	          }
	        }
	      }
	      else{
	        $topics=[];
	      }

	   $subjectalldata=Subject::where('status','1')->get();
	    if(!empty($subjectalldata))
	    {
	      $subjectlist=$subjectalldata->toArray();
	    }
	    else{
	      $subjectlist=[];
	    }

	    $filterarray=array(
	    	'subjectname'=>$subjectname,
			'categoryname'=>$categoryname,
			'coursename'=>$coursename,
			'quiz_typename'=>$quiz_typename
	    );

	  return view('admin.questions.quizlist', compact('topics','subjectlist','filterarray'));

  }
  catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                    $listmessage=[];
                    foreach($e->errors() as $key=>$list)
                    {
                        $listmessage[$key]=$list[0];
                    }

                    if(count($listmessage) > 0)
                    {
                        return back()->with('valid_error',$listmessage);
                    }
                        else{
                            return redirect('/admin/questions/')->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return redirect('/admin/questions/')->with('error','Something went wrong.');
                    }

               }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $topicid=$request->segment(4);
        if($topicid!="")
        {
        	$quiztopicdata=Quiztopic::find($topicid);
	      	if(is_null($quiztopicdata))
	      	{
	      		return redirect('admin/questions/')->with('error','Something went wrong.');
	      	}
	      	else{
	      		if($quiztopicdata->quiz_type=="1")
	      		{
	      			return view('admin.questions.create',compact('quiztopicdata'));
	      		}
	      		else{
	      			return view('admin.questions.createquiz',compact('quiztopicdata'));
	      		}
	      		
	      	}
        }
        else{
        	return redirect('admin/questions/')->with('error','Something went wrong.');
        }     
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeobjectivequiz(Request $request)
    {
        try{

        if($request->checkboxvalue)
        {
          $option_status=1;
        }
        else{
          $option_status=0;
        }

        $request->validate([
          'topic_id' => 'required',
          'question'=>Rule::requiredIf($request->get_question_preview=="" || $request->get_question_preview_latex==""),

          'a'=>Rule::requiredIf(($request->get_a_option_preview=="" || $request->get_a_option_preview_latex=="") && $option_status==0),

          'b'=>Rule::requiredIf(($request->get_b_option_preview=="" || $request->get_b_option_preview_latex=="") && $option_status==0),

          'c'=>Rule::requiredIf(($request->get_c_option_preview=="" || $request->get_c_option_preview_latex=="") && $option_status==0),

          'd'=>Rule::requiredIf(($request->get_d_option_preview=="" || $request->get_d_option_preview_latex=="") && $option_status==0),

          'optiona_image'=>Rule::requiredIf($option_status==1),
          'optionb_image'=>Rule::requiredIf($option_status==1),
          'optionc_image'=>Rule::requiredIf($option_status==1),
          'optiond_image'=>Rule::requiredIf($option_status==1),
          
          'answer' => 'required'
        ]);

         // return $request;

        $input = $request->all();
        if($input['get_question_preview']=="" || $input['get_question_preview_latex']=="")
        {
        	return back()->with('error','Question is required.');
        }
        else{
        	$quiz_question=htmlentities($input['get_question_preview']);
          $quiz_question_latex=htmlentities($input['get_question_preview_latex']);
        }

        if($option_status==0)
        {
            if($input['get_a_option_preview']=="" || $input['get_a_option_preview_latex']=="")
            {
              return back()->with('error','Option A is required.');
            }
            else{
              $a_option=htmlentities($input['get_a_option_preview']);
              $a_option_latex=htmlentities($input['get_a_option_preview_latex']);
            }

            if($input['get_b_option_preview']=="" || $input['get_b_option_preview_latex']=="")
            {
              return back()->with('error','Option B is required.');
            }
            else{
              $b_option=htmlentities($input['get_b_option_preview']);
              $b_option_latex=htmlentities($input['get_b_option_preview_latex']);
            }

            if($input['get_c_option_preview']=="" || $input['get_c_option_preview_latex']=="")
            {
              return back()->with('error','Option C is required.');
            }
            else{
              $c_option=htmlentities($input['get_c_option_preview']);
              $c_option_latex=htmlentities($input['get_c_option_preview_latex']);
            }

            if($input['get_d_option_preview']=="" || $input['get_d_option_preview_latex']=="")
            {
              return back()->with('error','Option D is required.');
            }
            else{
              $d_option=htmlentities($input['get_d_option_preview']);
              $d_option_latex=htmlentities($input['get_d_option_preview_latex']);
            }

        }
        else{

              $a_option="";
              $a_option_latex="";

              $b_option="";
              $b_option_latex="";

              $c_option="";
              $c_option_latex="";

              $d_option="";
              $d_option_latex="";
            }   


        if($input['get_answer_exp_preview']=="" || $input['get_answer_exp_preview_latex']=="")
        {
          $answer_exp="";
          $answer_exp_latex="";
        }
        else{
          $answer_exp=htmlentities($input['get_answer_exp_preview']);
          $answer_exp_latex=htmlentities($input['get_answer_exp_preview_latex']);
        }


        if ($optionafile = $request->file('optiona_image')) {

            try{
                $request->validate([
                  'optiona_image' => 'required|mimes:jpeg,png,jpg'
                ]);
              }
              catch(\Exception $e){
                        if($e instanceof ValidationException){
                            $listmessage=[];
                            foreach($e->errors() as $key=>$list)
                            {
                                $listmessage[$key]=$list[0];
                            }

                            if(count($listmessage) > 0)
                            {
                                return back()->with('valid_error',$listmessage);
                            }
                            else{
                                return back()->with('error','Something went wrong.');
                            }
                            
                        }
                        else{
                            return back()->with('error','Something went wrong.');
                        }      
                   }

                  $optiona_imagename = 'optiona_'.time().$optionafile->getClientOriginalName();  
                  $optionafile->move('images/questions/options/', $optiona_imagename);
                  $optiona_image = $optiona_imagename;

              }
              else{
                $optiona_image="";
              }


              if ($optionbfile = $request->file('optionb_image')) {

                  try{
                  $request->validate([
                    'optionb_image' => 'required|mimes:jpeg,png,jpg'
                  ]);
                }
                catch(\Exception $e){
                          if($e instanceof ValidationException){
                              $listmessage=[];
                              foreach($e->errors() as $key=>$list)
                              {
                                  $listmessage[$key]=$list[0];
                              }

                              if(count($listmessage) > 0)
                              {
                                  return back()->with('valid_error',$listmessage);
                              }
                              else{
                                  return back()->with('error','Something went wrong.');
                              }
                              
                          }
                          else{
                              return back()->with('error','Something went wrong.');
                          }      
                     }

                  $optionb_imagename = 'optionb_'.time().$optionbfile->getClientOriginalName();  
                  $optionbfile->move('images/questions/options/', $optionb_imagename);
                  $optionb_image = $optionb_imagename;

              }
              else{
                $optionb_image="";
              }

              if ($optioncfile = $request->file('optionc_image')) {

                  try{
                  $request->validate([
                    'optionc_image' => 'required|mimes:jpeg,png,jpg'
                  ]);
                }
                catch(\Exception $e){
                          if($e instanceof ValidationException){
                              $listmessage=[];
                              foreach($e->errors() as $key=>$list)
                              {
                                  $listmessage[$key]=$list[0];
                              }

                              if(count($listmessage) > 0)
                              {
                                  return back()->with('valid_error',$listmessage);
                              }
                              else{
                                  return back()->with('error','Something went wrong.');
                              }
                              
                          }
                          else{
                              return back()->with('error','Something went wrong.');
                          }      
                     }

                  $optionc_imagename = 'optionc_'.time().$optioncfile->getClientOriginalName();  
                  $optioncfile->move('images/questions/options/', $optionc_imagename);
                  $optionc_image = $optionc_imagename;

              }
              else{
                $optionc_image="";
              }

              if ($optiondfile = $request->file('optiond_image')) {

                  try{
                  $request->validate([
                    'optiond_image' => 'required|mimes:jpeg,png,jpg'
                  ]);
                }
                catch(\Exception $e){
                          if($e instanceof ValidationException){
                              $listmessage=[];
                              foreach($e->errors() as $key=>$list)
                              {
                                  $listmessage[$key]=$list[0];
                              }

                              if(count($listmessage) > 0)
                              {
                                  return back()->with('valid_error',$listmessage);
                              }
                              else{
                                  return back()->with('error','Something went wrong.');
                              }
                              
                          }
                          else{
                              return back()->with('error','Something went wrong.');
                          }      
                     }

                  $optiond_imagename = 'optiond_'.time().$optiondfile->getClientOriginalName();  
                  $optiondfile->move('images/questions/options/', $optiond_imagename);
                  $optiond_image = $optiond_imagename;

              }
              else{
                $optiond_image="";
              }



        if ($file = $request->file('question_img')) {

            try{
            $request->validate([
              'question_img' => 'required|mimes:jpeg,png,jpg'
            ]);
          }
          catch(\Exception $e){
                    if($e instanceof ValidationException){
                        $listmessage=[];
                        foreach($e->errors() as $key=>$list)
                        {
                            $listmessage[$key]=$list[0];
                        }

                        if(count($listmessage) > 0)
                        {
                            return back()->with('valid_error',$listmessage);
                        }
                        else{
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }      
               }

            $name = 'question_'.time().$file->getClientOriginalName();  
            $file->move('images/questions/', $name);
            $question_img = $name;

        }
        else{
        	$question_img="";
        }



        if ($answerfile = $request->file('answer_explaination_img')) {

            try{
            $request->validate([
              'answer_explaination_img' => 'required|mimes:jpeg,png,jpg'
            ]);
          }
          catch(\Exception $e){
                    if($e instanceof ValidationException){
                        $listmessage=[];
                        foreach($e->errors() as $key=>$list)
                        {
                            $listmessage[$key]=$list[0];
                        }

                        if(count($listmessage) > 0)
                        {
                            return back()->with('valid_error',$listmessage);
                        }
                        else{
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }      
               }

            $name = 'answer_'.time().$answerfile->getClientOriginalName();  
            $answerfile->move('images/questions/', $name);
            $answer_explaination_img = $name;

        }
        else{
          $answer_explaination_img="";
        }

        if($request->question_video_link!="")
        {
            $checkvideo=getVideoDetails($request->question_video_link);
            if($checkvideo['code']=="400")
            {
              return back()->with('error',$checkvideo['message']);
            }
            else{
              $question_video_link=$request->question_video_link;
            }
        }
        else{
          $question_video_link="";
        }

        if($request->answer_explaination_video_link!="")
        {
            $checkanswervideo=getVideoDetails($request->answer_explaination_video_link);
            if($checkanswervideo['code']=="400")
            {
              return back()->with('error',$checkanswervideo['message']);
            }
            else{
              $answer_explaination_video_link=$request->answer_explaination_video_link;
            }
        }
        else{
          $answer_explaination_video_link="";
        }

        $topicid=$request->topic_id;
        $quiztopicdata=Quiztopic::find($topicid);
        if(is_null($quiztopicdata))
      	{
      		return redirect('admin/questions/')->with('error','Something went wrong.');
      	}
      	else{
      		$checkquizquestion=Question::where('question',$quiz_question)->get()->first();
      		if($checkquizquestion)
      		{
      			return back()->with('error','Question already exists.');
      		}
      		else{
	      			try{
	  				$question = new Question;
	                $question->topic_id=$request->topic_id;
	                $question->question=$quiz_question;
                  $question->question_latex=$quiz_question_latex;
	                $question->a=$a_option;
                  $question->a_latex=$a_option_latex;
	                $question->b=$b_option;
                  $question->b_latex=$b_option_latex;
	                $question->c=$c_option;
                  $question->c_latex=$c_option_latex;
	                $question->d=$d_option;
                  $question->d_latex=$d_option_latex;
	                $question->answer=$request->answer;
	                $question->code_snippet="";
	                $question->answer_exp=$answer_exp;
                  $question->answer_exp_latex=$answer_exp_latex;
	                $question->question_img=$question_img;
	                $question->question_video_link=$question_video_link;
	                $question->answer_explaination_img=$answer_explaination_img;
	                $question->answer_explaination_video_link=$answer_explaination_video_link;
                  $question->a_image=$optiona_image;
                  $question->b_image=$optionb_image;
                  $question->c_image=$optionc_image;
                  $question->d_image=$optiond_image;
                  $question->option_status=$option_status;
	                $question->question_status=1;
	                $question->save();
		          return redirect('admin/questions/'.$topicid)->with('success','Question has been added.');

		        }catch(\Exception $e){
		           return back()->with('error','Something went wrong.');
		        }
      		}
      	}
      }
      catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                    $listmessage=[];
                    foreach($e->errors() as $key=>$list)
                    {
                        $listmessage[$key]=$list[0];
                    }

                    if(count($listmessage) > 0)
                    {
                        $listmessage['option_status']=$option_status;
                        return back()->with('valid_error',$listmessage);
                    }
                        else{
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }

               }
    }

    public function storetheoryquiz(Request $request)
    {
      try{
        $request->validate([
          'topic_id' => 'required',
          'question'=>Rule::requiredIf($request->get_question_preview=="" || $request->get_question_preview_latex==""),
        ]);

        $input = $request->all();

        if($input['get_question_preview']=="" || $input['get_question_preview_latex']=="")
        {
          return back()->with('error','Question is required.');
        }
        else{
          $quiz_question=htmlentities($input['get_question_preview']);
          $quiz_question_latex=htmlentities($input['get_question_preview_latex']);
        }


        if($input['get_answer_exp_preview']=="" || $input['get_answer_exp_preview_latex']=="")
        {
          $answer_exp="";
          $answer_exp_latex="";
        }
        else{
          $answer_exp=htmlentities($input['get_answer_exp_preview']);
          $answer_exp_latex=htmlentities($input['get_answer_exp_preview_latex']);
        }

        if ($file = $request->file('question_img')) {

            try{
            $request->validate([
              'question_img' => 'required|mimes:jpeg,png,jpg'
            ]);
          }
          catch(\Exception $e){
                    if($e instanceof ValidationException){
                        $listmessage=[];
                        foreach($e->errors() as $key=>$list)
                        {
                            $listmessage[$key]=$list[0];
                        }

                        if(count($listmessage) > 0)
                        {
                            return back()->with('valid_error',$listmessage);
                        }
                        else{
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }      
               }

            $name = 'question_'.time().$file->getClientOriginalName();  
            $file->move('images/questions/', $name);
            $question_img = $name;

        }
        else{
          $question_img="";
        }

        if ($file = $request->file('answer_explaination_img')) {

            try{
            $request->validate([
              'answer_explaination_img' => 'required|mimes:jpeg,png,jpg'
            ]);
          }
          catch(\Exception $e){
                    if($e instanceof ValidationException){
                        $listmessage=[];
                        foreach($e->errors() as $key=>$list)
                        {
                            $listmessage[$key]=$list[0];
                        }

                        if(count($listmessage) > 0)
                        {
                            return back()->with('valid_error',$listmessage);
                        }
                        else{
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }      
               }

            $name = 'answer_'.time().$file->getClientOriginalName();  
            $file->move('images/questions/', $name);
            $answer_explaination_img = $name;

        }
        else{
          $answer_explaination_img="";
        }

        if($request->question_video_link!="")
        {
            $checkvideo=checkvimeovideoid($request->question_video_link);
            if($checkvideo['code']=="400")
            {
              return back()->with('error',$checkvideo['message']);
            }
            else{
              $question_video_link=$request->question_video_link;
            }
        }
        else{
          $question_video_link="";
        }

        if($request->answer_explaination_video_link!="")
        {
            $checkanswervideo=checkvimeovideoid($request->answer_explaination_video_link);
            if($checkanswervideo['code']=="400")
            {
              return back()->with('error',$checkanswervideo['message']);
            }
            else{
              $answer_explaination_video_link=$request->answer_explaination_video_link;
            }
        }
        else{
          $answer_explaination_video_link="";
        }

        $topicid=$request->topic_id;
        $quiztopicdata=Quiztopic::find($topicid);
        if(is_null($quiztopicdata))
      	{
      		return redirect('admin/questions/')->with('error','Something went wrong.');
      	}
      	else{

      		$checkquizquestion=Question::where('question',$quiz_question)->get()->first();
      		if($checkquizquestion)
      		{
      			return back()->with('error','Question already exists.');
      		}
      		else{
	      			try{
	  				$question = new Question;
	                $question->topic_id=$request->topic_id;
	                $question->question=$quiz_question;
                  $question->question_latex=$quiz_question_latex;
	                $question->a='-';
                  $question->a_latex='-';
	                $question->b='-';
                  $question->b_latex='-';
	                $question->c='-';
                  $question->c_latex='-';
	                $question->d='-';
                  $question->d_latex='-';
	                $question->answer='-';
	                $question->code_snippet="";
	                $question->answer_exp=$answer_exp;
                  $question->answer_exp_latex=$answer_exp_latex;
	                $question->question_img=$question_img;
	                $question->question_video_link=$question_video_link;
	                $question->answer_explaination_img=$answer_explaination_img;
	                $question->answer_explaination_video_link=$answer_explaination_video_link;
                  $question->a_image="";
                  $question->b_image="";
                  $question->c_image="";
                  $question->d_image="";
	                $question->question_status=1;
                  $question->option_status=0;
	                $question->save();
		          return redirect('admin/questions/showquiz/'.$topicid)->with('success','Question has been added.');
		        }catch(\Exception $e){
		           return back()->with('error',$e->getMessage());
		        }
      		}
      	}
      }
      catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                    $listmessage=[];
                    foreach($e->errors() as $key=>$list)
                    {
                        $listmessage[$key]=$list[0];
                    }

                    if(count($listmessage) > 0)
                    {
                        return back()->with('valid_error',$listmessage);
                    }
                        else{
                            return back()->with('error','Something went wrong12.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong11.');
                    }

               }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(request $request,$id)
    {
        $topic = Quiztopic::findOrFail($id);
        
        $questions = \DB::table('questions')->where('topic_id', $topic->id)->select('id','question','a','b','c','d','answer','question_status');

        if($request->ajax())
        {
          return DataTables::of($questions)
          ->addIndexColumn()
          ->setRowClass('question_row')
          ->addColumn('question',function($row){

            return '<textarea class="textarea" id="textareavalue_'.$row->id.'" style="display: none;">'.html_entity_decode($row->question).'</textarea>
              <p id="renderer_'.$row->id.'" class="mathrender" style="color: black;font-size: 20px;"></p>';
              //return $row->question;
          })
          ->addColumn('a',function($row){
              return '<textarea class="textarea" id="textareaoptionavalue_'.$row->id.'" style="display: none;">'.html_entity_decode($row->a).'</textarea>
              <p id="rendereroptiona_'.$row->id.'" class="mathrender" style="color: black;font-size: 20px;"></p>';
          })
          ->addColumn('b',function($row){
              return '<textarea class="textarea" id="textareaoptionbvalue_'.$row->id.'" style="display: none;">'.html_entity_decode($row->b).'</textarea>
              <p id="rendereroptionb_'.$row->id.'" class="mathrender" style="color: black;font-size: 20px;"></p>';
          })
          ->addColumn('c',function($row){
              return '<textarea class="textarea" id="textareaoptioncvalue_'.$row->id.'" style="display: none;">'.html_entity_decode($row->c).'</textarea>
              <p id="rendereroptionc_'.$row->id.'" class="mathrender" style="color: black;font-size: 20px;"></p>';
          })
          ->addColumn('d',function($row){
              return '<textarea class="textarea" id="textareaoptiondvalue_'.$row->id.'" style="display: none;">'.html_entity_decode($row->d).'</textarea>
              <p id="rendereroptiond_'.$row->id.'" class="mathrender" style="color: black;font-size: 20px;"></p>';
          })
          ->addColumn('answer',function($row){
              return $row->answer;
          })

          ->addColumn('question_status',function($row){

            if($row->question_status=="1")
            {
                $statusvalue="Active";
            }
            else{
                $statusvalue="suspend";
            }

                return $statusvalue;
            })

          ->addColumn('action', function($row){

          	if($row->question_status=="1")
            {
                $checked="checked";
            }
            else{
                $checked="";
            }

              $btn = '<div class="admin-table-action-block">

                  <a href="' . route('questions.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

                  <a href="' . route('viewquestion', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">View</a>
                
                 <button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->question_status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';

              //       $btn .= '<div id="deleteModal' . $row->id . '" class="delete-modal modal fade" role="dialog">
              //   <div class="modal-dialog modal-sm">
              //     <!-- Modal content-->
              //     <div class="modal-content">
              //       <div class="modal-header">
              //         <button type="button" class="close" data-dismiss="modal">&times;</button>
              //         <div class="delete-icon"></div>
              //       </div>
              //       <div class="modal-body text-center">
              //         <h4 class="modal-heading">Are You Sure ?</h4>
              //         <p>Do you really want to delete these records? This process cannot be undone.</p>
              //       </div>
              //       <div class="modal-footer">
              //         <form method="POST" action="' . route("questions.destroy", $row->id) . '">
              //           ' . method_field("DELETE") . '
              //           ' . csrf_field() . '
              //             <button type="reset" class="btn btn-gray translate-y-3" data-dismiss="modal">No</button>
              //             <button type="submit" class="btn btn-danger">Yes</button>
              //         </form>
              //       </div>
              //     </div>
              //   </div>
              // </div>';


              $btn .= '<div id="changestatusModal' . $row->id . '" class="delete-modal modal fade" role="dialog">
                  <div class="modal-dialog modal-sm">
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="delete-icon"></div>
                      </div>

                       <form method="POST" action="' . route("questionchangestatus") . '">
                          ' . method_field("POST") . '
                          ' . csrf_field() . '
                      <div class="modal-body text-center">
                        <h4 class="modal-heading">Are You Sure ?</h4>
                        <p>Do you really want to Change the  status of this record? This process cannot be undone.</p>

                        <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Status: </label>
                             <input '.$checked.' type="checkbox" class="toggle-input statusvalue" name="status" id="toggle_status'.$row->id.'">
                             <label for="toggle_status'.$row->id.'"></label>
                            <br>
                          </div>
                          </div>
                          </div>

                      </div>
                      <div class="modal-footer">
                          <input type="hidden" name="id" value="'.$row->id.'">
                            <button type="reset" class="btn btn-gray translate-y-3" data-dismiss="modal">No</button>
                            <button type="submit" class="btn btn-danger">Yes</button>
                      </div>
                    </div>
                    </form>
                  </div>
                </div>';

              return $btn;
          })
          ->rawColumns(['question','a','b','c','d','answer','action'])
          ->make(true);
        }
        return view('admin.questions.show', compact('topic', 'questions'));
    }

    public function showquiz(request $request,$id)
    {
        $topic = Quiztopic::findOrFail($id);
        
        $questions = \DB::table('questions')->where('topic_id', $topic->id)->select('id','question','question_status');

        if($request->ajax())
        {
          return DataTables::of($questions)
          ->addIndexColumn()
          ->addColumn('question',function($row){
              return '<textarea class="textarea" id="textareavalue_'.$row->id.'" style="display: none;">'.html_entity_decode($row->question).'</textarea>
              <p id="renderer_'.$row->id.'" class="mathrender" style="color: black;font-size: 20px;"></p>';
          })
          ->addColumn('question_status',function($row){

            if($row->question_status=="1")
            {
                $statusvalue="Active";
            }
            else{
                $statusvalue="suspend";
            }

                return $statusvalue;
            })

          ->addColumn('action', function($row){

            if($row->question_status=="1")
            {
                $checked="checked";
            }
            else{
                $checked="";
            }

              $btn = '<div class="admin-table-action-block">

                  <a href="' . route('questions.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

                  <a href="' . route('viewquestion', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">View</a>
                
                 <button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->question_status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';

              //       $btn .= '<div id="deleteModal' . $row->id . '" class="delete-modal modal fade" role="dialog">
              //   <div class="modal-dialog modal-sm">
              //     <!-- Modal content-->
              //     <div class="modal-content">
              //       <div class="modal-header">
              //         <button type="button" class="close" data-dismiss="modal">&times;</button>
              //         <div class="delete-icon"></div>
              //       </div>
              //       <div class="modal-body text-center">
              //         <h4 class="modal-heading">Are You Sure ?</h4>
              //         <p>Do you really want to delete these records? This process cannot be undone.</p>
              //       </div>
              //       <div class="modal-footer">
              //         <form method="POST" action="' . route("questions.destroy", $row->id) . '">
              //           ' . method_field("DELETE") . '
              //           ' . csrf_field() . '
              //             <button type="reset" class="btn btn-gray translate-y-3" data-dismiss="modal">No</button>
              //             <button type="submit" class="btn btn-danger">Yes</button>
              //         </form>
              //       </div>
              //     </div>
              //   </div>
              // </div>';


              $btn .= '<div id="changestatusModal' . $row->id . '" class="delete-modal modal fade" role="dialog">
                  <div class="modal-dialog modal-sm">
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="delete-icon"></div>
                      </div>

                       <form method="POST" action="' . route("questionchangestatus") . '">
                          ' . method_field("POST") . '
                          ' . csrf_field() . '
                      <div class="modal-body text-center">
                        <h4 class="modal-heading">Are You Sure ?</h4>
                        <p>Do you really want to Change the  status of this record? This process cannot be undone.</p>

                        <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Status: </label>
                             <input '.$checked.' type="checkbox" class="toggle-input statusvalue" name="status" id="toggle_status'.$row->id.'">
                             <label for="toggle_status'.$row->id.'"></label>
                            <br>
                          </div>
                          </div>
                          </div>

                      </div>
                      <div class="modal-footer">
                          <input type="hidden" name="id" value="'.$row->id.'">
                            <button type="reset" class="btn btn-gray translate-y-3" data-dismiss="modal">No</button>
                            <button type="submit" class="btn btn-danger">Yes</button>
                      </div>
                    </div>
                    </form>
                  </div>
                </div>';

              return $btn;
          })
          ->rawColumns(['question','action'])
          ->make(true);
        }
        return view('admin.questions.showquiz', compact('topic', 'questions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $question = Question::findOrFail($id);
        $topic = Quiztopic::where('id',$question->topic_id)->first();
        if($topic->quiz_type=="1")
        {
        	return view('admin.questions.edit',compact('question','topic'));
        }
        else{
        	return view('admin.questions.editquiz',compact('question','topic'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      try{

        if($request->checkboxvalue)
        {
          $option_status=1;
        }
        else{
          $option_status=0;
        }

    $question = Question::find($id);
        if(is_null($question)){
       return redirect('admin/questions')->with('error','Something went wrong.');
    }

        $request->validate([
          'topic_id' => 'required',

           'question'=>Rule::requiredIf($request->get_question_preview=="" || $request->get_question_preview_latex==""),

          'a'=>Rule::requiredIf(($request->get_a_option_preview=="" || $request->get_a_option_preview_latex=="") && $option_status==0),

          'b'=>Rule::requiredIf(($request->get_b_option_preview=="" || $request->get_b_option_preview_latex=="") && $option_status==0),

          'c'=>Rule::requiredIf(($request->get_c_option_preview=="" || $request->get_c_option_preview_latex=="") && $option_status==0),

          'd'=>Rule::requiredIf(($request->get_d_option_preview=="" || $request->get_d_option_preview_latex=="") && $option_status==0),

          'optiona_image'=>Rule::requiredIf($option_status==1 && $question->a_image==""),
          'optionb_image'=>Rule::requiredIf($option_status==1 && $question->b_image==""),
          'optionc_image'=>Rule::requiredIf($option_status==1 && $question->c_image==""),
          'optiond_image'=>Rule::requiredIf($option_status==1 && $question->d_image==""),

          'answer' => 'required'
        ]);

       

        $input = $request->all();

        if($input['get_question_preview']=="" || $input['get_question_preview_latex']=="")
        {
          return back()->with('error','Question is required.');
        }
        else{
          $quiz_question=strip_tags(($input['get_question_preview']),"<math><menclose><merror><mfenced><mfrac><mglyph><mi><mlabeledtr><mmultiscripts><mn><mo><mover><mpadded><mphantom><mroot><mrow><ms><mspace><msqrt><mstyle><msub><msubsup><msup><mtable><mtd><mtext><mtr><munder><munderover><semantics>");
          $quiz_question=htmlentities($quiz_question);
          $quiz_question_latex=htmlentities($input['get_question_preview_latex']);
        }

        if($option_status==0)
        {

        if($input['get_a_option_preview']=="" || $input['get_a_option_preview_latex']=="")
        {
          return back()->with('error','Option A is required.');
        }
        else{

          $a_option=strip_tags(($input['get_a_option_preview']),"<math><menclose><merror><mfenced><mfrac><mglyph><mi><mlabeledtr><mmultiscripts><mn><mo><mover><mpadded><mphantom><mroot><mrow><ms><mspace><msqrt><mstyle><msub><msubsup><msup><mtable><mtd><mtext><mtr><munder><munderover><semantics>");
          $a_option=htmlentities($a_option);
          $a_option_latex=htmlentities($input['get_a_option_preview_latex']);
        }

        if($input['get_b_option_preview']=="" || $input['get_b_option_preview_latex']=="")
        {
          return back()->with('error','Option B is required.');
        }
        else{

          $b_option=strip_tags(($input['get_b_option_preview']),"<math><menclose><merror><mfenced><mfrac><mglyph><mi><mlabeledtr><mmultiscripts><mn><mo><mover><mpadded><mphantom><mroot><mrow><ms><mspace><msqrt><mstyle><msub><msubsup><msup><mtable><mtd><mtext><mtr><munder><munderover><semantics>");
          $b_option=htmlentities($b_option);
          $b_option_latex=htmlentities($input['get_b_option_preview_latex']);
        }

        if($input['get_c_option_preview']=="" || $input['get_c_option_preview_latex']=="")
        {
          return back()->with('error','Option C is required.');
        }
        else{

          $c_option=strip_tags(($input['get_c_option_preview']),"<math><menclose><merror><mfenced><mfrac><mglyph><mi><mlabeledtr><mmultiscripts><mn><mo><mover><mpadded><mphantom><mroot><mrow><ms><mspace><msqrt><mstyle><msub><msubsup><msup><mtable><mtd><mtext><mtr><munder><munderover><semantics>");
          $c_option=htmlentities($c_option);
          $c_option_latex=htmlentities($input['get_c_option_preview_latex']);
        }


        if($input['get_d_option_preview']=="" || $input['get_d_option_preview_latex']=="")
        {
          return back()->with('error','Option D is required.');
        }
        else{

          $d_option=strip_tags(($input['get_d_option_preview']),"<math><menclose><merror><mfenced><mfrac><mglyph><mi><mlabeledtr><mmultiscripts><mn><mo><mover><mpadded><mphantom><mroot><mrow><ms><mspace><msqrt><mstyle><msub><msubsup><msup><mtable><mtd><mtext><mtr><munder><munderover><semantics>");
          $d_option=htmlentities($d_option);
          $d_option_latex=htmlentities($input['get_d_option_preview_latex']);
        }
      }
      else{
              $a_option="";
              $a_option_latex="";

              $b_option="";
              $b_option_latex="";

              $c_option="";
              $c_option_latex="";

              $d_option="";
              $d_option_latex="";
      }

        if($input['get_answer_exp_preview']=="" || $input['get_answer_exp_preview_latex']=="")
        {
          $answer_exp="";
          $answer_exp_latex="";
        }
        else{

          $answer_exp=strip_tags(($input['get_answer_exp_preview']),"<math><menclose><merror><mfenced><mfrac><mglyph><mi><mlabeledtr><mmultiscripts><mn><mo><mover><mpadded><mphantom><mroot><mrow><ms><mspace><msqrt><mstyle><msub><msubsup><msup><mtable><mtd><mtext><mtr><munder><munderover><semantics>");
          $answer_exp=htmlentities($answer_exp);
          $answer_exp_latex=htmlentities($input['get_answer_exp_preview_latex']);
        }


        $topicid=$question->topic_id;

        if ($optionafile = $request->file('optiona_image')) {

            try{
                $request->validate([
                  'optiona_image' => 'required|mimes:jpeg,png,jpg'
                ]);
              }
              catch(\Exception $e){
                        if($e instanceof ValidationException){
                            $listmessage=[];
                            foreach($e->errors() as $key=>$list)
                            {
                                $listmessage[$key]=$list[0];
                            }

                            if(count($listmessage) > 0)
                            {
                                return back()->with('valid_error',$listmessage);
                            }
                            else{
                                return back()->with('error','Something went wrong.');
                            }
                            
                        }
                        else{
                            return back()->with('error','Something went wrong.');
                        }      
                   }

                  $optiona_imagename = 'optiona_'.time().$optionafile->getClientOriginalName();  
                  $optionafile->move('images/questions/options/', $optiona_imagename);
                  $optiona_image = $optiona_imagename;

              }
              else{
                $optiona_image="";
              }


              if ($optionbfile = $request->file('optionb_image')) {

                  try{
                  $request->validate([
                    'optionb_image' => 'required|mimes:jpeg,png,jpg'
                  ]);
                }
                catch(\Exception $e){
                          if($e instanceof ValidationException){
                              $listmessage=[];
                              foreach($e->errors() as $key=>$list)
                              {
                                  $listmessage[$key]=$list[0];
                              }

                              if(count($listmessage) > 0)
                              {
                                  return back()->with('valid_error',$listmessage);
                              }
                              else{
                                  return back()->with('error','Something went wrong.');
                              }
                              
                          }
                          else{
                              return back()->with('error','Something went wrong.');
                          }      
                     }

                  $optionb_imagename = 'optionb_'.time().$optionbfile->getClientOriginalName();  
                  $optionbfile->move('images/questions/options/', $optionb_imagename);
                  $optionb_image = $optionb_imagename;

              }
              else{
                $optionb_image="";
              }

              if ($optioncfile = $request->file('optionc_image')) {

                  try{
                  $request->validate([
                    'optionc_image' => 'required|mimes:jpeg,png,jpg'
                  ]);
                }
                catch(\Exception $e){
                          if($e instanceof ValidationException){
                              $listmessage=[];
                              foreach($e->errors() as $key=>$list)
                              {
                                  $listmessage[$key]=$list[0];
                              }

                              if(count($listmessage) > 0)
                              {
                                  return back()->with('valid_error',$listmessage);
                              }
                              else{
                                  return back()->with('error','Something went wrong.');
                              }
                              
                          }
                          else{
                              return back()->with('error','Something went wrong.');
                          }      
                     }

                  $optionc_imagename = 'optionc_'.time().$optioncfile->getClientOriginalName();  
                  $optioncfile->move('images/questions/options/', $optionc_imagename);
                  $optionc_image = $optionc_imagename;

              }
              else{
                $optionc_image="";
              }

              if ($optiondfile = $request->file('optiond_image')) {

                  try{
                  $request->validate([
                    'optiond_image' => 'required|mimes:jpeg,png,jpg'
                  ]);
                }
                catch(\Exception $e){
                          if($e instanceof ValidationException){
                              $listmessage=[];
                              foreach($e->errors() as $key=>$list)
                              {
                                  $listmessage[$key]=$list[0];
                              }

                              if(count($listmessage) > 0)
                              {
                                  return back()->with('valid_error',$listmessage);
                              }
                              else{
                                  return back()->with('error','Something went wrong.');
                              }
                              
                          }
                          else{
                              return back()->with('error','Something went wrong.');
                          }      
                     }

                  $optiond_imagename = 'optiond_'.time().$optiondfile->getClientOriginalName();  
                  $optiondfile->move('images/questions/options/', $optiond_imagename);
                  $optiond_image = $optiond_imagename;

              }
              else{
                $optiond_image="";
              }

        if ($file = $request->file('question_img')) {

            try{
            $request->validate([
              'question_img' => 'required|mimes:jpeg,png,jpg'
            ]);
          }
          catch(\Exception $e){
                    if($e instanceof ValidationException){
                        $listmessage=[];
                        foreach($e->errors() as $key=>$list)
                        {
                            $listmessage[$key]=$list[0];
                        }

                        if(count($listmessage) > 0)
                        {
                            return back()->with('valid_error',$listmessage);
                        }
                        else{
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }      
               }

            $name = 'question_'.time().$file->getClientOriginalName();  
            $file->move('images/questions/', $name);
            $question_img = $name;

        }
        else{
          $question_img="";
        }

        if ($file = $request->file('answer_explaination_img')) {

            try{
            $request->validate([
              'answer_explaination_img' => 'required|mimes:jpeg,png,jpg'
            ]);
          }
          catch(\Exception $e){
                    if($e instanceof ValidationException){
                        $listmessage=[];
                        foreach($e->errors() as $key=>$list)
                        {
                            $listmessage[$key]=$list[0];
                        }

                        if(count($listmessage) > 0)
                        {
                            return back()->with('valid_error',$listmessage);
                        }
                        else{
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }      
               }

            $name = 'answer_'.time().$file->getClientOriginalName();  
            $file->move('images/questions/', $name);
            $answer_explaination_img = $name;

        }
        else{
          $answer_explaination_img="";
        }

        if($request->question_video_link!="")
        {
            $checkvideo=checkvimeovideoid($request->question_video_link);
            if($checkvideo['code']=="400")
            {
              return back()->with('error',$checkvideo['message']);
            }
            else{
              $question_video_link=$request->question_video_link;
            }
        }
        else{
          $question_video_link="";
        }

        if($request->answer_explaination_video_link!="")
        {
            $checkanswervideo=checkvimeovideoid($request->answer_explaination_video_link);
            if($checkanswervideo['code']=="400")
            {
              return back()->with('error',$checkanswervideo['message']);
            }
            else{
              $answer_explaination_video_link=$request->answer_explaination_video_link;
            }
        }
        else{
          $answer_explaination_video_link="";
        }

        try
        {

          if($question_img!="")
          {
            $question->question_img=$question_img;
          }

          if($answer_explaination_img!="")
          {
            $question->answer_explaination_img=$answer_explaination_img;
          }

          if($optiona_image!="")
          {
            $question->a_image=$optiona_image;
          }

          if($optionb_image!="")
          {
            $question->b_image=$optionb_image;
          }

          if($optionc_image!="")
          {
            $question->c_image=$optionc_image;
          }

          if($optiond_image!="")
          {
            $question->d_image=$optiond_image;
          }
          
        	if($question->question==$quiz_question)
        	{
        		$question->a=$a_option;
            $question->a_latex=$a_option_latex;
            $question->b=$b_option;
            $question->b_latex=$b_option_latex;
            $question->c=$c_option;
            $question->c_latex=$c_option_latex;
            $question->d=$d_option;
            $question->d_latex=$d_option_latex;
            $question->answer=$request->answer;
            $question->code_snippet="";
            $question->answer_exp=$answer_exp;
            $question->answer_exp_latex=$answer_exp_latex;
            $question->question_video_link=$question_video_link;   
            $question->answer_explaination_video_link=$answer_explaination_video_link;
            $question->option_status=$option_status;
        	}
        	else{
        		$checkquizquestion=Question::where('question',$quiz_question)->get()->first();
        		if($checkquizquestion)
        		{
        			return back()->with('error','Question already exists.');
        		}
        		else{
        			$question->question=$quiz_question;
              $question->question_latex=$quiz_question_latex;
	            $question->a=$a_option;
              $question->a_latex=$a_option_latex;
	            $question->b=$b_option;
              $question->b_latex=$b_option_latex;
	            $question->c=$c_option;
              $question->c_latex=$c_option_latex;
	            $question->d=$d_option;
              $question->d_latex=$d_option_latex;
	            $question->answer=$request->answer;
	            $question->code_snippet="";
	            $question->answer_exp=$answer_exp;
              $question->answer_exp_latex=$answer_exp_latex;
	            $question->question_video_link=$question_video_link;
	            $question->answer_explaination_video_link=$answer_explaination_video_link;
              $question->option_status=$option_status;
        		}
        	}

          $question->save();

          return redirect('admin/questions/'.$topicid)->with('success','Question has been updated.');
        }
        catch(\Exception $e)
        {
          return back()->with('error',$e->getMessage());
        }

      }
      catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                    $listmessage=[];
                    foreach($e->errors() as $key=>$list)
                    {
                        $listmessage[$key]=$list[0];
                    }

                    if(count($listmessage) > 0)
                    {
                        $listmessage['option_status']=$option_status;
                        return back()->with('valid_error',$listmessage);
                    }
                        else{
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }

               }

        
    }

    public function updatetheoryquiz(Request $request, $id)
    {
      try{
        $request->validate([
          'topic_id' => 'required',

          'question'=>Rule::requiredIf($request->get_question_preview=="" || $request->get_question_preview_latex==""),

        ]);

        $question = Question::find($id);
        if(is_null($question)){
       return redirect('admin/questions')->with('error','Something went wrong.');
    }

        $input = $request->all();

        if($input['get_question_preview']=="" || $input['get_question_preview_latex']=="")
        {
          return back()->with('error','Question is required.');
        }
        else{
          $quiz_question=strip_tags(($input['get_question_preview']),"<math><menclose><merror><mfenced><mfrac><mglyph><mi><mlabeledtr><mmultiscripts><mn><mo><mover><mpadded><mphantom><mroot><mrow><ms><mspace><msqrt><mstyle><msub><msubsup><msup><mtable><mtd><mtext><mtr><munder><munderover><semantics>");
          $quiz_question=htmlentities($quiz_question);
          $quiz_question_latex=htmlentities($input['get_question_preview_latex']);
        }


        if($input['get_answer_exp_preview']=="" || $input['get_answer_exp_preview_latex']=="")
        {
          $answer_exp="";
          $answer_exp_latex="";
        }
        else{

          $answer_exp=strip_tags(($input['get_answer_exp_preview']),"<math><menclose><merror><mfenced><mfrac><mglyph><mi><mlabeledtr><mmultiscripts><mn><mo><mover><mpadded><mphantom><mroot><mrow><ms><mspace><msqrt><mstyle><msub><msubsup><msup><mtable><mtd><mtext><mtr><munder><munderover><semantics>");
          $answer_exp=htmlentities($answer_exp);
          $answer_exp_latex=htmlentities($input['get_answer_exp_preview_latex']);
        }

        $topicid=$question->topic_id;

        if ($file = $request->file('question_img')) {

            try{
            $request->validate([
              'question_img' => 'required|mimes:jpeg,png,jpg'
            ]);
          }
          catch(\Exception $e){
                    if($e instanceof ValidationException){
                        $listmessage=[];
                        foreach($e->errors() as $key=>$list)
                        {
                            $listmessage[$key]=$list[0];
                        }

                        if(count($listmessage) > 0)
                        {
                            return back()->with('valid_error',$listmessage);
                        }
                        else{
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }      
               }

            $name = 'question_'.time().$file->getClientOriginalName();  
            $file->move('images/questions/', $name);
            $question_img = $name;

        }
        else{
          $question_img="";
        }

        if ($file = $request->file('answer_explaination_img')) {

            try{
            $request->validate([
              'answer_explaination_img' => 'required|mimes:jpeg,png,jpg'
            ]);
          }
          catch(\Exception $e){
                    if($e instanceof ValidationException){
                        $listmessage=[];
                        foreach($e->errors() as $key=>$list)
                        {
                            $listmessage[$key]=$list[0];
                        }

                        if(count($listmessage) > 0)
                        {
                            return back()->with('valid_error',$listmessage);
                        }
                        else{
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }      
               }

            $name = 'answer_'.time().$file->getClientOriginalName();  
            $file->move('images/questions/', $name);
            $answer_explaination_img = $name;

        }
        else{
          $answer_explaination_img="";
        }

        if($request->question_video_link!="")
        {
            $checkvideo=checkvimeovideoid($request->question_video_link);
            if($checkvideo['code']=="400")
            {
              return back()->with('error',$checkvideo['message']);
            }
            else{
              $question_video_link=$request->question_video_link;
            }
        }
        else{
          $question_video_link="";
        }

        if($request->answer_explaination_video_link!="")
        {
            $checkanswervideo=checkvimeovideoid($request->answer_explaination_video_link);
            if($checkanswervideo['code']=="400")
            {
              return back()->with('error',$checkanswervideo['message']);
            }
            else{
              $answer_explaination_video_link=$request->answer_explaination_video_link;
            }
        }
        else{
          $answer_explaination_video_link="";
        }


        try
        {
          
          if($question_img!="" && $answer_explaination_img!="")
          {
          	if($question->question==$quiz_question)
          	{
          		$question->a='-';
              $question->a_latex='-';
	            $question->b='-';
              $question->b_latex='-';
	            $question->c='-';
              $question->c_latex='-';
	            $question->d='-';
              $question->d_latex='-';
	            $question->answer='-';
	            $question->code_snippet="";
	            $question->answer_exp=$answer_exp;
              $question->answer_exp_latex=$answer_exp_latex;
	            $question->question_img=$question_img;
	            $question->question_video_link=$question_video_link;
	            $question->answer_explaination_img=$answer_explaination_img;
	            $question->answer_explaination_video_link=$answer_explaination_video_link;
          	}
          	else{
          		$checkquizquestion=Question::where('question',$quiz_question)->get()->first();
          		if($checkquizquestion)
          		{
          			return back()->with('error','Question already exists.');
          		}
          		else{
          			$question->question=$quiz_question;
                $question->question_latex=$quiz_question_latex;
		            $question->a='-';
                $question->a_latex='-';
                $question->b='-';
                $question->b_latex='-';
                $question->c='-';
                $question->c_latex='-';
                $question->d='-';
                $question->d_latex='-';
		            $question->answer='-';
		            $question->code_snippet="";
		            $question->answer_exp=$answer_exp;
                $question->answer_exp_latex=$answer_exp_latex;
		            $question->question_img=$question_img;
		            $question->question_video_link=$question_video_link;
		            $question->answer_explaination_img=$answer_explaination_img;
		            $question->answer_explaination_video_link=$answer_explaination_video_link;
          		}
          	}
            
          }
          elseif($question_img!="" && $answer_explaination_img=="")
          {
          	if($question->question==$quiz_question)
          	{
          		$question->a='-';
              $question->a_latex='-';
              $question->b='-';
              $question->b_latex='-';
              $question->c='-';
              $question->c_latex='-';
              $question->d='-';
              $question->d_latex='-';
	            $question->answer='-';
	            $question->code_snippet="";
	            $question->answer_exp=$answer_exp;
              $question->answer_exp_latex=$answer_exp_latex;
	            $question->question_img=$question_img;
	            $question->question_video_link=$question_video_link;
	            $question->answer_explaination_video_link=$answer_explaination_video_link;
          	}
          	else{
          		$checkquizquestion=Question::where('question',$quiz_question)->get()->first();
          		if($checkquizquestion)
          		{
          			return back()->with('error','Question already exists.');
          		}
          		else{
          			$question->question=$quiz_question;
                $question->question_latex=$quiz_question_latex;
		            $question->a='-';
                $question->a_latex='-';
                $question->b='-';
                $question->b_latex='-';
                $question->c='-';
                $question->c_latex='-';
                $question->d='-';
                $question->d_latex='-';
		            $question->answer='-';
		            $question->code_snippet="";
		            $question->answer_exp=$answer_exp;
                $question->answer_exp_latex=$answer_exp_latex;
		            $question->question_img=$question_img;
		            $question->question_video_link=$question_video_link;
		            $question->answer_explaination_video_link=$answer_explaination_video_link;
          		}
          	}
            
          }
          elseif($question_img=="" && $answer_explaination_img!="")
          {
          	if($question->question==$quiz_question)
          	{
          		$question->a='-';
              $question->a_latex='-';
              $question->b='-';
              $question->b_latex='-';
              $question->c='-';
              $question->c_latex='-';
              $question->d='-';
              $question->d_latex='-';
	            $question->answer='-';
	            $question->code_snippet="";
	            $question->answer_exp=$answer_exp;
              $question->answer_exp_latex=$answer_exp_latex;
	            $question->question_video_link=$question_video_link;
	            $question->answer_explaination_img=$answer_explaination_img;
	            $question->answer_explaination_video_link=$answer_explaination_video_link;
          	}
          	else{
          		$checkquizquestion=Question::where('question',$quiz_question)->get()->first();
          		if($checkquizquestio)
          		{
          			return back()->with('error','Question already exists.');
          		}
          		else{
          			$question->question=$quiz_question;
                $question->question_latex=$quiz_question_latex;
		            $question->a='-';
                $question->a_latex='-';
                $question->b='-';
                $question->b_latex='-';
                $question->c='-';
                $question->c_latex='-';
                $question->d='-';
                $question->d_latex='-';
		            $question->answer='-';
		            $question->code_snippet="";
		            $question->answer_exp=$answer_exp;
                $question->answer_exp_latex=$answer_exp_latex;
		            $question->question_video_link=$question_video_link;
		            $question->answer_explaination_img=$answer_explaination_img;
		            $question->answer_explaination_video_link=$answer_explaination_video_link;
          		}
          	}
            
          }
          else{
          	if($question->question==$quiz_question)
          	{
          		$question->a='-';
              $question->a_latex='-';
              $question->b='-';
              $question->b_latex='-';
              $question->c='-';
              $question->c_latex='-';
              $question->d='-';
              $question->d_latex='-';
	            $question->answer='-';
	            $question->code_snippet="";
	            $question->answer_exp=$answer_exp;
              $question->answer_exp_latex=$answer_exp_latex;
	            $question->question_video_link=$question_video_link;
	            $question->answer_explaination_video_link=$answer_explaination_video_link;
          	}
          	else{
          		$checkquizquestion=Question::where('question',$quiz_question)->get()->first();
          		if($checkquizquestion)
          		{
          			return back()->with('error','Question already exists.');
          		}
          		else{
          			$question->question=$quiz_question;
                $question->question_latex=$quiz_question_latex;
		            $question->a='-';
                $question->a_latex='-';
                $question->b='-';
                $question->b_latex='-';
                $question->c='-';
                $question->c_latex='-';
                $question->d='-';
                $question->d_latex='-';
		            $question->answer='-';
		            $question->code_snippet="";
		            $question->answer_exp=$answer_exp;
                $question->answer_exp_latex=$answer_exp_latex;
		            $question->question_video_link=$question_video_link;
		            $question->answer_explaination_video_link=$answer_explaination_video_link;
          		}
          	}
          }

          $question->save();

          return redirect('admin/questions/showquiz/'.$topicid)->with('success','Question has been updated.');
        }
        catch(\Exception $e)
        {
          return back()->with('error',$e->getMessage());
        }
      }
      catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                    $listmessage=[];
                    foreach($e->errors() as $key=>$list)
                    {
                        $listmessage[$key]=$list[0];
                    }

                    if(count($listmessage) > 0)
                    {
                        return back()->with('valid_error',$listmessage);
                    }
                        else{
                            return back()->with('error','Something went wrong12.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong11.');
                    }

               }

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      try{
        $question = Question::find($id);

        if ($question->question_img != null) {
            unlink(public_path().'/images/questions/'.$question->question_img);
        }
        try{
          $question->delete();
          return back()->with('success', 'Question has been deleted');
        }
        catch(\Exception $e)
        {
          return back()->with('error',$e->getMessage());
        }
      }catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
        
    }

    public function changestatus(Request $request)
    {
        try{
        $id=$request->id;
        $question = Question::find($id);

        if(is_null($question)){
       return redirect('admin/questions')->with('error','Something went wrong.');
    }

        if(isset($request->status)){
            $question->question_status = 1;
          }else{
            $question->question_status = 0;
        }

        try{
            $question->save();
           return back()->with('success','Question updated !');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

    }
    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
  }

  public function postAcceptor(Request $request)
  {
     if ($file = $request->file('file')) {
          $name = 'question_'.time().$file->getClientOriginalName();
          $extension=$file->getClientOriginalExtension();
          $extensionarray=array('jpg','png','jpeg');
          if(in_array($extension, $extensionarray))
          {
            $file->move('images/', $name);
            $question_img = 'images/'.$name;
          }
          else{
            $question_img="";
          }  
      }
      else{
        $question_img="";
      }

      echo json_encode(array('location' => $question_img));

  }

  public function import_questions_module(Request $request)
    {   
      $tempquestionslist=Tempquestions::orderBy('id','ASC')->get();
      if($tempquestionslist)
      {
        $tempquestionslistarray=$tempquestionslist->toArray();
        $questionslistarray=[];
        foreach($tempquestionslistarray as $list)
        {
          $questionslistarray[]=array(
            'question'=>$list['question'],
            'a'=>$list['a'],
            'b'=>$list['b'],
            'c'=>$list['c'],
            'd'=>$list['d'],
            'answer_exp'=>$list['answer_exp'],
            'question_id'=>$list['id']
          );
        }
      }
      else{
        $questionslistarray=[];
      }
      return view('admin.questions.import_module',compact('questionslistarray'));    
    }

     /**
     * Import a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importObjectivequestionExcelToDB(Request $request)
    {

      $validator = Validator::make(
        [
            'question_file' => $request->question_file,
            'extension' => strtolower($request->question_file->getClientOriginalExtension()),
        ],
        [
            'question_file' => 'required',
            'extension' => 'required|in:xlsx,xls,csv',
        ]
      );

      if ($validator->fails()) 
      {
        return back()->withErrors('error','Invalid file format Please use xlsx and csv file format !');
      }

      if($request->hasFile('question_file'))
      {
          DB::table('temp_questions')->delete();

          $quiztopicsdata = Quiztopic::where('quiz_type',"1")->where('quiz_status','1')->get();
          if($quiztopicsdata)
          {
            $quiztopicsdatalist=$quiztopicsdata->toArray();

            $quizid_arr=[];
            foreach($quiztopicsdatalist as $list)
            {
              $quizid_arr[]=$list['id'];
            }
          }
          else{
            $quizid_arr=[];
          }

          $excelinstructionscount=Objectiveexcelinstructions::where('option_status',0)->count();
          $headercount=2;
          $intstartrow=(int)$excelinstructionscount+(int)$headercount+1;

          $objectivequestionsimport = new ObjectiveQuestionsImport($quizid_arr,$intstartrow);

          $objectivequestionsimport->onlySheets('ObjectiveQuizSample');

          $failurearray=[];

          try{
            Excel::import($objectivequestionsimport, $request->file('question_file'));
          }
          catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
           $failures = $e->failures();
           foreach ($failures as $failure) {
               $failurearray[]=array(
                'row'=>$failure->row(),
                'attribute'=>$failure->attribute(),
                'errors'=>$failure->errors()[0]
               );
           }
      }

        if(count($failurearray) > 0)
        {
          $listmessage="";
          foreach($failurearray as $list)
          {
              $listmessage.=$list['errors'].' at row'.$list['row'].'<br>';
          }

          return back()->with('error', $listmessage);
        }
        else{
          return back()->with('success', 'Question Imported Successfully');
        }
      }

        return back()->with('error', 'Request data does not have any files to import');
    }



    public function importObjectivequestionImageExcelToDB(Request $request)
    {

      $validator = Validator::make(
        [
            'question_file' => $request->question_file,
            'extension' => strtolower($request->question_file->getClientOriginalExtension()),
        ],
        [
            'question_file' => 'required',
            'extension' => 'required|in:xlsx,xls,csv',
        ]
      );

      if ($validator->fails()) 
      {
        return back()->withErrors('error','Invalid file format Please use xlsx and csv file format !');
      }

      if($request->hasFile('question_file'))
      {
          DB::table('temp_questions')->delete();

          $quiztopicsdata = Quiztopic::where('quiz_type',"1")->where('quiz_status','1')->get();
          if($quiztopicsdata)
          {
            $quiztopicsdatalist=$quiztopicsdata->toArray();

            $quizid_arr=[];
            foreach($quiztopicsdatalist as $list)
            {
              $quizid_arr[]=$list['id'];
            }
          }
          else{
            $quizid_arr=[];
          }

          $excelinstructionscount=Objectiveexcelinstructions::where('option_status',1)->count();
          $headercount=2;
          $intstartrow=(int)$excelinstructionscount+(int)$headercount+1;

          $objectivequestionsimport = new ObjectiveQuestionsImageImport($quizid_arr,$intstartrow);

          $objectivequestionsimport->onlySheets('ObjectiveQuizSample');

          $failurearray=[];

          try{
            Excel::import($objectivequestionsimport, $request->file('question_file'));
          }
          catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
           $failures = $e->failures();
           foreach ($failures as $failure) {
               $failurearray[]=array(
                'row'=>$failure->row(),
                'attribute'=>$failure->attribute(),
                'errors'=>$failure->errors()[0]
               );
           }
      }

        if(count($failurearray) > 0)
        {
          $listmessage="";
          foreach($failurearray as $list)
          {
              $listmessage.=$list['errors'].' at row'.$list['row'].'<br>';
          }

          return back()->with('error', $listmessage);
        }
        else{
          return back()->with('success', 'Question Imported Successfully');
        }
      }

        return back()->with('error', 'Request data does not have any files to import');
    }


    public function importTheoryquestionExcelToDB(Request $request)
    {

      $validator = Validator::make(
        [
            'question_file' => $request->question_file,
            'extension' => strtolower($request->question_file->getClientOriginalExtension()),
        ],
        [
            'question_file' => 'required',
            'extension' => 'required|in:xlsx,xls,csv',
        ]
      );

      if ($validator->fails()) 
      {
        return back()->withErrors('error','Invalid file format Please use xlsx and csv file format !');
      }

      if($request->hasFile('question_file'))
      {

          DB::table('temp_questions')->delete();

          $quiztopicsdata = Quiztopic::where('quiz_type',"2")->where('quiz_status','1')->get();
          if($quiztopicsdata)
          {
            $quiztopicsdatalist=$quiztopicsdata->toArray();

            $quizid_arr=[];
            foreach($quiztopicsdatalist as $list)
            {
              $quizid_arr[]=$list['id'];
            }
          }
          else{
            $quizid_arr=[];
          }

          $excelinstructionscount=Theoryexcelinstructions::count();
          $headercount=2;
          $intstartrow=(int)$excelinstructionscount+(int)$headercount+1;

          $theoryquestionsimport = new TheoryQuestionsImport($quizid_arr,$intstartrow);

          $theoryquestionsimport->onlySheets('TheoryQuizSample');

          $failurearray=[];

          try{
            Excel::import($theoryquestionsimport, $request->file('question_file'));
          }
          catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
           $failures = $e->failures();
           foreach ($failures as $failure) {
               $failurearray[]=array(
                'row'=>$failure->row(),
                'attribute'=>$failure->attribute(),
                'errors'=>$failure->errors()[0]
               );
           }
      }

        if(count($failurearray) > 0)
        {
          $listmessage="";
          foreach($failurearray as $list)
          {
              $listmessage.=$list['errors'].' at row'.$list['row'].'<br>';
          }

          return back()->with('error', $listmessage);
        }
        else{
          return back()->with('success', 'Question Imported Successfully');
        }
      }

        return back()->with('error', 'Request data does not have any files to import');
    }

  public function get_objective_question_sample_export()
    {

      $quiztopicsdata = Quiztopic::where('quiz_type',"1")->where('quiz_status','1')->get();
       if($quiztopicsdata)
       {
          $quiztopicsdatalist=$quiztopicsdata->toArray();
          $quiz_topic_arr=[];
          $quizid_arr=[];
          foreach($quiztopicsdatalist as $list)
          {
            $subjectdata=Subject::where('id',$list['subject'])->first();
            if(!empty($subjectdata))
            {
                $subjectdataarray=$subjectdata->toArray();
                $subjectname=$subjectdataarray['title'];
            }
            else{
                $subjectname="-";
            }

            $categorydata=Subjectcategory::where('id',$list['category'])->first();
              if(!empty($categorydata))
              {
                  $categorydataarray=$categorydata->toArray();
                  $categoryname=$categorydataarray['category_name'];
              }
              else{
                  $categoryname="-";
              }

              $course_topicdata=Coursetopic::where('id',$list['course_topic'])->first();
                if(!empty($course_topicdata))
                {
                    $course_topicdataarray=$course_topicdata->toArray();
                    $coursetopicname=$course_topicdataarray['topic_name'];
                }
                else{
                    $coursetopicname="-";
                }

                $quizid_arr[]=$list['id'];

                $quiz_topic_arr[]=array(
                  'quiz_id'=>$list['id'],
                  'quiz_title'=>$list['title'],
                  'course'=>$subjectname,
                  'course_topic'=>$categoryname,
                  'course_sub_topic'=>$coursetopicname
                );
          }
       }
       else{
          $quiz_topic_arr=[];
          $quizid_arr=[];
       }

        $objectiveexcelinstructionsdata=Objectiveexcelinstructions::where('option_status',0)->get();
       if($objectiveexcelinstructionsdata)
       {

          $objectiveexcelinstructionsdataarray=$objectiveexcelinstructionsdata->toArray();
          $questionarray=[];
          foreach($objectiveexcelinstructionsdataarray as $arr)
          {
            $questionarray[]=array(
              'quiz_id'=>$arr['quiz_id'],
              'question'=>$arr['question'],
              'a'=>$arr['a'],
              'b'=>$arr['b'],
              'c'=>$arr['c'],
              'd'=>$arr['d'],
              'correct_answer'=>$arr['correct_answer'],
              'answer_explaination'=>$arr['answer_explaination'],
              'question_image'=>$arr['question_image'],
              'question_video_link'=>$arr['question_video_link'],
              'answer_explaination_image'=>$arr['answer_explaination_image'],
              'answer_explaination_video_link'=>$arr['answer_explaination_video_link'],
              'a_image'=>$arr['a_image'],
              'b_image'=>$arr['b_image'],
              'c_image'=>$arr['c_image'],
              'd_image'=>$arr['d_image']
            );
          }
       }
       else{
          $questionarray[]=array(
            'quiz_id'=>'',
            'question'=>'',
            'a'=>'',
            'b'=>'',
            'c'=>'',
            'd'=>'',
            'correct_answer'=>'',
            'answer_explaination'=>'',
            'question_image'=>'',
            'question_video_link'=>'',
            'answer_explaination_image'=>'',
            'answer_explaination_video_link'=>'',
            'a_image'=>"",
            'b_image'=>"",
            'c_image'=>"",
            'd_image'=>""
          );
       }

      return Excel::download(new ObjectiveQuestionSampleExport($questionarray,$quiz_topic_arr,$quizid_arr), 'objective_question_sample_export.xlsx');
    }

    public function get_objective_question_images_sample_export()
    {

      $quiztopicsdata = Quiztopic::where('quiz_type',"1")->where('quiz_status','1')->get();
       if($quiztopicsdata)
       {
          $quiztopicsdatalist=$quiztopicsdata->toArray();
          $quiz_topic_arr=[];
          $quizid_arr=[];
          foreach($quiztopicsdatalist as $list)
          {
            $subjectdata=Subject::where('id',$list['subject'])->first();
            if(!empty($subjectdata))
            {
                $subjectdataarray=$subjectdata->toArray();
                $subjectname=$subjectdataarray['title'];
            }
            else{
                $subjectname="-";
            }

            $categorydata=Subjectcategory::where('id',$list['category'])->first();
              if(!empty($categorydata))
              {
                  $categorydataarray=$categorydata->toArray();
                  $categoryname=$categorydataarray['category_name'];
              }
              else{
                  $categoryname="-";
              }

              $course_topicdata=Coursetopic::where('id',$list['course_topic'])->first();
                if(!empty($course_topicdata))
                {
                    $course_topicdataarray=$course_topicdata->toArray();
                    $coursetopicname=$course_topicdataarray['topic_name'];
                }
                else{
                    $coursetopicname="-";
                }

                $quizid_arr[]=$list['id'];

                $quiz_topic_arr[]=array(
                  'quiz_id'=>$list['id'],
                  'quiz_title'=>$list['title'],
                  'course'=>$subjectname,
                  'course_topic'=>$categoryname,
                  'course_sub_topic'=>$coursetopicname
                );
          }
       }
       else{
          $quiz_topic_arr=[];
          $quizid_arr=[];
       }

        $objectiveexcelinstructionsdata=Objectiveexcelinstructions::where('option_status',1)->get();
       if($objectiveexcelinstructionsdata)
       {
          $objectiveexcelinstructionsdataarray=$objectiveexcelinstructionsdata->toArray();

          $questionarray=[];
          foreach($objectiveexcelinstructionsdataarray as $arr)
          {
            $questionarray[]=array(
              'quiz_id'=>$arr['quiz_id'],
              'question'=>$arr['question'],
              'a_image'=>$arr['a_image'],
              'b_image'=>$arr['b_image'],
              'c_image'=>$arr['c_image'],
              'd_image'=>$arr['d_image'],
              'correct_answer'=>$arr['correct_answer'],
              'answer_explaination'=>$arr['answer_explaination'],
              'question_image'=>$arr['question_image'],
              'question_video_link'=>$arr['question_video_link'],
              'answer_explaination_image'=>$arr['answer_explaination_image'],
              'answer_explaination_video_link'=>$arr['answer_explaination_video_link']
            );
          }
       }
       else{
          $questionarray[]=array(
            'quiz_id'=>'',
            'question'=>'',
            'a_image'=>"",
            'b_image'=>"",
            'c_image'=>"",
            'd_image'=>"",
            'correct_answer'=>'',
            'answer_explaination'=>'',
            'question_image'=>'',
            'question_video_link'=>'',
            'answer_explaination_image'=>'',
            'answer_explaination_video_link'=>''
          );
       }

      return Excel::download(new ObjectiveQuestionImageSampleExport($questionarray,$quiz_topic_arr,$quizid_arr), 'objective_question_sample_export.xlsx');
    }

    public function get_theory_question_sample_export()
    {

       $quiztopicsdata = Quiztopic::where('quiz_type',"2")->where('quiz_status','1')->get();
       if($quiztopicsdata)
       {
          $quiztopicsdatalist=$quiztopicsdata->toArray();
          $quiz_topic_arr=[];
          $quizid_arr=[];
          foreach($quiztopicsdatalist as $list)
          {
            $subjectdata=Subject::where('id',$list['subject'])->first();
            if(!empty($subjectdata))
            {
                $subjectdataarray=$subjectdata->toArray();
                $subjectname=$subjectdataarray['title'];
            }
            else{
                $subjectname="-";
            }

            $categorydata=Subjectcategory::where('id',$list['category'])->first();
              if(!empty($categorydata))
              {
                  $categorydataarray=$categorydata->toArray();
                  $categoryname=$categorydataarray['category_name'];
              }
              else{
                  $categoryname="-";
              }

              $course_topicdata=Coursetopic::where('id',$list['course_topic'])->first();
                if(!empty($course_topicdata))
                {
                    $course_topicdataarray=$course_topicdata->toArray();
                    $coursetopicname=$course_topicdataarray['topic_name'];
                }
                else{
                    $coursetopicname="-";
                }

                $quizid_arr[]=$list['id'];

                $quiz_topic_arr[]=array(
                  'quiz_id'=>$list['id'],
                  'quiz_title'=>$list['title'],
                  'course'=>$subjectname,
                  'course_topic'=>$categoryname,
                  'course_sub_topic'=>$coursetopicname
                );
          }
       }
       else{
          $quiz_topic_arr=[];
          $quizid_arr=[];
       }

       
       $theoryexcelinstructionsdata=Theoryexcelinstructions::all();
       if($theoryexcelinstructionsdata)
       {
          $questionarray=[];
          foreach($theoryexcelinstructionsdata as $arr)
          {
            $questionarray[]=array(
              'quiz_id'=>$arr['quiz_id'],
              'question'=>$arr['question'],
              'answer_explaination'=>$arr['answer_explaination'],
              'question_image'=>$arr['question_image'],
              'question_video_link'=>$arr['question_video_link'],
              'answer_explaination_image'=>$arr['answer_explaination_image'],
              'answer_explaination_video_link'=>$arr['answer_explaination_video_link']
            );
          }
       }
       else{
          $questionarray[]=array(
            'quiz_id'=>'',
            'question'=>'',
            'answer_explaination'=>'',
            'question_image'=>'',
            'question_video_link'=>'',
            'answer_explaination_image'=>'',
            'answer_explaination_video_link'=>''
          );
       }

       return Excel::download(new TheoryQuestionSampleExport($questionarray,$quiz_topic_arr,$quizid_arr), 'theory_question_sample_export.xlsx');
    }

    public function viewquestion($id)
    {
        $question = Question::findOrFail($id);
        $topic = Quiztopic::where('id',$question->topic_id)->first();
        return view('admin.questions.view',compact('question','topic'));
    }

    public function viewquizquestion($id)
    {
        $question = Question::findOrFail($id);
        $topic = Quiztopic::where('id',$question->topic_id)->first();
        return view('admin.questions.viewquizquestion',compact('question','topic'));
    }


    public function submitimporttempquestions(Request $request)
    {
        try{

        if(isset($request->question_id) && $request->question_id!="")
        {
          $question_id_arr=$request->question_id;
          if(count($question_id_arr) > 0)
          {
              if(isset($request->questionlatex) && $request->questionlatex!="")
              {
                $questionlatex_arr=$request->questionlatex;
                if(count($questionlatex_arr) > 0)
                {
                  if(isset($request->optionalatex) && $request->optionalatex!="")
                  {
                      $optionalatex_arr=$request->optionalatex;
                      if(count($optionalatex_arr) > 0)
                      {
                        $optionalatex_arr=$optionalatex_arr;
                      }
                      else{
                        $optionalatex_arr=[];
                      }
                  }
                  else{
                    $optionalatex_arr=[];
                  }

                  if(isset($request->optionblatex) && $request->optionblatex!="")
                  {
                      $optionblatex_arr=$request->optionblatex;
                      if(count($optionblatex_arr) > 0)
                      {
                        $optionblatex_arr=$optionblatex_arr;
                      }
                      else{
                        $optionblatex_arr=[];
                      }
                  }
                  else{
                    $optionblatex_arr=[];
                  }

                  if(isset($request->optionclatex) && $request->optionclatex!="")
                  {
                      $optionclatex_arr=$request->optionclatex;
                      if(count($optionclatex_arr) > 0)
                      {
                        $optionclatex_arr=$optionclatex_arr;
                      }
                      else{
                        $optionclatex_arr=[];
                      }
                  }
                  else{
                    $optionclatex_arr=[];
                  }

                  if(isset($request->optiondlatex) && $request->optiondlatex!="")
                  {
                      $optiondlatex_arr=$request->optiondlatex;
                      if(count($optiondlatex_arr) > 0)
                      {
                        $optiondlatex_arr=$optiondlatex_arr;
                      }
                      else{
                        $optiondlatex_arr=[];
                      }
                  }
                  else{
                    $optiondlatex_arr=[];
                  }

                   if(isset($request->answerexplatex) && $request->answerexplatex!="")
                  {
                      $answerexplatex_arr=$request->answerexplatex;
                      if(count($answerexplatex_arr) > 0)
                      {
                        $answerexplatex_arr=$answerexplatex_arr;
                      }
                      else{
                        $answerexplatex_arr=[];
                      }
                  }
                  else{
                    $answerexplatex_arr=[];
                  }


                    foreach($question_id_arr as $key=>$value)
                    {
                      if(isset($questionlatex_arr[$key]))
                      {
                        Tempquestions::where('id', $value)
                         ->update([
                             'question_latex' => $questionlatex_arr[$key]
                          ]);
                      }

                      if(isset($optionalatex_arr[$key]))
                      {
                        Tempquestions::where('id', $value)
                         ->update([
                             'a_latex' => $optionalatex_arr[$key]
                          ]);
                      }

                      if(isset($optionblatex_arr[$key]))
                      {
                        Tempquestions::where('id', $value)
                         ->update([
                             'b_latex' => $optionblatex_arr[$key]
                          ]);
                      }

                      if(isset($optionclatex_arr[$key]))
                      {
                        Tempquestions::where('id', $value)
                         ->update([
                             'c_latex' => $optionclatex_arr[$key]
                          ]);
                      }

                      if(isset($optiondlatex_arr[$key]))
                      {
                        Tempquestions::where('id', $value)
                         ->update([
                             'd_latex' => $optiondlatex_arr[$key]
                          ]);
                      }

                      if(isset($answerexplatex_arr[$key]))
                      {
                        Tempquestions::where('id', $value)
                         ->update([
                             'answer_exp_latex' => $answerexplatex_arr[$key]
                          ]);
                      }
                    }

                    Tempquestions::query()
                     ->where('question_status',1)
                     ->each(function ($oldPost) {
                      $newPost = $oldPost->replicate();
                      $newPost->setTable('questions');
                      $newPost->save();

                      $oldPost->delete();

                    });


                    return back()->with('success', 'Questions imported Successfully.');

                }
                else{
                  return back()->with('error', 'Something went wrong.');
                }
              }
              else{
                return back()->with('error', 'Something went wrong.');
              }
          }
          else{
            return back()->with('error', 'Something went wrong.');
          }
        }
        else{
          return back()->with('error', 'Something went wrong.');
        }
    }
    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
    }


    public function deleteimagefromdb(Request $request)
    {
      try{

        $input = $request->all();

        $request->validate([
            'question_id'=>'required',
            'image_type'=>'required'
        ]);

        $question_id=$request->question_id;
        $image_type=$request->image_type;

        $questiondata=Question::find($question_id);


      if(is_null($questiondata)){
        $data=array('code'=>'400','message'=>'Something went wrong4');
      }

      try{

        if (File::exists(public_path('images/questions/'.$questiondata->$image_type))) {
            unlink("images/questions/".$questiondata->$image_type);
        } else {
            unlink("images/questions/options/".$questiondata->$image_type);
        }

        $questiondata->$image_type="";
        $questiondata->save();

        $data=array('code'=>'200','message'=>'Image deleted Successfully.');
      }
        catch(\Exception $e)
        {
          $data=array('code'=>'400','message'=>'Something went wrong1.');
        }
      }
      catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                    $listmessage=[];
                    foreach($e->errors() as $key=>$list)
                    {
                        $listmessage[$key]=$list[0];
                    }

                    if(count($listmessage) > 0)
                    {
                        return back()->with('valid_error',$listmessage);
                    }
                        else{
                        $data=array('code'=>'400','message'=>'Something went wrong2.');
                        }
                        
                    }
                    else{
                      $data=array('code'=>'400','message'=>'Something went wrong3.');
                    }

               }

               return json_encode($data);

    }

}
