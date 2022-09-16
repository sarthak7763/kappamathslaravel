<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Avatar;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        $users = \DB::table('users')->where('role','!=' , 'A')->select('id','image','name','email','mobile','role','city','address');

        if($request->ajax()){
          return DataTables::of($users)
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
          ->addColumn('mobile',function($row){
              return $row->mobile;
          })
          ->addColumn('role',function($row){
              return $row->role == 'S' ? 'Student' : '-';
          })
          ->addColumn('city',function($row){
              return isset($row->city) && $row->city ? $row->city : '-';
          })
          ->addColumn('address',function($row){
            return isset($row->address) && $row->address ? $row->address : '-';
          })
          ->addColumn('action',function($row){
            $btn ='<div class="admin-table-action-block">

                    <a href="' . route('users.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>
                  
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
                        <form method="POST" action="' . route("users.destroy", $row->id) . '">
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
          ->rawColumns(['image','name','email','mobile','role','city','address','action'])
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
          $user = new User;

          $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'unique:users|min:10',
            'password' => 'required|string|min:6',
          ]);

          $user->name = $request->name;
          $user->email = $request->email;
          $user->mobile = $request->mobile;
          $user->address = $request->address;
          $user->role = $request->role;
          $user->city = $request->city;

          if($request->password !="")
          {
            $user->password = bcrypt($request->password);
          }

          if ($file = $request->file('image'))
          {

          if($user->image !="")
          {
              unlink('images/user/'.$user->image);
          }

          $name = time().$file->getClientOriginalName();

          $file->move('images/user', $name);
          
          $user->image = $name;
      

          }
          try{
            $user->save();
            return back()->with('added', 'User has been added !');

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
      $user = User::find($id);
      return view('admin.users.edit',compact('user'));
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
        $user = User::findOrFail($id);

        $request->validate([
          'name' => 'required|string|max:255',
          'email' => 'required|string|email',
          'mobile' => 'sometimes|nullable|min:10'
        ]);

        $input = $request->all();

        if (Auth::user()->role == 'A') 
        {
          $user->name = $request->name;
          $user->email = $request->email;
          $user->mobile = $request->mobile;
          $user->address = $request->address;
          $user->city = $request->city;

          if($request->password !="")
          {
            $user->password = bcrypt($request->password);
          }

          if ($file = $request->file('image'))
          {
            if($user->image !="")
            {
              unlink('images/user/'.$user->image);
            }

            $name = time().$file->getClientOriginalName();
            $file->move('images/user', $name);
            $user->image = $name;      
          }

          $user->save();

        } 
        else if (Auth::user()->role == 'S') 
        {
          $user->name = $request->name;
          $user->email = $request->email;
          $user->mobile = $request->mobile;
          $user->address = $request->address;
          $user->city = $request->city;

          if($request->password !="")
          {
            $user->password = bcrypt($request->password);
          }

            if ($file = $request->file('image'))
            {
              if($user->image !="")
              {
                unlink('images/user/'.$user->image);
              }

              $name = time().$file->getClientOriginalName();
              $file->move('images/user', $name);
              $user->image = $name;
            }

          $user->save();

        }

        return back()->with('updated', 'Student has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        
        if($user->image !=''){
          unlink('images/user/'.$user->image);
        }
        try{
          $user->delete();
          return back()->with('deleted', 'User has been deleted');
        }catch(\Exception $e){
          return back()->with('deleted',$e->getMessage());
        }
        
    }

}
