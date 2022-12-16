<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subjectcategory;
use App\Subject;
use Exception;
use Yajra\Datatables\DataTables;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class SubjectcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->filter_start_date!="" && $request->filter_end_date!="")
        {
          $filter_start_date=date('Y-m-d',strtotime($request->filter_start_date));

          $filter_end_date=date('Y-m-d',strtotime($request->filter_end_date));

          $filter_end_date=date('Y-m-d', strtotime("+1 day", strtotime($filter_end_date)));

          $subjectcategory = \DB::table('subject_category')->where('created_at','>=',$filter_start_date)->where('created_at','<=',$filter_end_date)->select('id','subject','category_name','category_status');
        }
        elseif($request->filter_start_date!="" && $request->filter_end_date=="")
        {
           $filter_start_date=date('Y-m-d',strtotime($request->filter_start_date));

          $subjectcategory = \DB::table('subject_category')->where('created_at','>=',$filter_start_date)->select('id','subject','category_name','category_status');
        }
        elseif($request->filter_start_date=="" && $request->filter_end_date!="")
        {
          $filter_end_date=date('Y-m-d',strtotime($request->filter_end_date));

          $filter_end_date=date('Y-m-d', strtotime("+1 day", strtotime($filter_end_date)));

          $subjectcategory = \DB::table('subject_category')->where('created_at','<=',$filter_end_date)->select('id','subject','category_name','category_status');
        }
        else{
          $subjectcategory = \DB::table('subject_category')->select('id','subject','category_name','category_status');
        }
        

          if($request->ajax()){

            return DataTables::of($subjectcategory)
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
            ->addColumn('category_name',function($row){
                return $row->category_name;
            })
            ->addColumn('category_status',function($row){

            if($row->category_status=="1")
            {
                $statusvalue="Active";
            }
            else{
                $statusvalue="suspend";
            }

                return $statusvalue;
            })

            ->addColumn('action',function($row){

            if($row->category_status=="1")
            {
                $checked="checked";
            }
            else{
                $checked="";
            }

              $btn = '<div class="admin-table-action-block">

                    <a href="' . route('course-category.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

                    <button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->category_status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';
                   

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
                //         <form method="POST" action="' . route("course-category.destroy", $row->id) . '">
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

                       <form method="POST" action="' . route("subjectcategorychangestatus") . '">
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
            ->rawColumns(['subject','category_name','category_status','action'])
            ->make(true);

          }

        return view('admin.subjectcategory.index', compact('subjectcategory'));
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

          return view('admin.subjectcategory.create', compact('subjectlist'));
      }catch(\Exception $e){
                  return redirect('admin/course-category/')->with('deleted','Something went wrong.');     
               }

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
            'title' => 'required|string'
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

            $name = 'category_'.time().$file->getClientOriginalName(); 
            $file->move('images/subjectcategory/', $name);
            $topic_img = $name;
        }
        else{
        	$topic_img="";
        }

        try{
        $subjectcategorydata=Subjectcategory::where('category_name',$request->title)->first();
        if($subjectcategorydata)
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
                $subjectcategory = new Subjectcategory;
                $subjectcategory->subject = $request->course;
                $subjectcategory->category_name = $request->title;
                $subjectcategory->category_description = $request->description;
                $subjectcategory->category_image = $topic_img;
                $subjectcategory->category_status = $statusvalue;
                $subjectcategory->save();

               return redirect('admin/course-category/')->with('success','Topic has been added.');

		        }catch(\Exception $e){
		          return back()->with('error',$e->getMessage());     
		       }
        }
    }
    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

        }catch(\Exception $e){
                  
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

	     
	     $subjectcategory = Subjectcategory::findOrFail($id);
	     $subjectcategory->title=$subjectcategory->category_name;
	     $subjectcategory->description=$subjectcategory->category_description;
	     return view('admin.subjectcategory.edit',compact('subjectcategory','subjectlist'));
     }
     catch(\Exception $e){
                  return redirect('admin/course-category/')->with('deleted','Something went wrong.');     
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
          'title' => 'required|string'
        ]);

          $subjectcategory = Subjectcategory::find($id);

         if(is_null($subjectcategory)){
		   return redirect('admin/course-category')->with('error','Something went wrong.');
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
            $file->move('images/subjectcategory/', $name);
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


          if($subjectcategory->category_name==$request->title)
          {
	          if($topic_img!="")
	          {
	          	$subjectcategory->subject = $request->course;
  		        $subjectcategory->category_description = $request->description;
  		        $subjectcategory->category_image = $topic_img;
  		        $subjectcategory->category_status = $statusvalue;
	          }
	          else{
	          	$subjectcategory->subject = $request->course;
  		        $subjectcategory->category_description = $request->description;
  		        $subjectcategory->category_status = $statusvalue;
	          }
          }
          else{
          		try{
		        $subjectcategorydata=Subjectcategory::where('category_name',$request->title)->first();
		        if($subjectcategorydata)
		        {
		        	return back()->with('error','Title already exists.');
		        }
		    }
		    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

            if($topic_img!="")
	          {
	          	$subjectcategory->subject = $request->course;
	          	$subjectcategory->category_name=$request->title;
  		        $subjectcategory->category_description = $request->description;
  		        $subjectcategory->category_image = $topic_img;
  		        $subjectcategory->category_status = $statusvalue;
	          }
	          else{
	          	$subjectcategory->subject = $request->course;
	          	$subjectcategory->category_name=$request->title;
		          $subjectcategory->category_description = $request->description;
		          $subjectcategory->category_status = $statusvalue;
	          }
          }
         try{
            $subjectcategory->save();

          return redirect('admin/course-category/')->with('success','Topic updated !.');

         }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

       }catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                        $listmessage="";
                        foreach($e->errors() as $list)
                        {
                            $listmessage.=$list[0].'<br>';
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
        $subjectcategory = Subjectcategory::find($id);

        if(is_null($subjectcategory)){
		   return redirect('admin/course-category')->with('error','Something went wrong.');
		}

        try{
            $subjectcategory->delete();
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
        $subjectcategory = Subjectcategory::find($id);

        if(is_null($subjectcategory)){
		   return redirect('admin/course-category')->with('error','Something went wrong.');
		}

        if(isset($request->status)){
            $subjectcategory->category_status = 1;
          }else{
            $subjectcategory->category_status = 0;
        }

        try{
            $subjectcategory->save();
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
