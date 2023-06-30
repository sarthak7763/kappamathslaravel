<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Objectiveexcelinstructions;
use Yajra\Datatables\DataTables;
use Exception;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;
use Illuminate\Validation\Rule;

class ObjectiveExcelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  
      $objectiveexcelinsdata = \DB::table('objective_excel_instructions')->select('id','quiz_id','question','a','b','c','d','correct_answer','answer_explaination','question_image','question_video_link','answer_explaination_image','answer_explaination_video_link');

          if($request->ajax()){

            return DataTables::of($objectiveexcelinsdata)
            ->addIndexColumn()
            ->addColumn('quiz_id',function($row){
                return $row->quiz_id;
            })
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
            ->addColumn('correct_answer',function($row){
                return $row->correct_answer;
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

                    <a href="' . route('objective-excel-instructions.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

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
                        <form method="POST" action="' . route("objective-excel-instructions.destroy", $row->id) . '">
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
            ->rawColumns(['quiz_id','question','a','b','c','d','correct_answer','answer_explaination','question_image','question_video_link','answer_explaination_image','answer_explaination_video_link','action'])
            ->make(true);

          }

        return view('admin.objectiveexcel.index', compact('objectiveexcelinsdata'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.objectiveexcel.create');
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

        if($request->checkboxvalue)
        {
          $option_status=1;
        }
        else{
          $option_status=0;
        }

          $input = $request->all();
          $request->validate([
            'quiz_id' => 'required',
            'question' => 'required',
            'a'=>Rule::requiredIf($option_status==0),
            'b'=>Rule::requiredIf($option_status==0),
            'c'=>Rule::requiredIf($option_status==0),
            'd'=>Rule::requiredIf($option_status==0),
            'correct_answer'=>'required',
            'answer_explaination' => 'required',
            'question_image' => 'required',
            'question_video_link' => 'required',
            'answer_explaination_image' => 'required',
            'answer_explaination_video_link' => 'required',
            'a_image'=>Rule::requiredIf($option_status==1),
            'b_image'=>Rule::requiredIf($option_status==1),
            'c_image'=>Rule::requiredIf($option_status==1),
            'd_image'=>Rule::requiredIf($option_status==1),
          ]);

        try{

            $input['option_status']=$option_status;
		      $objectiveexceldata = Objectiveexcelinstructions::create($input);
		    return redirect('/admin/objective-excel-instructions/')->with('success', 'Instructions has been added');
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
            $objectiveexceldata = Objectiveexcelinstructions::findOrFail($id);
           return view('admin.objectiveexcel.edit',compact('objectiveexceldata'));
        }
        catch(\Exception $e){
                  return redirect('admin/objective-excel-instructions/')->with('error','Something went wrong.');     
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

          $request->validate([
            'quiz_id' => 'required',
            'question' => 'required',
            'a'=>Rule::requiredIf($option_status==0),
            'b'=>Rule::requiredIf($option_status==0),
            'c'=>Rule::requiredIf($option_status==0),
            'd'=>Rule::requiredIf($option_status==0),
            'correct_answer'=>'required',
            'answer_explaination' => 'required',
            'question_image' => 'required',
            'question_video_link' => 'required',
            'answer_explaination_image' => 'required',
            'answer_explaination_video_link' => 'required',
            'a_image'=>Rule::requiredIf($option_status==1),
            'b_image'=>Rule::requiredIf($option_status==1),
            'c_image'=>Rule::requiredIf($option_status==1),
            'd_image'=>Rule::requiredIf($option_status==1),
          ]);

          $objectiveexceldata = Objectiveexcelinstructions::find($id);
          if(is_null($objectiveexceldata)){
           return redirect('admin/objective-excel-instructions')->with('error','Something went wrong.');
        }

        if($option_status==0)
        {
            $objectiveexceldata->a = $request->a;
            $objectiveexceldata->b = $request->b;
            $objectiveexceldata->c = $request->c;
            $objectiveexceldata->d = $request->d;

            $objectiveexceldata->a_image = "";
            $objectiveexceldata->b_image = "";
            $objectiveexceldata->c_image = "";
            $objectiveexceldata->d_image = "";
        }
        else{
            $objectiveexceldata->a = "";
            $objectiveexceldata->b = "";
            $objectiveexceldata->c = "";
            $objectiveexceldata->d = "";

            $objectiveexceldata->a_image = $request->a_image;
            $objectiveexceldata->b_image = $request->b_image;
            $objectiveexceldata->c_image = $request->c_image;
            $objectiveexceldata->d_image = $request->d_image;
        }

        $objectiveexceldata->quiz_id = $request->quiz_id;
        $objectiveexceldata->question = $request->question;
        $objectiveexceldata->option_status=$option_status;
        $objectiveexceldata->correct_answer = $request->correct_answer;
        $objectiveexceldata->answer_explaination = $request->answer_explaination;
        $objectiveexceldata->question_image = $request->question_image;
        $objectiveexceldata->question_video_link = $request->question_video_link;
        $objectiveexceldata->answer_explaination_image = $request->answer_explaination_image;
        $objectiveexceldata->answer_explaination_video_link = $request->answer_explaination_video_link;

         try{
            $objectiveexceldata->save();
          return redirect('admin/objective-excel-instructions/')->with('success','Instructions updated !');
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
        $objectiveexceldata = Objectiveexcelinstructions::find($id);

        if(is_null($objectiveexceldata)){
           return redirect('admin/objective-excel-instructions')->with('error','Something went wrong.');
        }

        try{
            $objectiveexceldata->delete();
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
