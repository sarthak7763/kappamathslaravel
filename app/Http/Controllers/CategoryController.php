<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use Yajra\Datatables\DataTables;
use Exception;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $category = \DB::table('category')->select('id','title','description','status');

          if($request->ajax()){

            return DataTables::of($category)

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

                    <a href="' . route('category.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

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

                       <form method="POST" action="' . route("categorychangestatus") . '">
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

        return view('admin.category.index', compact('category'));
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
          'title' => 'required|string'
        ]);

        if(isset($request->status)){
          $input['status'] = "1";
        }else{
          $input['status'] = "0";
        }

        try{
        $categorydata=Category::where('title',$request->title)->first();
        if($categorydata)
        {
        	return back()->with('deleted','Title already exists.');
        }
        else{
        	try{
		         $quiz = Category::create($input);
		           return back()->with('added', 'Category has been added');
		        }catch(\Exception $e){
		          return back()->with('deleted',$e->getMessage());     
		       }
        }
    }
    catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }

    }catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
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
            $category = Category::findOrFail($id);
           return view('admin.category.edit',compact('category'));
        }
        catch(\Exception $e){
                  return redirect('admin/category/')->with('deleted','Something went wrong.');     
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

          $category = Category::find($id);
          if(is_null($category)){
           return redirect('admin/category')->with('deleted','Something went wrong.');
        }

        if(isset($request->status)){
            $statusvalue = 1;
          }else{
            $statusvalue = 0;
          }

        if($category->title==$request->title)
        {
          $category->description = $request->description;
          $category->status=$statusvalue;
        }
        else{
            try{
                $categorydata=Category::where('title',$request->title)->first();
                if($categorydata)
                {
                    return back()->with('deleted','Title already exists.');
                }
            }
            catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }

            $category->title=$request->title;
            $category->description = $request->description;
            $category->status=$statusvalue;
        } 

         try{
            $category->save();
          return back()->with('updated','Category updated !');
         }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
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
        $category = Category::find($id);

        if(is_null($category)){
           return redirect('admin/category')->with('deleted','Something went wrong.');
        }

        try{
            $category->delete();
           return back()->with('deleted', 'Category has been deleted');
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
        $category = Category::find($id);

        if(is_null($category)){
           return redirect('admin/category')->with('deleted','Something went wrong.');
        }


        if(isset($request->status)){
            $category->status = 1;
          }else{
            $category->status = 0;
        }

        try{
            $category->save();
           return back()->with('updated','Category updated !');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }


    }
}
