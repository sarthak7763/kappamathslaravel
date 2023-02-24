<?php

namespace App\Http\Controllers;

use App\Imports\TheoryQuestionsImport;
use App\Imports\ObjectiveQuestionsImport;
use Illuminate\Http\Request;
use App\Question;
use App\Quiztopic;
use App\Coursetopic;
use App\Subject;
use App\Subjectcategory;
use App\User;
use App\Theoryexcelinstructions;
use App\Objectiveexcelinstructions;

use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ObjectiveQuestionSampleExport;
use App\Exports\TheoryQuestionSampleExport;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

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

        return view('admin.questions.index', compact('topics','subjectlist'));
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
        $request->validate([
          'topic_id' => 'required',
          'question' => 'required',
          'a' => 'required',
          'b' => 'required',
          'c' => 'required',
          'd' => 'required',
          'answer' => 'required'
        ]);

         // return $request;

        $input = $request->all();

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
  			try{
  				$question = new Question;
                $question->topic_id=$request->topic_id;
                $question->question=$request->question;
                $question->a=$request->a;
                $question->b=$request->b;
                $question->c=$request->c;
                $question->d=$request->d;
                $question->answer=$request->answer;
                $question->code_snippet="";
                $question->answer_exp=$request->answer_exp;
                $question->question_img=$question_img;
                $question->question_video_link=$question_video_link;
                $question->answer_explaination_img=$answer_explaination_img;
                $question->answer_explaination_video_link=$answer_explaination_video_link;
                $question->question_status=1;
                $question->save();
	          return redirect('admin/questions/'.$topicid)->with('success','Question has been added.');

	        }catch(\Exception $e){
	           return back()->with('error',$e->getMessage());
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

    public function storetheoryquiz(Request $request)
    {
      try{
        $request->validate([
          'topic_id' => 'required',
          'question' => 'required',
          'answer_exp' => 'required'
        ]);

        $input = $request->all();

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
  			try{
  				$question = new Question;
                $question->topic_id=$request->topic_id;
                $question->question=$request->question;
                $question->a='-';
                $question->b='-';
                $question->c='-';
                $question->d='-';
                $question->answer='-';
                $question->code_snippet="";
                $question->answer_exp=$request->answer_exp;
                $question->question_img=$question_img;
                $question->question_video_link=$question_video_link;
                $question->answer_explaination_img=$answer_explaination_img;
                $question->answer_explaination_video_link=$answer_explaination_video_link;
                $question->question_status=1;
                $question->save();
	          return redirect('admin/questions/showquiz/'.$topicid)->with('success','Question has been added.');
	        }catch(\Exception $e){
	           return back()->with('error',$e->getMessage());
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
          ->addColumn('question',function($row){
              return $row->question;
          })
          ->addColumn('a',function($row){
              return $row->a;
          })
          ->addColumn('b',function($row){
              return $row->b;
          })
          ->addColumn('c',function($row){
              return $row->c;
          })
          ->addColumn('d',function($row){
              return $row->d;
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
              return $row->question;
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
        $request->validate([
          'topic_id' => 'required',
          'question' => 'required',
          'a' => 'required',
          'b' => 'required',
          'c' => 'required',
          'd' => 'required',
          'answer' => 'required'
        ]);

        $question = Question::find($id);
        if(is_null($question)){
		   return redirect('admin/questions')->with('error','Something went wrong.');
		}

        $input = $request->all();
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
            $question->question=$request->question;
            $question->a=$request->a;
            $question->b=$request->b;
            $question->c=$request->c;
            $question->d=$request->d;
            $question->answer=$request->answer;
            $question->code_snippet="";
            $question->answer_exp=$request->answer_exp;
            $question->question_img=$question_img;
            $question->question_video_link=$question_video_link;
            $question->answer_explaination_img=$answer_explaination_img;
            $question->answer_explaination_video_link=$answer_explaination_video_link;
          }
          elseif($question_img!="" && $answer_explaination_img=="")
          {
            $question->question=$request->question;
            $question->a=$request->a;
            $question->b=$request->b;
            $question->c=$request->c;
            $question->d=$request->d;
            $question->answer=$request->answer;
            $question->code_snippet="";
            $question->answer_exp=$request->answer_exp;
            $question->question_img=$question_img;
            $question->question_video_link=$question_video_link;
            $question->answer_explaination_video_link=$answer_explaination_video_link;
          }
          elseif($question_img=="" && $answer_explaination_img!="")
          {
            $question->question=$request->question;
            $question->a=$request->a;
            $question->b=$request->b;
            $question->c=$request->c;
            $question->d=$request->d;
            $question->answer=$request->answer;
            $question->code_snippet="";
            $question->answer_exp=$request->answer_exp;
            $question->question_video_link=$question_video_link;
            $question->answer_explaination_img=$answer_explaination_img;
            $question->answer_explaination_video_link=$answer_explaination_video_link;
          }
          else{
            $question->question=$request->question;
            $question->a=$request->a;
            $question->b=$request->b;
            $question->c=$request->c;
            $question->d=$request->d;
            $question->answer=$request->answer;
            $question->code_snippet="";
            $question->answer_exp=$request->answer_exp;
            $question->question_video_link=$question_video_link;
            $question->answer_explaination_video_link=$answer_explaination_video_link;
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

    public function updatetheoryquiz(Request $request, $id)
    {
      try{
        $request->validate([
          'topic_id' => 'required',
          'question' => 'required',
          'answer_exp' =>'required'
        ]);

        $question = Question::find($id);
        if(is_null($question)){
       return redirect('admin/questions')->with('error','Something went wrong.');
    }

        $input = $request->all();
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
            $question->question=$request->question;
            $question->a='-';
            $question->b='-';
            $question->c='-';
            $question->d='-';
            $question->answer='-';
            $question->code_snippet="";
            $question->answer_exp=$request->answer_exp;
            $question->question_img=$question_img;
            $question->question_video_link=$question_video_link;
            $question->answer_explaination_img=$answer_explaination_img;
            $question->answer_explaination_video_link=$answer_explaination_video_link;
          }
          elseif($question_img!="" && $answer_explaination_img=="")
          {
            $question->question=$request->question;
            $question->a='-';
            $question->b='-';
            $question->c='-';
            $question->d='-';
            $question->answer='-';
            $question->code_snippet="";
            $question->answer_exp=$request->answer_exp;
            $question->question_img=$question_img;
            $question->question_video_link=$question_video_link;
            $question->answer_explaination_video_link=$answer_explaination_video_link;
          }
          elseif($question_img=="" && $answer_explaination_img!="")
          {
            $question->question=$request->question;
            $question->a='-';
            $question->b='-';
            $question->c='-';
            $question->d='-';
            $question->answer='-';
            $question->code_snippet="";
            $question->answer_exp=$request->answer_exp;
            $question->question_video_link=$question_video_link;
            $question->answer_explaination_img=$answer_explaination_img;
            $question->answer_explaination_video_link=$answer_explaination_video_link;
          }
          else{
            $question->question=$request->question;
            $question->a='-';
            $question->b='-';
            $question->c='-';
            $question->d='-';
            $question->answer='-';
            $question->code_snippet="";
            $question->answer_exp=$request->answer_exp;
            $question->question_video_link=$question_video_link;
            $question->answer_explaination_video_link=$answer_explaination_video_link;
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
      return view('admin.questions.import_module');    
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

          $excelinstructionscount=Objectiveexcelinstructions::count();
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

        $objectiveexcelinstructionsdata=Objectiveexcelinstructions::all();
       if($objectiveexcelinstructionsdata)
       {
          $questionarray=[];
          foreach($objectiveexcelinstructionsdata as $arr)
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
              'answer_explaination_video_link'=>$arr['answer_explaination_video_link']
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
            'answer_explaination_video_link'=>''
          );
       }

      return Excel::download(new ObjectiveQuestionSampleExport($questionarray,$quiz_topic_arr,$quizid_arr), 'objective_question_sample_export.xlsx');
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

}
