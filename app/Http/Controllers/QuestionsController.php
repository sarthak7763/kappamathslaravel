<?php

namespace App\Http\Controllers;

use App\Imports\QuestionsImport;
use Illuminate\Http\Request;
use App\Topic;
use App\Question;
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
        $topics = Topic::all();
        $questions = Question::all();
        return view('admin.questions.index', compact('questions', 'topics'));
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
    public function store(Request $request)
    {

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
            $input['question_img'] = $name;

        }
        

        try{
          Question::create($input);
          return back()->with('added', 'Question has been added');
        }catch(\Exception $e){
           return back()->with('deleted',$e->getMessage());
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
        $topic = Topic::findOrFail($id);
        
        $questions = \DB::table('questions')->where('topic_id', $topic->id)->select('id','question','a','b','c','d','e','f','answer');

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
          ->addColumn('e',function($row){
              return $row->e;
          })
          ->addColumn('f',function($row){
              return $row->f;
          })
          ->addColumn('answer',function($row){
              return $row->answer;
          })

          ->addColumn('action', function($row){

              $btn = '<div class="admin-table-action-block">

                  <a href="' . route('questions.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>
                
                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal' . $row->id . '"><i class="fa fa-trash"></i> </button></div>';

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
                      <form method="POST" action="' . route("questions.destroy", $row->id) . '">
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
          ->rawColumns(['question','a','b','c','d','e','f','answer','action'])
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
        $question = Question::find($id);
        $topic = Topic::where('id',$question->topic_id)->first();
       return view('admin.questions.edit',compact('question','topic'));
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
        $question = Question::find($id);
        $request->validate([
          'topic_id' => 'required',
          'question' => 'required',
          'a' => 'required',
          'b' => 'required',
          'c' => 'required',
          'd' => 'required',
          'answer' => 'required',
        ]);

        $input = $request->all();

        if ($file = $request->file('question_img')) {

            $name = 'question_'.time().$file->getClientOriginalName();

            if($question->question_img != null) {
                unlink(public_path().'/images/questions/'.$question->question_img);
            }

            $file->move('images/questions/', $name);
            $input['question_img'] = $name;

        }

        try
        {
          $question->update($input);
          return back()->with('updated', 'Question has been updated');
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
}
