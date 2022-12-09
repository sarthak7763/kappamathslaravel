<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coursetopic;
use App\Subject;
use App\Subjectcategory;
use Exception;
use Yajra\Datatables\DataTables;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class CoursetopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $coursetopic = \DB::table('coursetopics')->select('id','subject','category','topic_name','topic_video_id','topic_status','sort_order');

          if($request->ajax()){

            return DataTables::of($coursetopic)
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
            ->addColumn('topic_name',function($row){
                return $row->topic_name;
            })
            ->addColumn('topic_video_id',function($row){
                return $row->topic_video_id;
            })
            ->addColumn('topic_status',function($row){

            if($row->topic_status=="1")
            {
                $statusvalue="Active";
            }
            else{
                $statusvalue="suspend";
            }

                return $statusvalue;
            })

            ->addColumn('sort_order',function($row){
                return $row->sort_order;
            })

            ->addColumn('action',function($row){

            if($row->topic_status=="1")
            {
                $checked="checked";
            }
            else{
                $checked="";
            }

              $btn = '<div class="admin-table-action-block">

                    <a href="' . route('course-topic.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

                    <button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->topic_status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';
                   

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
                //         <form method="POST" action="' . route("course-topic.destroy", $row->id) . '">
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

                       <form method="POST" action="' . route("coursetopicchangestatus") . '">
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
            ->escapeColumns(['action'])
            ->rawColumns(['category','topic_name','topic_video_id','topic_status','sort_order','action'])
            ->make(true);

          }

        return view('admin.coursetopic.index', compact('coursetopic'));
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

          return view('admin.coursetopic.create', compact('subjectlist'));
      }catch(\Exception $e){
                  return redirect('admin/course-topic/')->with('deleted','Something went wrong.');     
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
              'title' => 'required|string',
              'sort_order'=>'required'
        ]);

        if(isset($request->status)){
          $statusvalue = "1";
        }else{
          $statusvalue = "0";
        }

        if ($file = $request->file('topic_img')) {

            try{
            $request->validate([
              'topic_img' => 'required|mimes:jpeg,png,jpg'
            ]);
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
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }      
               }

            $name = 'topic_'.time().$file->getClientOriginalName(); 
            $file->move('images/topics/', $name);
            $topic_img = $name;
        }
        else{
        	$topic_img="";
        }

        try{
        $coursetopicdata=Coursetopic::where('topic_name',$request->title)->first();
        if($coursetopicdata)
        {
        	return back()->with('error','Title already exists.');
        }
        else{
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

               if($topic_img!="" || $request->topic_video_id)
               {
                    try{
                      $coursetopic = new Coursetopic;
                      $coursetopic->subject=$request->course;
                      $coursetopic->category = $request->topic;
                      $coursetopic->topic_name = $request->title;
                      $coursetopic->topic_description = $request->description;
                      $coursetopic->topic_image = $topic_img;
                      $coursetopic->topic_video_id=$request->topic_video_id;
                      $coursetopic->topic_status = $statusvalue;
                      $coursetopic->sort_order=$request->sort_order;
                      $coursetopic->save();
                     return redirect('/admin/course-topic/')->with('success', 'Sub Topic has been added');
                  }catch(\Exception $e){
                    return back()->with('error',$e->getMessage());     
                 }
               }
               else{
                return back()->with('error','Please choose image or enter video id to continue the process.');
               }  
        }
    }
    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
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
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error',$e->getMessage());
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
	     
	     $coursetopic = Coursetopic::findOrFail($id);
	     $coursetopic->title=$coursetopic->topic_name;
	     $coursetopic->description=$coursetopic->topic_description;
       $coursetopic->topic_video_id=$coursetopic->topic_video_id;
       $subject=$coursetopic->subject;
       $category=$coursetopic->category;

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
                  return redirect('admin/course-topic/')->with('deleted','Something went wrong.');     
               }

        return view('admin.coursetopic.edit',compact('coursetopic','subjectlist','subjectcategorylist'));
     }
     catch(\Exception $e){
                  return redirect('admin/course-topic/')->with('deleted','Something went wrong.');     
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
          'title' => 'required|string',
          'sort_order'=>'required'
        ]);

          $coursetopic = Coursetopic::find($id);

         if(is_null($coursetopic)){
		   return redirect('admin/course-topic')->with('deleted','Something went wrong.');
		}

          if ($file = $request->file('topic_img')) {

            try{
            $request->validate([
              'topic_img' => 'required|mimes:jpeg,png,jpg'
            ]);
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
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }      
               }

            $name = 'topic_'.time().$file->getClientOriginalName(); 
            $file->move('images/topics/', $name);
            $topic_img = $name;
        }
        else{
        	$topic_img="";
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




          if($coursetopic->topic_name==$request->title)
          {
	          if($topic_img!="")
	          {
              $coursetopic->subject=$request->course;
	          	$coursetopic->category = $request->topic;
  		        $coursetopic->topic_description = $request->description;
  		        $coursetopic->topic_image = $topic_img;
  		        $coursetopic->topic_status = $statusvalue;
              $coursetopic->topic_video_id=$request->topic_video_id;
              $coursetopic->sort_order=$request->sort_order;
	          }
	          else{
              $coursetopic->subject=$request->course;
	          	$coursetopic->category = $request->topic;
  		        $coursetopic->topic_description = $request->description;
  		        $coursetopic->topic_status = $statusvalue;
              $coursetopic->topic_video_id=$request->topic_video_id;
              $coursetopic->sort_order=$request->sort_order;
	          }
          }
          else{
          		try{
		        $coursetopicdata=Coursetopic::where('topic_name',$request->title)->first();
		        if($coursetopicdata)
		        {
		        	return back()->with('error','Title already exists.');
		        }
		    }
		    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

            if($topic_img!="")
	          {
              $coursetopic->subject=$request->course;
	          	$coursetopic->category = $request->topic;
	          	$coursetopic->topic_name=$request->title;
  		        $coursetopic->topic_description = $request->description;
  		        $coursetopic->topic_image = $topic_img;
  		        $coursetopic->topic_status = $statusvalue;
              $coursetopic->topic_video_id=$request->topic_video_id;
              $coursetopic->sort_order=$request->sort_order;
	          }
	          else{
              $coursetopic->subject=$request->course;
	          	$coursetopic->category = $request->topic;
	          	$coursetopic->topic_name=$request->title;
		          $coursetopic->topic_description = $request->description;
		          $coursetopic->topic_status = $statusvalue;
              $coursetopic->topic_video_id=$request->topic_video_id;
              $coursetopic->sort_order=$request->sort_order;
	          }
          }
         try{
            $coursetopic->save();
          return redirect('/admin/course-topic/')->with('success','Topic updated !');
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
                            return back()->with('error','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('error',$e->getMessage());
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
        $coursetopic = Coursetopic::find($id);

        if(is_null($coursetopic)){
		   return redirect('admin/course-topic')->with('error','Something went wrong.');
		}

        try{
            $coursetopic->delete();
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
        $coursetopic = Coursetopic::find($id);

        if(is_null($coursetopic)){
		   return redirect('admin/course-topic')->with('error','Something went wrong.');
		}

        if(isset($request->status)){
            $coursetopic->topic_status = 1;
          }else{
            $coursetopic->topic_status = 0;
        }

        try{
            $coursetopic->save();
           return back()->with('success','Topic updated !');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

    }
    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
  }


}
