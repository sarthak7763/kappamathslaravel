<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Avatar;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Validator;
use App\Result;
use App\Resultmarks;
use App\Subject;
use App\Subjectcategory;
use App\Coursetopic;
use App\Quiztopic;
use App\Question;
use Illuminate\Support\Str;

class UsersController extends Controller
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

          $users = \DB::table('users')->where('role','!=' , 'A')->where('created_at','>=',$filter_start_date)->where('created_at','<=',$filter_end_date)->select('id','name','email','username','mobile','role','status');
       }
       elseif($request->filter_start_date!="" && $request->filter_end_date=="")
       {
          $filter_start_date=date('Y-m-d',strtotime($request->filter_start_date));

          $users = \DB::table('users')->where('role','!=' , 'A')->where('created_at','>=',$filter_start_date)->select('id','name','email','username','mobile','role','status');
       }
       elseif($request->filter_start_date=="" && $request->filter_end_date!="")
       {
          $filter_end_date=date('Y-m-d',strtotime($request->filter_end_date));
          $filter_end_date=date('Y-m-d', strtotime("+1 day", strtotime($filter_end_date)));

          $users = \DB::table('users')->where('role','!=' , 'A')->where('created_at','<=',$filter_end_date)->select('id','name','email','username','mobile','role','status');
       }
       else{
        $users = \DB::table('users')->where('role','!=' , 'A')->select('id','name','email','username','mobile','role','status');
       }
        

        if($request->ajax()){
          return DataTables::of($users)
          
          ->addIndexColumn()
          ->addColumn('name',function($row){
            return ucfirst($row->name);
          })
          ->addColumn('email',function($row){
            return $row->email;
          })
          ->addColumn('username',function($row){
            return $row->username;
          })
          ->addColumn('mobile',function($row){
              return $row->mobile;
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

            $btn ='<div class="admin-table-action-block">';

                  if($row->role=="S")
                  {
                    $btn.='
                    <a href="' . route('users.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>';

                    $btn.='
                    <a href="'.url('/admin/users/result/' . $row->username ).'" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating">Result</a>';
                  }
                  else{
                    $btn.='
                    <a href="' . route('profile') . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>';
                  }
                  
                  if($row->role=="S")
                  {
                    $btn.='<button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';
                  }


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
                //         <form method="POST" action="' . route("users.destroy", $row->id) . '">
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

                       <form method="POST" action="' . route("userchangestatus") . '">
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
          ->rawColumns(['name','email','username','mobile','status','action'])
          ->make(true);
        }
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

      return view('admin.users.create');
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

          $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'username'=>'required'
          ]);

          try{
          $checkmail=User::where('email',$request->email)->get()->first();
          if($checkmail)
          {
              return back()->with('error', 'Email already exists. Please try with another email.');
          }
        }
        catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

               try{
               $checkusername=User::where('username',$request->username)->get()->first();
               if($checkusername)
               {
                  return back()->with('error', 'Username already exists. Please try with another Username.');
               }
             }
             catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

               if(isset($request->status)){
                $userstatus = "1";
              }else{
                $userstatus = "0";
              }

          $user = new User;
          $user->name = $request->name;
          $user->email = $request->email;
          $user->mobile = $request->mobile;
          $user->username=$request->username;
          $user->address="";
          $user->role = 'S';
          $user->city="";
          $user->status=$userstatus;

          if($request->password !="")
          {
            $user->password = bcrypt($request->password);
          }

            if ($file = $request->file('image')) {
                $name = 'user_'.time(); 
                $file->move('images/user/', $name);
                $image = $name;
            }
            else{
                $image="";
            }

            $user->image = $image;

          try{
            $user->save();
            return redirect('/admin/users/')->with('success', 'User has been added !');

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
      $user = User::findOrFail($id);
      return view('admin.users.edit',compact('user'));
      }
      catch(\Exception $e){
                  return redirect('admin/users/')->with('error','Something went wrong.');     
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
          'name' => 'required|string',
          'email' => 'required|string|email'
        ]);

        $user = User::find($id);
          if(is_null($user)){
           return redirect('admin/users/')->with('error','Something went wrong.');
        }

        $input = $request->all();

        if(isset($request->status)){
          $userstatus = "1";
        }else{
          $userstatus = "0";
        }


        if ($file = $request->file('image')) {
                $name = 'user_'.time(); 
                $file->move('images/user/', $name);
                $image = $name;
            }
            else{
                $image="";
            }

        if($user->role=="A")
        {
          if($user->email==$request->email)
          {
            if($image!="")
            {
              $user->name = $request->name;
              $user->mobile = $request->mobile;
              $user->address = "";
              $user->city = "";
              $user->image = $image;
              $user->status=$userstatus;

            }
            else{
              $user->name = $request->name;
              $user->mobile = $request->mobile;
              $user->address = "";
              $user->city = "";
              $user->status=$userstatus;
            }
            
          }
          else
          {
              try{
            $checkmail=User::where('email',$request->email)->get()->first();
            if($checkmail)
            {
                return back()->with('error', 'Email already exists. Please try with another email.');
            }
          }
          catch(\Exception $e){
                    return back()->with('error','Something went wrong.');     
                 }


            if($image!="")
            {
                $user->name = $request->name;
                $user->mobile = $request->mobile;
                $user->address = "";
                $user->city = "";
                $user->email=$request->email;
                $user->image = $image;
                $user->status=$userstatus;
            }
            else{
                $user->name = $request->name;
                $user->mobile = $request->mobile;
                $user->address = "";
                $user->city = "";
                $user->email=$request->email;
                $user->status=$userstatus;
            } 

          }
        }
        else
        {
          if($user->email==$request->email && $user->username==$request->username)
          {
            if($image!="")
            {
              $user->name = $request->name;
              $user->mobile = $request->mobile;
              $user->address = "";
              $user->city = "";
              $user->image = $image;
              $user->status=$userstatus;
            }
            else{
              $user->name = $request->name;
              $user->mobile = $request->mobile;
              $user->address = "";
              $user->city = "";
              $user->status=$userstatus;
            }
            
          }
          elseif($user->email!=$request->email && $user->username==$request->username)
          {
              try{
            $checkmail=User::where('email',$request->email)->get()->first();
            if($checkmail)
            {
                return back()->with('error', 'Email already exists. Please try with another email.');
            }
          }
          catch(\Exception $e){
                    return back()->with('error','Something went wrong.');     
                 }


            if($image!="")
            {
                $user->name = $request->name;
                $user->mobile = $request->mobile;
                $user->address = "";
                $user->city = "";
                $user->email=$request->email;
                $user->image = $image;
                $user->status=$userstatus;
            }
            else{
                $user->name = $request->name;
                $user->mobile = $request->mobile;
                $user->address = "";
                $user->city = "";
                $user->email=$request->email;
                $user->status=$userstatus;
            }
          }
          elseif($user->email==$request->email && $user->username!=$request->username)
          {
              try{
                 $checkusername=User::where('username',$request->username)->get()->first();
                 if($checkusername)
                 {
                    return back()->with('error', 'Username already exists. Please try with another Username.');
                 }
               }
               catch(\Exception $e){
                    return back()->with('error','Something went wrong.');     
                 }

                 if($image!="")
                 {
                    $user->name = $request->name;
                    $user->mobile = $request->mobile;
                    $user->address = "";
                    $user->city = "";
                    $user->username=$request->username;
                    $user->image = $image;
                    $user->status=$userstatus;
                 }
                 else{
                      $user->name = $request->name;
                      $user->mobile = $request->mobile;
                      $user->address = "";
                      $user->city = "";
                      $user->username=$request->username;
                      $user->status=$userstatus;
                 } 
          }
            else{
                try{
            $checkmail=User::where('email',$request->email)->get()->first();
            if($checkmail)
            {
                return back()->with('error', 'Email already exists. Please try with another email.');
            }
          }
          catch(\Exception $e){
                    return back()->with('error','Something went wrong.');     
                 }

                 try{
                 $checkusername=User::where('username',$request->username)->get()->first();
                 if($checkusername)
                 {
                    return back()->with('error', 'Username already exists. Please try with another Username.');
                 }
               }
               catch(\Exception $e){
                    return back()->with('error','Something went wrong.');     
                 }

              if($image!="")
              {
                  $user->name = $request->name;
                  $user->mobile = $request->mobile;
                  $user->address = "";
                  $user->city = "";
                  $user->username=$request->username;
                  $user->email = $request->email;
                  $user->image = $image;
                  $user->status=$userstatus;
              } 
              else{
                  $user->name = $request->name;
                  $user->mobile = $request->mobile;
                  $user->address = "";
                  $user->city = "";
                  $user->username=$request->username;
                  $user->email = $request->email;
                  $user->status=$userstatus;
              }  
          }
        }

          if($request->password !="")
          {
            $user->password = bcrypt($request->password);
          }

          try{
            $user->save();

            if($user->role=="S")
            {
              return redirect('/admin/users')->with('success', 'User has been updated !');
            }
            else{
              return redirect('/admin/profile')->with('success', 'Profile has been updated !');
            }
            

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

    public function updateprofile(Request $request, $id)
    {
        try{
        $request->validate([
          'name' => 'required|string',
          'email' => 'required|string|email'
        ]);

        $user = User::find($id);
          if(is_null($user)){
           return redirect('admin/users/')->with('error','Something went wrong.');
        }

        $input = $request->all();

        if ($file = $request->file('image')) {
                $name = 'user_'.time(); 
                $file->move('images/user/', $name);
                $image = $name;
            }
            else{
                $image="";
            }

        if($user->email==$request->email)
        {
          if($image!="")
          {
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->address = "";
            $user->city = "";
            $user->image = $image;
            $user->username="";
          }
          else{
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->address = "";
            $user->city = "";
            $user->username="";
          }
          
        }
        else
        {
            try{
          $checkmail=User::where('email',$request->email)->get()->first();
          if($checkmail)
          {
              return back()->with('error', 'Email already exists. Please try with another email.');
          }
        }
        catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }


          if($image!="")
          {
              $user->name = $request->name;
              $user->mobile = $request->mobile;
              $user->address = "";
              $user->city = "";
              $user->email=$request->email;
              $user->image = $image;
              $user->username="";
          }
          else{
              $user->name = $request->name;
              $user->mobile = $request->mobile;
              $user->address = "";
              $user->city = "";
              $user->email=$request->email;
              $user->username="";
          }

        }

          if($request->password !="")
          {
            $user->password = bcrypt($request->password);
          }

          try{
            $user->save();
            return redirect('/admin/users')->with('success', 'User has been updated !');

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
        $user = User::find($id);

        if(is_null($user)){
           return redirect('admin/users')->with('error','Something went wrong.');
        }
        
        if($user->image !=''){
          unlink('images/user/'.$user->image);
        }
        try{
          $user->delete();
          return back()->with('success', 'User has been deleted');
        }catch(\Exception $e){
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
        $user = User::find($id);

        if(is_null($user)){
           return redirect('admin/users')->with('error','Something went wrong.');
        }


        if(isset($request->status)){
            $user->status = 1;
          }else{
            $user->status = 0;
        }

        try{
            $user->save();
           return back()->with('success','User updated !');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }


    }

    public function userresult($id)
    {
      try{
          $userdet = User::where('username',$id)->get()->first();

        if($userdet){
           $userdetarr=$userdet->toArray();
           $userid=$userdetarr['id'];
           $name=$userdetarr['name'];
           $username=$userdetarr['username'];
        }
        else{
          return redirect('admin/users')->with('error','Something went wrong.');
        }

        $quizresultmarks=Resultmarks::where('user_id',$userid)->orderBy('result_marks_date','desc')->get();
        if($quizresultmarks)
        {
          $quizresultmarksarray=$quizresultmarks->toArray();

          $user_result=[];
          foreach($quizresultmarksarray as $list)
          {
            $random_question_idsdb=$list['random_question_ids'];

            $quiz_topiciddb=$list['topic_id'];
              $searchForValue = ',';
            if( strpos($quiz_topiciddb, $searchForValue) !== false ) {
                 $quiz_topiciddbarray=explode(',',$quiz_topiciddb);
            }
            else{
              $quiz_topiciddbarray=array($quiz_topiciddb);
            }


            $sub_topic_titlearr=[];
            $topic_tile_arr=[];
            foreach($quiz_topiciddbarray as $arrid)
            {
              $quizdet=Quiztopic::where('id',$arrid)->get()->first();
              if($quizdet)
              {
                $quizdetarr=$quizdet->toArray();
                $course_topicid=$quizdetarr['course_topic'];
                $category=$quizdetarr['category'];

                $coursetopicdet=Coursetopic::where('id',$course_topicid)->get()->first();
                if($coursetopicdet)
                {
                  $coursetopicarr=$coursetopicdet->toArray();
                  $sub_topic_titlearr[]=$coursetopicarr['topic_name'];
                }

                $subjectcategorydet=Subjectcategory::where('id',$category)->get()->first();
                if($subjectcategorydet)
                {
                  $subjectcategoryarr=$subjectcategorydet->toArray();
                  $topic_tile_arr[]=$subjectcategoryarr['category_name'];
                }
              }
            }

          if(count($sub_topic_titlearr) > 0)
          {
            $sub_topic_title=implode(',',$sub_topic_titlearr);
          }
          else{
            $sub_topic_title="";
          }

          if(count($topic_tile_arr) > 0)
          {
            $topic_title=implode(',',$topic_tile_arr);
          }
          else{
            $topic_title="";
          }


          $random_question_idsarray=json_decode($random_question_idsdb,true);
              $random_question_ids=[];
              foreach($random_question_idsarray as $listval)
              {
                foreach($listval as $rowval)
                {
                  $random_question_ids[]=$rowval;
                }
              }

              $attempt_question_idsdb=$list['question_ids'];
              if($attempt_question_idsdb!="")
              {
                $attempt_question_idsarray=json_decode($attempt_question_idsdb,true);

                $attempt_question_ids=[];
                foreach($attempt_question_idsarray as $listvalnew)
                {
                  foreach($listvalnew as $rowvalnew)
                  {
                    $attempt_question_ids[]=$rowvalnew;
                  }
                }

                if(count($attempt_question_ids) > 0)
                {
                  $questions_array_diff=array_values(array_diff($random_question_ids, $attempt_question_ids));

                  if(count($questions_array_diff) == 0)
                  {
                    $quizcorrectresultdetail=Result::where('user_id',$userid)->where('result_marks_id',$list['id'])->where('answer','1')->get();

                    if($quizcorrectresultdetail)
                    {
                      $correct_questions=$quizcorrectresultdetail->count();
                    }
                    else{
                      $correct_questions=0;
                    }

                    $quizincorrectresultdetail=Result::where('user_id',$userid)->where('result_marks_id',$list['id'])->where('answer','2')->get();

                    if($quizincorrectresultdetail)
                    {
                      $incorrect_questions=$quizincorrectresultdetail->count();
                    }
                    else{
                      $incorrect_questions=0;
                    }


                    $quizskipresultdetail=Result::where('user_id',$userid)->where('result_marks_id',$list['id'])->where('answer','0')->get();

                    if($quizskipresultdetail)
                    {
                      $skip_questions=$quizskipresultdetail->count();
                    }
                    else{
                      $skip_questions=0;
                    }

                    $total_marks=$list['total_marks'];
                    $result_marks=$list['marks'];

                    if($total_marks==0)
                    {
                      $total_score=0;
                    }
                    else{
                      $total_score=round(($result_marks/$total_marks)*100);
                    }

                    $result_marks_date=date('d M, Y',strtotime($list['result_marks_date']));

                    $user_result[]=array(
                    'sub_topic_title'=>$sub_topic_title,
                    'topic_title'=>$topic_title,
                    'total_questions'=>$list['total_questions'],
                    'correct_questions'=>$correct_questions,
                    'incorrect_questions'=>$incorrect_questions,
                    'skip_questions'=>$skip_questions,
                    'total_score'=>$total_score,
                    'total_time'=>$list['result_timer'],
                    'result_date'=>$result_marks_date,
                    'result_id'=>(int)$list['id'],
                    'result_type'=>$list['result_type']
                  );
                    
                  }
                }
              }

          }
        }
        else{
          $user_result=[];
        }

        return view('admin.users.resultuser',compact('user_result','name','username'));

      }
      catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
    }

}
