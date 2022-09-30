<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Avatar;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\ValidationException;
use Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        $users = \DB::table('users')->where('role','!=' , 'A')->select('id','image','name','email','username','mobile','role','status');

        if($request->ajax()){
          return DataTables::of($users)

          ->filter(function ($row) use ($request) { 
            if ($request->input('search.value') != "") {
                $search=$request->input('search.value');
                $row->where('name', 'LIKE', '%'.$search.'%');
            }
        })
          
          ->addIndexColumn()
          ->addColumn('image',function($row){
            if ($row->image) {
                $image = '<img src="' . asset('/images/user/' . $row->image) . '" alt="Pic" width="50px" class="img-responsive">';
            } else {
                $image = '<img  src="' . Avatar::create(ucfirst($row->name))->toBase64() . '" alt="Pic" width="50px" class="img-responsive">';
            }
            return $image;
          })
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
          ->addColumn('role',function($row){
              return $row->role == 'S' ? 'Student' : '-';
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

            $btn ='<div class="admin-table-action-block">

                    <a href="' . route('users.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>
                  
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
          ->rawColumns(['image','name','email','username','mobile','role','status','action'])
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
            'mobile' => 'required|min:10',
            'password' => 'required|min:8',
            'username'=>'required'
          ]);

          try{
          $checkmail=User::where('email',$request->email)->get()->first();
          if($checkmail)
          {
              return back()->with('deleted', 'Email already exists. Please try with another email.');
          }
        }
        catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }

               try{
               $checkusername=User::where('username',$request->username)->get()->first();
               if($checkusername)
               {
                  return back()->with('deleted', 'Username already exists. Please try with another Username.');
               }
             }
             catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }


          $user = new User;
          $user->name = $request->name;
          $user->email = $request->email;
          $user->mobile = $request->mobile;
          $user->username=$request->username;
          $user->address="";
          $user->role = 'S';
          $user->city="";

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

            $user->image = $name;

          try{
            $user->save();
            return back()->with('added', 'User has been added !');

          }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
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
                            return back()->with('deleted',$listmessage);
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
      $user = User::findOrFail($id);
      return view('admin.users.edit',compact('user'));
      }
      catch(\Exception $e){
                  return redirect('admin/users/')->with('deleted','Something went wrong.');     
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
          'email' => 'required|string|email',
          'mobile' => 'sometimes|nullable|min:10'
        ]);

        $user = User::find($id);
          if(is_null($user)){
           return redirect('admin/users/')->with('deleted','Something went wrong.');
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


        if($user->email==$request->email && $user->username==$request->username)
        {
          if($image!="")
          {
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->address = "";
            $user->city = "";
            $user->image = $image;
          }
          else{
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->address = "";
            $user->city = "";
          }
          
        }
        elseif($user->email!=$request->email && $user->username==$request->username)
        {
            try{
          $checkmail=User::where('email',$request->email)->get()->first();
          if($checkmail)
          {
              return back()->with('deleted', 'Email already exists. Please try with another email.');
          }
        }
        catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }


          if($image!="")
          {
              $user->name = $request->name;
              $user->mobile = $request->mobile;
              $user->address = "";
              $user->city = "";
              $user->email=$request->email;
              $user->image = $image;
          }
          else{
              $user->name = $request->name;
              $user->mobile = $request->mobile;
              $user->address = "";
              $user->city = "";
              $user->email=$request->email;
          }
          

        }
        elseif($user->email==$request->email && $user->username!=$request->username)
        {
            try{
               $checkusername=User::where('username',$request->username)->get()->first();
               if($checkusername)
               {
                  return back()->with('deleted', 'Username already exists. Please try with another Username.');
               }
             }
             catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }

               if($image!="")
               {
                  $user->name = $request->name;
                  $user->mobile = $request->mobile;
                  $user->address = "";
                  $user->city = "";
                  $user->username=$request->username;
                  $user->image = $image;
               }
               else{
                    $user->name = $request->name;
                    $user->mobile = $request->mobile;
                    $user->address = "";
                    $user->city = "";
                    $user->username=$request->username;
               }
              
        }
        else{
              try{
          $checkmail=User::where('email',$request->email)->get()->first();
          if($checkmail)
          {
              return back()->with('deleted', 'Email already exists. Please try with another email.');
          }
        }
        catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }

               try{
               $checkusername=User::where('username',$request->username)->get()->first();
               if($checkusername)
               {
                  return back()->with('deleted', 'Username already exists. Please try with another Username.');
               }
             }
             catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
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
            } 
            else{
                $user->name = $request->name;
                $user->mobile = $request->mobile;
                $user->address = "";
                $user->city = "";
                $user->username=$request->username;
                $user->email = $request->email;
            }  
           

        }

          if($request->password !="")
          {
            $user->password = bcrypt($request->password);
          }

          try{
            $user->save();
            return back()->with('updated', 'User has been updated !');

          }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
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
                            return back()->with('deleted',$listmessage);
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
        $user = User::find($id);

        if(is_null($user)){
           return redirect('admin/users')->with('deleted','Something went wrong.');
        }
        
        if($user->image !=''){
          unlink('images/user/'.$user->image);
        }
        try{
          $user->delete();
          return back()->with('deleted', 'User has been deleted');
        }catch(\Exception $e){
          return back()->with('deleted',$e->getMessage());
        }

      }catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }

        
    }

    public function changestatus(Request $request)
    {
        try{
        $id=$request->id;
        $user = User::find($id);

        if(is_null($user)){
           return redirect('admin/users')->with('deleted','Something went wrong.');
        }


        if(isset($request->status)){
            $user->status = 1;
          }else{
            $user->status = 0;
        }

        try{
            $user->save();
           return back()->with('updated','User updated !');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }


    }

}
