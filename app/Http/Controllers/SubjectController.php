<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subject;
use Yajra\Datatables\DataTables;
use Exception;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $subject = \DB::table('subject')->select('id','title','description','status');

          if($request->ajax()){

            return DataTables::of($subject)

            ->filter(function ($row) use ($request) { 
            if ($request->input('search.value') != "") {
                $search=$request->input('search.value');
                $row->where('title', 'LIKE', '%'.$search.'%');
            }
        })

            ->addIndexColumn()
            ->addColumn('title',function($row){
                return $row->title;
            })
            ->addColumn('description',function($row){
                return $row->description;
            })
            ->addColumn('status',function($row){

            if($row->status=="1")
            {
                $statusvalue="Active";
            }
            else{
                $statusvalue="suspend";
            }

                return $statusvalue;
            })

            ->addColumn('action',function($row){

            if($row->status=="1")
            {
                $checked="checked";
            }
            else{
                $checked="";
            }

              $btn = '<div class="admin-table-action-block">

                    <a href="' . route('subject.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

                    <button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';
                   

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
                //         <form method="POST" action="' . route("category.destroy", $row->id) . '">
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

                       <form method="POST" action="' . route("subjectchangestatus") . '">
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
            ->rawColumns(['title','description','status','action'])
            ->make(true);

          }

        return view('admin.subject.index', compact('subject'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
          'title' => 'required|string',
          'image' => 'required|mimes:jpeg,png,jpg'
        ]);

        if(isset($request->status)){
          $input['status'] = "1";
        }else{
          $input['status'] = "0";
        }


        if ($file = $request->file('image')) {
            $name = 'subject_'.time().$file->getClientOriginalName(); 
            $file->move('images/subjects/', $name);
            $image = $name;
        }
        else{
            $image="";
        }

        $input['image']=$image;

        try{
        $subjectdata=Subject::where('title',$request->title)->first();
        if($subjectdata)
        {
        	return back()->with('deleted','Title already exists.');
        }
        else{
        	try{
		         $quiz = Subject::create($input);
		           return back()->with('added', 'Course has been added');
		        }catch(\Exception $e){
		          return back()->with('deleted',$e->getMessage());     
		       }
        }
    }
    catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
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
                            return back()->with('deleted','Something went wrong.');
                        }
                        
                    }
                    else{
                        return back()->with('deleted','Something went wrong.');
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
            $subject = Subject::findOrFail($id);
           return view('admin.subject.edit',compact('subject'));
        }
        catch(\Exception $e){
                  return redirect('admin/subject/')->with('deleted','Something went wrong.');     
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
          'title' => 'required|string'
        ]);

          $subject = Subject::find($id);
          if(is_null($subject)){
           return redirect('admin/subject')->with('deleted','Something went wrong.');
        }

        if(isset($request->status)){
            $statusvalue = 1;
          }else{
            $statusvalue = 0;
          }

        if ($file = $request->file('image')) {
            $name = 'subject_'.time().$file->getClientOriginalName(); 
            $file->move('images/subjects/', $name);
            $subjectimage = $name;
        }
        else{
            $subjectimage="";
        }

        if($subject->title==$request->title)
        {
            if($subjectimage!="")
            {
                $subject->description = $request->description;
                $subject->status=$statusvalue;
                $subject->image=$subjectimage;
            }
            else{
                $subject->description = $request->description;
                $subject->status=$statusvalue;
            }
        }
        else{
            try{
                $subjectdata=Subject::where('title',$request->title)->first();
                if($subjectdata)
                {
                    return back()->with('deleted','Title already exists.');
                }
            }
            catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }

            if($subjectimage!="")
            {
                $subject->title=$request->title;
                $subject->description = $request->description;
                $subject->status=$statusvalue;
                $subject->image=$subjectimage;
            }
            else{
                $subject->title=$request->title;
                $subject->description = $request->description;
                $subject->status=$statusvalue;
            }
        } 

         try{
            $subject->save();
          return back()->with('updated','Course updated !');
         }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
         }

     }
     catch(\Exception $e){          
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
                        return back()->with('deleted','Something went wrong.');
                    }
                    
                }
                else{
                    return back()->with('deleted','Something went wrong.');
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
        $subject = Subject::find($id);

        if(is_null($subject)){
           return redirect('admin/subject')->with('deleted','Something went wrong.');
        }

        try{
            $subject->delete();
           return back()->with('deleted', 'Course has been deleted');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
         }
     }
     catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }
        
    }

    public function changestatus(Request $request)
    {
        try{
        $id=$request->id;
        $subject = Subject::find($id);

        if(is_null($subject)){
           return redirect('admin/subject')->with('deleted','Something went wrong.');
        }


        if(isset($request->status)){
            $subject->status = 1;
          }else{
            $subject->status = 0;
        }

        try{
            $subject->save();
           return back()->with('updated','Course updated !');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }


    }
}
