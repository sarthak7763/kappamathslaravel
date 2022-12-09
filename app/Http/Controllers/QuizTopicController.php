<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quiztopic;
use App\Coursetopic;
use App\Subject;
use App\Subjectcategory;
use DataTables;
use Illuminate\Validation\ValidationException;
use Validator;
class QuizTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $topics = Quiztopic::all();

        $topics = \DB::table('quiztopics')->select('id','subject','category','course_topic','quiz_type','title','description','per_q_mark','timer','quiz_status');
          if($request->ajax()){

            return DataTables::of($topics)
            ->addIndexColumn()
            ->addColumn('subject',function($row){
                if($row->subject!="" && $row->subject!="0")
                {
                    $subjectdata=Subject::where('id',$row->subject)->first();
                    if(!empty($subjectdata))
                    {
                        $subjectdataarray=$subjectdata->toArray();
                        $subjectname=$subjectdataarray['title'];
                    }
                    else{
                        $subjectname="NA";
                    }
                }
                else{
                    $subjectname="NA";
                }

                return $subjectname;
            })
            ->addColumn('category',function($row){
                if($row->category!="" && $row->category!="0")
                {
                    $categorydata=Subjectcategory::where('id',$row->category)->first();
                    if(!empty($categorydata))
                    {
                        $categorydataarray=$categorydata->toArray();
                        $categoryname=$categorydataarray['category_name'];
                    }
                    else{
                        $categoryname="NA";
                    }
                }
                else{
                    $categoryname="NA";
                }

                return $categoryname;
            })
            ->addColumn('course_topic',function($row){
                if($row->course_topic!="" && $row->course_topic!="0")
                {
                    $course_topicdata=Coursetopic::where('id',$row->course_topic)->first();
                    if(!empty($course_topicdata))
                    {
                        $course_topicdataarray=$course_topicdata->toArray();
                        $coursetopicname=$course_topicdataarray['topic_name'];
                    }
                    else{
                        $coursetopicname="NA";
                    }
                }
                else{
                    $coursetopicname="NA";
                }

                return $coursetopicname;
            })
            ->addColumn('quiz_type',function($row){

            if($row->quiz_type=="1")
            {
                $quiztypevalue="Objective Quiz";
            }
            else{
                $quiztypevalue="Theory Quiz";
            }

                return $quiztypevalue;
            })
            ->addColumn('title',function($row){
                return $row->title;
            })
            ->addColumn('description',function($row){
                return $row->description;
            })
            ->addColumn('per_q_mark',function($row){
                return $row->per_q_mark;
            })
            ->addColumn('timer',function($row){
              return $row->timer;
            })
            ->addColumn('quiz_status',function($row){

            if($row->quiz_status=="1")
            {
                $statusvalue="Active";
            }
            else{
                $statusvalue="suspend";
            }

                return $statusvalue;
            })

            ->addColumn('action',function($row){

            if($row->quiz_status=="1")
            {
                $checked="checked";
            }
            else{
                $checked="";
            }

              $btn = '<div class="admin-table-action-block">

                    <a href="' . route('quiz-topics.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>
                  
                     <button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->quiz_status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';

                //      $btn .= '<div id="deleteModal' . $row->id . '" class="delete-modal modal fade" role="dialog">
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
                //         <form method="POST" action="' . route("quiz-topics.destroy", $row->id) . '">
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

                       <form method="POST" action="' . route("quiztopicchangestatus") . '">
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
            ->rawColumns(['title','description','per_q_mark','timer','action'])
            ->make(true);

          }

        return view('admin.quiz.index', compact('topics'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        try{
        $subjectalldata=Subject::where('status','1')->get();
          if(!empty($subjectalldata))
          {
            $subjectlist=$subjectalldata->toArray();
          }
          else{
            $subjectlist=[];
          }

          return view('admin.quiz.create', compact('subjectlist'));
      }catch(\Exception $e){
                  return redirect('admin/quiz-topics/')->with('deleted','Something went wrong.');     
               }
    }


     public function getsubjectcategorylist(Request $request)
    {
      try{

        $input = $request->all();

        $request->validate([
            'course'=>'required'
        ]);

        $subject=$request->course;
        $subjectdata=Subject::find($subject);


      if(is_null($subjectdata)){
        $data=array('code'=>'400','message'=>'Something went wrong');
      }

      try{
      $subjectcategorydata=Subjectcategory::where('subject',$subject)->where('category_status','1')->get();

        if(!empty($subjectcategorydata))
        {
          $subjectcategorylist=$subjectcategorydata->toArray();
        }
        else{
          $subjectcategorylist=[];
        }

        $data=array('code'=>'200','message'=>$subjectcategorylist);
      }
        catch(\Exception $e)
        {
          $data=array('code'=>'400','message'=>'Something went wrong.');
        }
      }
      catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                        $listmessage="";
                        foreach($e->errors() as $list)
                        {
                            $listmessage.=$list[0];
                        }

                        if($listmessage!="")
                        {
                          $data=array('code'=>'400','message'=>$listmessage);
                        }
                        else{
                        $data=array('code'=>'400','message'=>'Something went wrong.');
                        }
                        
                    }
                    else{
                      $data=array('code'=>'400','message'=>'Something went wrong.');
                    }

               }

               return json_encode($data);

    }

    public function getcoursetopiclist(Request $request)
    {
      try{

        $input = $request->all();

        $request->validate([
            'course'=>'required',
            'topic'=>'required'
        ]);

        $subject=$request->course;
        $category=$request->topic;

        $subjectdata=Subject::find($subject);

      if(is_null($subjectdata)){
        $data=array('code'=>'400','message'=>'Please choose subject.');
      }

      try{
        $subjectcategorydata=Subjectcategory::where('id',$category)->where('subject',$subject)->get()->first();
        if(!$subjectcategorydata)
        {
          $data=array('code'=>'400','message'=>'Please choose category.');
        }

      $coursetopicdata=Coursetopic::where('subject',$subject)->where('category',$category)->where('topic_status','1')->get();

        if(!empty($coursetopicdata))
        {
          $coursetopiclist=$coursetopicdata->toArray();
        }
        else{
          $coursetopiclist=[];
        }

        $data=array('code'=>'200','message'=>$coursetopiclist);
      }
        catch(\Exception $e)
        {
          $data=array('code'=>'400','message'=>'Something went wrong.');
        }
      }
      catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                        $listmessage="";
                        foreach($e->errors() as $list)
                        {
                            $listmessage.=$list[0].'<br/>';
                        }

                        if($listmessage!="")
                        {
                          $data=array('code'=>'400','message'=>$listmessage);
                        }
                        else{
                        $data=array('code'=>'400','message'=>'Something went wrong.');
                        }
                        
                    }
                    else{
                      $data=array('code'=>'400','message'=>'Something went wrong.');
                    }

               }

               return json_encode($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
       $input = $request->all();
        $request->validate([
          'course'=>'required',
          'topic'=>'required',
          'sub_topic'=>'required',
          'quiz_type'=>'required',
          'title' => 'required|string',
          'per_question_mark' => 'required'    
        ]);

        if(isset($request->status)){
          $statusvalue = "1";
        }else{
          $statusvalue = "0";
        }

        try{
            $subjectdata=Subject::where('id',$request->course)->first();
            if(!$subjectdata)
            {
                return back()->with('error','Please choose course.');
            }
          }catch(\Exception $e){
                return back()->with('error','Something went wrong.');     
             }


             try{
                $categorydata=Subjectcategory::where('id',$request->topic)->where('subject',$request->course)->first();
                if(!$categorydata)
                {
                    return back()->with('error','Please choose topic.');
                }
            }catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }


               try{
                $coursetopicdata=Coursetopic::where('id',$request->sub_topic)->where('subject',$request->course)->where('category',$request->topic)->first();
                if(!$coursetopicdata)
                {
                    return back()->with('error','Please choose Course Topic.');
                }
            }catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

                try{
                $quiztopicdata=Quiztopic::where('course_topic',$request->sub_topic)->where('subject',$request->course)->where('category',$request->topic)->where('quiz_type',$request->quiz_type)->first();
                if($quiztopicdata)
                {
                    if($request->quiz_type=="1")
                    {
                      return back()->with('error','Objective Quiz already added in this subtopic.');
                    }
                    else{
                      return back()->with('error','Theory Quiz already added in this subtopic.');
                    }
                    
                }
            }catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }


        try{
          $quiztopic = new Quiztopic;
          $quiztopic->subject=$request->course;
          $quiztopic->category = $request->topic;
          $quiztopic->course_topic=$request->sub_topic;
          $quiztopic->quiz_type=$request->quiz_type;
          $quiztopic->title = $request->title;
          $quiztopic->description = $request->description;
          $quiztopic->per_q_mark = $request->per_question_mark;
          $quiztopic->timer=$request->timer;
          $quiztopic->quiz_status = $statusvalue;
          $quiztopic->save();

           return redirect('/admin/quiz-topics/')->with('success', 'Quiz Topic has been added');
        }catch(\Exception $e){
          return back()->with('error',$e->getMessage());
           
       }
     }
     catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                        $listmessage="";
                        foreach($e->errors() as $list)
                        {
                            $listmessage.=$list[0].'<br/>';
                        }

                        if($listmessage!="")
                        {
                            return back()->with('error',$listmessage);
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

        $quiztopic = Quiztopic::findOrFail($id);
        $quiztopic->per_question_mark=$quiztopic->per_q_mark;
        $subject=$quiztopic->subject;
        $category=$quiztopic->category;
        $course_topic=$quiztopic->course_topic;

        try{
      $subjectcategorydata=Subjectcategory::where('subject',$subject)->where('category_status','1')->get();

        if(!empty($subjectcategorydata))
        {
          $subjectcategorylist=$subjectcategorydata->toArray();
        }
        else{
          $subjectcategorylist=[];
        }
      }
      catch(\Exception $e){
                  return redirect('admin/quiz-topics/')->with('deleted','Something went wrong.');     
               }

      try{
      $subjectcoursedata=Coursetopic::where('subject',$subject)->where('category',$category)->where('topic_status','1')->get();

        if(!empty($subjectcoursedata))
        {
          $subjectcourselist=$subjectcoursedata->toArray();
        }
        else{
          $subjectcourselist=[];
        }
      }
      catch(\Exception $e){
                  return redirect('admin/quiz-topics/')->with('error','Something went wrong.');     
               }

          return view('admin.quiz.edit', compact('subjectlist','quiztopic','subjectcategorylist','subjectcourselist'));
      }catch(\Exception $e){
                  return redirect('admin/quiz-topics/')->with('error','Something went wrong.');     
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
          'course'=>'required',
          'topic'=>'required',
          'sub_topic'=>'required',
          'quiz_type'=>'required',
          'title' => 'required|string',
          'per_question_mark' => 'required'
          
        ]);

          $quiztopic = Quiztopic::find($id);

          if(is_null($quiztopic)){
       return redirect('admin/quiz-topics')->with('error','Something went wrong11.');
    }

          if(isset($request->status)){
            $statusvalue = 1;
          }else{
            $statusvalue = 0;
          }

          try{
                $subjectdata=Subject::where('id',$request->course)->first();
                if(!$subjectdata)
                {
                    return back()->with('error','Please choose course.');
                }
            }catch(\Exception $e){
                  return back()->with('error','Something went wrong12.');     
               }

            try{
                $categorydata=Subjectcategory::where('id',$request->topic)->where('subject',$request->course)->first();
                if(!$categorydata)
                {
                    return back()->with('error','Please choose topic.');
                }
            }catch(\Exception $e){
                  return back()->with('error','Something went wrong13.');     
               }

               try{
                $coursetopicdata=Coursetopic::where('id',$request->sub_topic)->where('subject',$request->course)->where('category',$request->topic)->first();
                if(!$coursetopicdata)
                {
                    return back()->with('error','Please choose Course Topic.');
                }
            }catch(\Exception $e){
                  return back()->with('error','Something went wrong14.');     
               }


               if($quiztopic->quiz_type!=$request->quiz_type)
               {
                    try{
                  $quiztopicdata=Quiztopic::where('course_topic',$request->sub_topic)->where('subject',$request->course)->where('category',$request->topic)->where('quiz_type',$request->quiz_type)->first();
                  if($quiztopicdata)
                  {
                      if($request->quiz_type=="1")
                      {
                        return back()->with('error','Objective Quiz already added in this subtopic.');
                      }
                      else{
                        return back()->with('error','Theory Quiz already added in this subtopic.');
                      }
                      
                  }
              }catch(\Exception $e){
                    return back()->with('error','Something went wrong.');     
                 }
               }

         try{
            $quiztopic->subject=$request->course;
            $quiztopic->category = $request->topic;
            $quiztopic->course_topic=$request->sub_topic;
            $quiztopic->quiz_type=$request->quiz_type;
            $quiztopic->title = $request->title;
            $quiztopic->description = $request->description;
            $quiztopic->per_q_mark = $request->per_question_mark;
            $quiztopic->timer=$request->timer;
            $quiztopic->quiz_status = $statusvalue;
            $quiztopic->save();

          return redirect('/admin/quiz-topics/')->with('success','Quiz Topic updated !');
         }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

       }
       catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                        $listmessage="";
                        foreach($e->errors() as $list)
                        {
                            $listmessage.=$list[0].'<br/>';
                        }

                        if($listmessage!="")
                        {
                            return back()->with('error',$listmessage);
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
        $quiztopic = Quiztopic::find($id);

         if(is_null($quiztopic)){
       return redirect('admin/quiz-topics')->with('error','Something went wrong.');
    }

        try{
            $quiztopic->delete();
           return back()->with('success', 'Topic has been deleted');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

       }
       catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
        
    }

    public function changestatus(Request $request)
    {
        try{
        $id=$request->id;
        $quiztopic = Quiztopic::find($id);

        if(is_null($quiztopic)){
       return redirect('admin/quiz-topics')->with('error','Something went wrong.');
    }

        if(isset($request->status)){
            $quiztopic->quiz_status = 1;
          }else{
            $quiztopic->quiz_status = 0;
        }

        try{
            $quiztopic->save();
           return back()->with('success','Quiz Topic updated !');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

    }
    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
  }

}
