<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Theoryexcelinstructions;
use Yajra\Datatables\DataTables;
use Exception;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class TheoryExcelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  
      $theoryexcelinsdata = \DB::table('theory_excel_instructions')->select('id','quiz_id','question','answer_explaination','question_image','question_video_link','answer_explaination_image','answer_explaination_video_link');

          if($request->ajax()){

            return DataTables::of($theoryexcelinsdata)
            ->addIndexColumn()
            ->addColumn('quiz_id',function($row){
                return $row->quiz_id;
            })
            ->addColumn('question',function($row){
                return $row->question;
            })
            ->addColumn('answer_explaination',function($row){
                return $row->answer_explaination;
            })
            ->addColumn('question_image',function($row){
                return $row->question_image;
            })
            ->addColumn('question_video_link',function($row){
                return $row->question_video_link;
            })
            ->addColumn('answer_explaination_image',function($row){
                return $row->answer_explaination_image;
            })
            ->addColumn('answer_explaination_video_link',function($row){
                return $row->answer_explaination_video_link;
            })

            ->addColumn('action',function($row){

              $btn = '<div class="admin-table-action-block">

                    <a href="' . route('theory-excel-instructions.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

                    <button type="button" class="btn btn-danger deletebtn" data-toggle="modal" data-target="#deleteModal' . $row->id . '">Delete </button></div>';

                         $btn .= '<div id="deleteModal' . $row->id . '" class="delete-modal modal fade" role="dialog">
                  <div class="modal-dialog modal-sm">
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="delete-icon"></div>
                      </div>
                      <div class="modal-body text-center">
                        <h4 class="modal-heading">Are You Sure ?</h4>
                        <p>Do you really want to delete these records? This process cannot be undone.</p>
                      </div>
                      <div class="modal-footer">
                        <form method="POST" action="' . route("theory-excel-instructions.destroy", $row->id) . '">
                          ' . method_field("DELETE") . '
                          ' . csrf_field() . '
                            <button type="reset" class="btn btn-gray translate-y-3" data-dismiss="modal">No</button>
                            <button type="submit" class="btn btn-danger">Yes</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>';


              return $btn;
            })
            ->escapeColumns(['action'])
            ->rawColumns(['quiz_id','question','answer_explaination','question_image','question_video_link','answer_explaination_image','answer_explaination_video_link','action'])
            ->make(true);

          }

        return view('admin.theoryexcel.index', compact('theoryexcelinsdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.theoryexcel.create');
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
            'quiz_id' => 'required',
            'question' => 'required',
            'answer_explaination' => 'required',
            'question_image' => 'required',
            'question_video_link' => 'required',
            'answer_explaination_image' => 'required',
            'answer_explaination_video_link' => 'required'
          ]);

        try{
		      $theoryexceldata = Theoryexcelinstructions::create($input);
		    return redirect('/admin/theory-excel-instructions/')->with('success', 'Instructions has been added');
		    }catch(\Exception $e){
		          return back()->with('error',$e->getMessage());     
		       }

    }catch(\Exception $e){
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
            $theoryexceldata = Theoryexcelinstructions::findOrFail($id);
           return view('admin.theoryexcel.edit',compact('theoryexceldata'));
        }
        catch(\Exception $e){
                  return redirect('admin/theory-excel-instructions/')->with('error','Something went wrong.');     
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
            'quiz_id' => 'required',
            'question' => 'required',
            'answer_explaination' => 'required',
            'question_image' => 'required',
            'question_video_link' => 'required',
            'answer_explaination_image' => 'required',
            'answer_explaination_video_link' => 'required'
          ]);

          $theoryexceldata = Theoryexcelinstructions::find($id);
          if(is_null($theoryexceldata)){
           return redirect('admin/theory-excel-instructions')->with('error','Something went wrong.');
        }

        $theoryexceldata->quiz_id = $request->quiz_id;
        $theoryexceldata->question = $request->question;
        $theoryexceldata->answer_explaination = $request->answer_explaination;
        $theoryexceldata->question_image = $request->question_image;
        $theoryexceldata->question_video_link = $request->question_video_link;
        $theoryexceldata->answer_explaination_image = $request->answer_explaination_image;
        $theoryexceldata->answer_explaination_video_link = $request->answer_explaination_video_link;

         try{
            $theoryexceldata->save();
          return redirect('admin/theory-excel-instructions/')->with('success','Instructions updated !');
         }catch(\Exception $e){
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
        $theoryexceldata = Theoryexcelinstructions::find($id);

        if(is_null($theoryexceldata)){
           return redirect('admin/theory-excel-instructions')->with('error','Something went wrong.');
        }

        try{
            $theoryexceldata->delete();
           return back()->with('success', 'Instructions has been deleted');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }
     }
     catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
        
    }

}
