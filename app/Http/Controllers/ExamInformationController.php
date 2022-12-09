<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Examinformation;
use Yajra\Datatables\DataTables;
use Exception;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class ExamInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $examinformation = \DB::table('exam_information')->select('id','question','answer','status');

          if($request->ajax()){

            return DataTables::of($examinformation)
            ->addIndexColumn()
            ->addColumn('question',function($row){
                return $row->question;
            })
            ->addColumn('answer',function($row){
                return $row->answer;
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

                    <a href="' . route('exam-information.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

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

                       <form method="POST" action="' . route("examinformationchangestatus") . '">
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
            ->rawColumns(['question','answer','status','action'])
            ->make(true);

          }

        return view('admin.examinformation.index', compact('examinformation'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('admin.examinformation.create');
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
          'question' => 'required',
          'answer'=>'required'
        ]);

        if(isset($request->status)){
          $input['status'] = "1";
        }else{
          $input['status'] = "0";
        }

        try{
        $examinformationdata=Examinformation::where('question',$request->question)->first();
        if($examinformationdata)
        {
        	return back()->with('error','Exam Information already exists.');
        }
        else{
        	try{
		         $quiz = Examinformation::create($input);
		           return redirect('/admin/exam-information/')->with('success', 'Exam Information has been added');
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

            $examinformation = Examinformation::findOrFail($id);
           return view('admin.examinformation.edit',compact('examinformation'));
        }
        catch(\Exception $e){
                  return redirect('admin/exam-information/')->with('error','Something went wrong.');     
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
          'question' => 'required',
          'answer'=>'required'
        ]);

          $examinformation = Examinformation::find($id);
          if(is_null($examinformation)){
           return redirect('admin/exam-information')->with('error','Something went wrong.');
        }

        if(isset($request->status)){
            $statusvalue = 1;
          }else{
            $statusvalue = 0;
          }

        if($examinformation->question==$request->question)
        {
            $examinformation->answer = $request->answer;
            $examinformation->status=$statusvalue;
        }
        else{
            try{
                $examinformationdata=Examinformation::where('question',$request->question)->first();
                if($examinformationdata)
                {
                    return back()->with('error','Exam Information already exists.');
                }
            }
            catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

            $examinformation->question=$request->question;
            $examinformation->answer = $request->answer;
            $examinformation->status=$statusvalue;
        } 

         try{
            $examinformation->save();
          return redirect('admin/exam-information/')->with('success','Exam Information updated !');
         }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
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
        $examinformation = Examinformation::find($id);

        if(is_null($examinformation)){
           return redirect('admin/exam-information')->with('error','Something went wrong.');
        }

        try{
            $examinformation->delete();
           return back()->with('success', 'Exam Information has been deleted');
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
        $examinformation = Examinformation::find($id);

        if(is_null($examinformation)){
           return redirect('admin/exam-information')->with('error','Something went wrong.');
        }


        if(isset($request->status)){
            $examinformation->status = 1;
          }else{
            $examinformation->status = 0;
        }

        try{
            $examinformation->save();
           return back()->with('success','Exam Information updated !');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }


    }
}
