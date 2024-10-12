<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subjectcategory;
use App\Subject;
use App\User;
use Exception;
use Yajra\Datatables\DataTables;
use DB,Toastr;
use Illuminate\Validation\ValidationException;
use Validator;

class MySubjectcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
      $subject = Subject::all();

      $subject_search = $request->course;

     
      $course_search = $request->category_title;

          if($request->ajax()){
          $query = Subjectcategory::orderBy('id');

            if(!empty($subject_search)){
             $query = $query->where('subject',$subject_search);
            }


          
           $data =  $query->get();
            
                                     




            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('subject',function($row){
                  
                $subjectdata=Subject::where('id',$row->subject)->first();
               
               
                    if(!empty($subjectdata)){
                       $subjectname = $subjectdata->title;
                       }
                    else{
                        $subjectname="NA";
                    }
                  return $subjectname;
            })
            ->addColumn('category_name',function($row){
              return $row->category_name;
            })
            ->addColumn('category_status',function($row){
              if($row->category_status==1){

                $status ='<span class="badge badge-success badge_changestatus" style="cursor: pointer;" span-id="'.$row->id.'">Active</span>';
                 

              }else{
                 $status='<span class="badge badge-danger badge_changestatus" style="cursor: pointer;" span-id="'.$row->id.'">Inactive</span>';
                            
              }
              return $status;
            })
            ->addColumn('sort_order',function($row){
                return $row->sort_order;
            })

            ->addColumn('action',function($row){
            $action = '<a href=" '.route('mycoursetopic-edit',$row->id).'   data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>
                      <button type="button" class="btn btn-danger changestatus" data-id ="'.$row->id.'" data-status = "'.$row->category_status.'"  >Change Status </button>';
                  return $action;
              })
            ->escapeColumns(['action'])
            ->rawColumns(['subject','category_name','category_status','sort_order','action'])
            ->make(true);

          }
          









            return view('admin.MyCourseTopic.index',compact('subject','subject_search','course_search'));
    }




    public function updateStatus(Request $request)
    {

      $current_id = $request->id;
      $current_status = $request->status;
      $course_subcatdata = Subjectcategory::find($current_id);
      if($current_status==1)
      {
        $course_subcatdata->category_status = 0;
      }
      else
      {
        $course_subcatdata->category_status = 1;
      }
      $course_subcatdata->save();

      return response()->json(['code'=>'200','status'=>'success','message'=>'subject category status updated']);

    }


             public function add()
             {

              $subject_data = Subject::where('status',1)->get()->toArray();
          
                return view('admin.MyCourseTopic.add',compact('subject_data'));
             }



              public function store(Request $request)
              {
              
      
               


              try{
            $data = $request->all();
            // $validator = Validator::make($request->all(), [
            //             'title' => 'required',
            //             'course' => 'required',
            //             'description' => 'required',
            //             'sort_order' => 'required',
            //             'topic_image' => 'required|mimes:jpeg,png,pdf,doc',

            // ],
            // [
            // 'title.required' => 'title is required.',
            // 'course.required' => 'course is required.',
            // 'description.required'=>'description is required',
            // 'sort_order.required'=>'Sort order is required',
            // 'topic_image.required'=>'Sort order is required',
            // 'topic_image.mimes'=>'Topic image extension should be jpeg,jpg,pdf,doc',
            // ]);
            // if($validator->fails()){
            // return Redirect::back()->withErrors($validator)->withInput();
            // }

           
            if($request->hasFile('topic_image')){
              
            
              $imageName = time().'.'.$request->topic_image->extension();
              $image = $request->topic_image->move(public_path('images/'), $imageName);

            
                
                $subject_category = new Subjectcategory;
                $subject_category->category_name=$data['title'];
                
                $subject_category->subject=$data['course'];
                
                $subject_category->category_description=$data['description'];
                
                $subject_category->sort_order=$data['sort_order'];
                $subject_category->category_image=$imageName;
                $subject_category->category_status=1;
                
                $subject_category->save();

            
              
                
           return redirect()->route('mycoursetopic-list')->with('success',"Subject category saved successfully"); 
            }
        }  
        catch(Exception $e){ 
         
        return redirect()->back()->with('error', $e->getmessage());     
       }
    } 
    
    

    public function subjectCategory(Request $request)
    {
      $data = $request->all();
      $course_topic = Subjectcategory::where('subject',$data['course'])->where('category_status',1)->get();

          
      if(!empty($course_topic)){
                    return response()->json(['status'=>'success','message'=>$course_topic]);

          }
          else{
              return response()->json(['status'=>'error','message'=>'something went wrong']);
          }
  }
}
