<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coursetopic;
use App\Category;
use Exception;
use Yajra\Datatables\DataTables;
class CoursetopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $coursetopic = \DB::table('coursetopics')->select('id','category','topic_name','topic_image','topic_status');

          if($request->ajax()){

            return DataTables::of($coursetopic)

            ->filter(function ($row) use ($request) { 
            if ($request->input('search.value') != "") {
                $search=$request->input('search.value');
                $row->where('topic_name', 'LIKE', '%'.$search.'%');
            }
        })

            ->addIndexColumn()
            ->addColumn('category',function($row){
                if($row->category!="" && $row->category!="0")
                {
                    $categorydata=Category::where('id',$row->category)->first();
                    if(!empty($categorydata))
                    {
                        $categorydataarray=$categorydata->toArray();
                        $categoryname=$categorydataarray['title'];
                    }
                    else{
                        $categoryname="NA";
                    }
                }
                else{
                    $categoryname="NA";
                }

                return $categoryname;
            })
            ->addColumn('topic_name',function($row){
                return $row->topic_name;
            })
            ->addColumn('topic_image',function($row){
            	if($row->topic_image!="")
            	{
            		$topicimagerow='<img src="/images/topics/'.$row->topic_image.'" style="height: auto;width: 15%;">';
            	}
            	else{
            		$topicimagerow='<img src="/images/noimage.jpg" style="height: auto;width: 15%;">';
            	}
            	
                return $topicimagerow;
            })
            ->addColumn('topic_status',function($row){

            if($row->topic_status=="1")
            {
                $statusvalue="Active";
            }
            else{
                $statusvalue="suspend";
            }

                return $statusvalue;
            })

            ->addColumn('action',function($row){

            if($row->topic_status=="1")
            {
                $checked="checked";
            }
            else{
                $checked="";
            }

              $btn = '<div class="admin-table-action-block">

                    <a href="' . route('course-topic.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

                    <button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->topic_status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';
                   

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
                //         <form method="POST" action="' . route("course-topic.destroy", $row->id) . '">
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

                       <form method="POST" action="' . route("coursetopicchangestatus") . '">
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
            ->rawColumns(['category','topic_name','topic_image','topic_status','action'])
            ->make(true);

          }

        return view('admin.coursetopic.index', compact('coursetopic'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    	try{
        $categoryalldata=Category::where('status','1')->get();
          if(!empty($categoryalldata))
          {
            $categorylist=$categoryalldata->toArray();
          }
          else{
            $categorylist=[];
          }

          return view('admin.coursetopic.create', compact('categorylist'));
      }catch(\Exception $e){
                  return redirect('admin/course-topic/')->with('deleted','Something went wrong.');     
               }

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
            'category'=>'required',
          'title' => 'required|string'
        ]);

        if(isset($request->status)){
          $statusvalue = "1";
        }else{
          $statusvalue = "0";
        }

        if ($file = $request->file('topic_img')) {
            $name = 'topic_'.time().$file->getClientOriginalName(); 
            $file->move('images/topics/', $name);
            $topic_img = $name;
        }
        else{
        	$topic_img="";
        }

        try{
        $coursetopicdata=Coursetopic::where('topic_name122',$request->title)->first();
        if($coursetopicdata)
        {
        	return back()->with('deleted','Title already exists.');
        }
        else{
            try{
                $categorydata=Category::where('id',$request->category)->first();
                if(!$categorydata)
                {
                    return back()->with('deleted','Please choose category.');
                }
            }catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }


        	try{
                $coursetopic = new Coursetopic;
                $coursetopic->category = $request->category;
                $coursetopic->topic_name = $request->title;
                $coursetopic->topic_description = $request->description;
                $coursetopic->topic_image = $topic_img;
                $coursetopic->topic_status = $statusvalue;
                $coursetopic->save();
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
    	$categoryalldata=Category::where('status','1')->get();
	      if(!empty($categoryalldata))
	      {
	        $categorylist=$categoryalldata->toArray();
	      }
	      else{
	        $categorylist=[];
	      }

	     
	     $coursetopic = Coursetopic::findOrFail($id);
	     $coursetopic->title=$coursetopic->topic_name;
	     $coursetopic->description=$coursetopic->topic_description;
	     return view('admin.coursetopic.edit',compact('coursetopic','categorylist'));
     }
     catch(\Exception $e){
                  return redirect('admin/course-topic/')->with('deleted','Something went wrong.');     
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
        	'category'=>'required',
          'title' => 'required|string'
        ]);

          $coursetopic = Coursetopic::find($id);

         if(is_null($coursetopic)){
		   return redirect('admin/course-topic')->with('deleted','Something went wrong.');
		}

          if ($file = $request->file('topic_img')) {
            $name = 'topic_'.time().$file->getClientOriginalName(); 
            $file->move('images/topics/', $name);
            $topic_img = $name;
        }
        else{
        	$topic_img="";
        }

          if(isset($request->status)){
            $statusvalue = 1;
          }else{
            $statusvalue = 0;
          }

          try{
                $categorydata=Category::where('id',$request->category)->first();
                if(!$categorydata)
                {
                    return back()->with('deleted','Please choose category.');
                }
            }catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }


          if($coursetopic->topic_name==$request->title)
          {
	          if($topic_img!="")
	          {
	          	$coursetopic->category = $request->category;
  		        $coursetopic->topic_description = $request->description;
  		        $coursetopic->topic_image = $topic_img;
  		        $coursetopic->topic_status = $statusvalue;
	          }
	          else{
	          	$coursetopic->category = $request->category;
  		        $coursetopic->topic_description = $request->description;
  		        $coursetopic->topic_status = $statusvalue;
	          }
          }
          else{
          		try{
		        $coursetopicdata=Coursetopic::where('topic_name',$request->title)->first();
		        if($coursetopicdata)
		        {
		        	return back()->with('deleted','Title already exists.');
		        }
		    }
		    catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }

            if($topic_img!="")
	          {
	          	$coursetopic->category = $request->category;
	          	$coursetopic->topic_name=$request->title;
  		        $coursetopic->topic_description = $request->description;
  		        $coursetopic->topic_image = $topic_img;
  		        $coursetopic->topic_status = $statusvalue;
	          }
	          else{
	          	$coursetopic->category = $request->category;
	          	$coursetopic->topic_name=$request->title;
		          $coursetopic->topic_description = $request->description;
		          $coursetopic->topic_status = $statusvalue;
	          }
          }
         try{
            $coursetopic->save();
          return back()->with('updated','Topic updated !');
         }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
         }

       }catch(\Exception $e){
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
        $coursetopic = Coursetopic::find($id);

        if(is_null($coursetopic)){
		   return redirect('admin/course-topic')->with('deleted','Something went wrong.');
		}

        try{
            $coursetopic->delete();
           return back()->with('deleted', 'Topic has been deleted');
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
        $coursetopic = Coursetopic::find($id);

        if(is_null($coursetopic)){
		   return redirect('admin/course-topic')->with('deleted','Something went wrong.');
		}

        if(isset($request->status)){
            $coursetopic->topic_status = 1;
          }else{
            $coursetopic->topic_status = 0;
        }

        try{
            $coursetopic->save();
           return back()->with('updated','Topic updated !');
        }catch(\Exception $e){
            return back()->with('deleted',$e->getMessage());
         }

    }
    catch(\Exception $e){
                  return back()->with('deleted','Something went wrong.');     
               }
  }


}
