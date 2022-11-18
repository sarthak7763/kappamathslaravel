<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Cmspages;
use Yajra\Datatables\DataTables;
use Exception;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class CmsPagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cmspages = \DB::table('cms_pages')->select('id','name','status');

          if($request->ajax()){

            return DataTables::of($cmspages)

            ->filter(function ($row) use ($request) { 
            if ($request->input('search.value') != "") {
                $search=$request->input('search.value');
                $row->where('name', 'LIKE', '%'.$search.'%');
            }
        })

            ->addIndexColumn()
            ->addColumn('name',function($row){
                return $row->name;
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

                    <a href="' . route('cms-pages.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

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

                       <form method="POST" action="' . route("cmspageschangestatus") . '">
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
            ->rawColumns(['name','status','action'])
            ->make(true);

          }

        return view('admin.cmspages.index', compact('cmspages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('admin.cmspages.create');
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
          'name' => 'required|string'
        ]);

        if(isset($request->status)){
          $input['status'] = "1";
        }else{
          $input['status'] = "0";
        }

        $input['slug'] = Str::slug($request->name);

        try{
        $cmspagesdata=Cmspages::where('name',$request->name)->first();
        if($cmspagesdata)
        {
        	return back()->with('deleted','CMS page already exists.');
        }
        else{
        	try{
		         $quiz = Cmspages::create($input);
		           return back()->with('added', 'CMS page has been added');
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

            $cmspages = Cmspages::findOrFail($id);
           return view('admin.cmspages.edit',compact('cmspages'));
        }
        catch(\Exception $e){
                  return redirect('admin/cms-pages/')->with('deleted','Something went wrong.');     
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
          'name' => 'required|string'
        ]);

          $cmspages = Cmspages::find($id);
          if(is_null($cmspages)){
           return redirect('admin/cms-pages')->with('deleted','Something went wrong.');
        }

        if(isset($request->status)){
            $statusvalue = 1;
          }else{
            $statusvalue = 0;
          }

        if($cmspages->name==$request->name)
        {
            $cmspages->description = $request->description;
            $cmspages->status=$statusvalue;
        }
        else{
            try{
                $cmspagesdata=Cmspages::where('name',$request->name)->first();
                if($cmspagesdata)
                {
                    return back()->with('deleted','CMS Page already exists.');
                }
            }
            catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }

            $slug = Str::slug($request->name);
            $cmspages->name=$request->name;
            $cmspages->slug=$slug;
            $cmspages->description = $request->description;
            $cmspages->status=$statusvalue;
        } 

         try{
            $cmspages->save();
          return back()->with('updated','CMS Page updated !');
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
        $cmspages = Cmspages::find($id);

        if(is_null($cmspages)){
           return redirect('admin/cms-pages')->with('deleted','Something went wrong.');
        }

        try{
            $cmspages->delete();
           return back()->with('deleted', 'CMS page has been deleted');
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
        $cmspages = Cmspages::find($id);

        if(is_null($cmspages)){
           return redirect('admin/cms-pages')->with('deleted','Something went wrong.');
        }


        if(isset($request->status)){
            $cmspages->status = 1;
          }else{
            $cmspages->status = 0;
        }

        try{
            $cmspages->save();
           return back()->with('updated','CMS Page updated !');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }


    }
}
