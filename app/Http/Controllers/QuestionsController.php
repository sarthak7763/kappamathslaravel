<?php

namespace App\Http\Controllers;

use App\Imports\QuestionsImport;
use Illuminate\Http\Request;
use App\Question;
use App\Quiztopic;
use App\Coursetopic;
use App\Subject;
use App\Subjectcategory;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

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
                     return redirect('admin/questions/')->with('deleted','Something went wrong.'); 
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
	      		return redirect('admin/questions/')->with('deleted','Something went wrong.');
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
        	return redirect('admin/questions/')->with('deleted','Something went wrong.');
        }     
    }

    /**
     * Import a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importExcelToDB(Request $request)
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
        return back()->withErrors('deleted','Invalid file format Please use xlsx and csv file format !');
      }

      if($request->hasFile('question_file'))
      {
        // return $request->file('question_file');
        Excel::import(new QuestionsImport, $request->file('question_file'));
        return back()->with('added', 'Question Imported Successfully');
      }
        return back()->with('deleted', 'Request data does not have any files to import');
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
          'answer' => 'required',
          'question_img' => 'sometimes|image|mimes:jpg,jpeg,png'
        ]);

         // return $request;

        $input = $request->all();

        if ($file = $request->file('question_img')) {
            $name = 'question_'.time().$file->getClientOriginalName();  
            $file->move('images/questions/', $name);
            $question_img = $name;

        }
        else{
        	$question_img="";
        }

        $topicid=$request->topic_id;
        $quiztopicdata=Quiztopic::find($topicid);
        if(is_null($quiztopicdata))
      	{
      		return redirect('admin/questions/')->with('deleted','Something went wrong.');
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
                $question->code_snippet=$request->code_snippet;
                $question->answer_exp=$request->answer_exp;
                $question->question_img=$question_img;
                $question->question_video_link=$request->question_video_link;
                $question->question_status=1;
                $question->save();
	          return redirect('admin/questions/'.$topicid)->with('added','Question has been added.');

	        }catch(\Exception $e){
	           return back()->with('deleted',$e->getMessage());
	        }
      	}
      }
      catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }
    }

    public function storetheoryquiz(Request $request)
    {
      try{
        $request->validate([
          'topic_id' => 'required',
          'question' => 'required',
          'answer_exp' => 'required',
          'question_img' => 'sometimes|image|mimes:jpg,jpeg,png'
        ]);

         // return $request;

        $input = $request->all();

        if ($file = $request->file('question_img')) {
            $name = 'question_'.time().$file->getClientOriginalName();  
            $file->move('images/questions/', $name);
            $question_img = $name;

        }
        else{
        	$question_img="";
        }

        $topicid=$request->topic_id;
        $quiztopicdata=Quiztopic::find($topicid);
        if(is_null($quiztopicdata))
      	{
      		return redirect('admin/questions/')->with('deleted','Something went wrong.');
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
                $question->code_snippet=$request->code_snippet;
                $question->answer_exp=$request->answer_exp;
                $question->question_img=$question_img;
                $question->question_video_link=$request->question_video_link;
                $question->question_status=1;
                $question->save();
	          return redirect('admin/questions/'.$topicid)->with('added','Question has been added.');
	        }catch(\Exception $e){
	           return back()->with('deleted',$e->getMessage());
	        }
      	}
      }
      catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
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
        
        $questions = \DB::table('questions')->where('topic_id', $topic->id)->select('id','question','a','b','c','d','e','f','answer','question_status');

        if($request->ajax())
        {
          return DataTables::of($questions)

          ->filter(function ($row) use ($request) { 
            if ($request->input('search.value') != "") {
                $search=$request->input('search.value');
                $row->where('question', 'LIKE', '%'.$search.'%');
            }
        })

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
        $request->validate([
          'topic_id' => 'required',
          'question' => 'required',
          'a' => 'required',
          'b' => 'required',
          'c' => 'required',
          'd' => 'required',
          'answer' => 'required',
        ]);

        $question = Question::find($id);
        if(is_null($question)){
		   return redirect('admin/questions')->with('deleted','Something went wrong.');
		}

        $input = $request->all();
        $topicid=$question->topic_id;

        if ($file = $request->file('question_img')) {
            $name = 'question_'.time().$file->getClientOriginalName();
            $file->move('images/questions/', $name);
            $question_img = $name;
        }
        else{
        	$question_img="";
        }

        try
        {
          
          if($question_img!="")
          {
            $question->question=$request->question;
            $question->a=$request->a;
            $question->b=$request->b;
            $question->c=$request->c;
            $question->d=$request->d;
            $question->answer=$request->answer;
            $question->code_snippet=$request->code_snippet;
            $question->answer_exp=$request->answer_exp;
            $question->question_img=$question_img;
            $question->question_video_link=$request->question_video_link;
          }
          else{
            $question->question=$request->question;
            $question->a=$request->a;
            $question->b=$request->b;
            $question->c=$request->c;
            $question->d=$request->d;
            $question->answer=$request->answer;
            $question->code_snippet=$request->code_snippet;
            $question->answer_exp=$request->answer_exp;
            $question->question_video_link=$request->question_video_link;
          }

          $question->save();

          return redirect('admin/questions/'.$topicid)->with('added','Question has been added.');
        }
        catch(\Exception $e)
        {
          return back()->with('deleted',$e->getMessage());
        }

        
    }

    public function updatetheoryquiz(Request $request, $id)
    {
        $request->validate([
          'topic_id' => 'required',
          'question' => 'required',
          'answer_exp' =>'required',
        ]);

        $question = Question::find($id);
        if(is_null($question)){
       return redirect('admin/questions')->with('deleted','Something went wrong.');
    }

        $input = $request->all();
        $topicid=$question->topic_id;

        if ($file = $request->file('question_img')) {
            $name = 'question_'.time().$file->getClientOriginalName();
            $file->move('images/questions/', $name);
            $question_img = $name;
        }
        else{
          $question_img="";
        }

        try
        {
          
          if($question_img!="")
          {
            $question->question=$request->question;
            $question->a='-';
            $question->b='-';
            $question->c='-';
            $question->d='-';
            $question->answer='-';
            $question->code_snippet=$request->code_snippet;
            $question->answer_exp=$request->answer_exp;
            $question->question_img=$question_img;
            $question->question_video_link=$request->question_video_link;
          }
          else{
            $question->question=$request->question;
            $question->a='-';
            $question->b='-';
            $question->c='-';
            $question->d='-';
            $question->answer='-';
            $question->code_snippet=$request->code_snippet;
            $question->answer_exp=$request->answer_exp;
            $question->question_video_link=$request->question_video_link;
          }

          $question->save();

          return redirect('admin/questions/'.$topicid)->with('added','Question has been added.');
        }
        catch(\Exception $e)
        {
          return back()->with('deleted',$e->getMessage());
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
        $question = Question::find($id);

        if ($question->question_img != null) {
            unlink(public_path().'/images/questions/'.$question->question_img);
        }
        try{
          $question->delete();
          return back()->with('deleted', 'Question has been deleted');
        }
        catch(\Exception $e)
        {
          return back()->with('deleted',$e->getMessage());
        }
        
    }

    public function changestatus(Request $request)
    {
        try{
        $id=$request->id;
        $question = Question::find($id);

        if(is_null($question)){
       return redirect('admin/questions')->with('deleted','Something went wrong.');
    }

        if(isset($request->status)){
            $question->question_status = 1;
          }else{
            $question->question_status = 0;
        }

        try{
            $question->save();
           return back()->with('updated','Question updated !');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
         }

    }
    catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
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

}
