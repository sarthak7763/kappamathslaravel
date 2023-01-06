<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contactsubject;
use App\Contactenquiry;
use Yajra\Datatables\DataTables;
use Exception;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $contactsubject = \DB::table('contact_subject')->select('id','name','status');

          if($request->ajax()){

            return DataTables::of($contactsubject)
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

                    <a href="' . route('contact-subject.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

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

                       <form method="POST" action="' . route("contactsubjectchangestatus") . '">
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

        return view('admin.contact.index', compact('contactsubject'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('admin.contact.create');
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

        try{
        $contactsubjectdata=Contactsubject::where('name',$request->name)->first();
        if($contactsubjectdata)
        {
        	return back()->with('error','Subject Name already exists.');
        }
        else{
        	try{
		         $quiz = Contactsubject::create($input);
		           return redirect('/admin/contact-subject/')->with('success', 'Record has been added');
		        }catch(\Exception $e){
		          return back()->with('error',$e->getMessage());     
		       }
        }
    }
    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
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

            $contactsubject = Contactsubject::findOrFail($id);
           return view('admin.contact.edit',compact('contactsubject'));
        }
        catch(\Exception $e){
                  return redirect('admin/contact-subject/')->with('error','Something went wrong.');     
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

          $contactsubject = Contactsubject::find($id);
          if(is_null($contactsubject)){
           return redirect('admin/contact-subject')->with('error','Something went wrong.');
        }

        if(isset($request->status)){
            $statusvalue = 1;
          }else{
            $statusvalue = 0;
          }

        if($contactsubject->name==$request->name)
        {
            $contactsubject->status=$statusvalue;
        }
        else{
            try{
                $contactsubjectdata=Contactsubject::where('name',$request->name)->first();
                if($contactsubjectdata)
                {
                    return back()->with('error','Name already exists.');
                }
            }
            catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

            $contactsubject->name=$request->name;
            $contactsubject->status=$statusvalue;
        } 

         try{
            $contactsubject->save();
          return redirect('/admin/contact-subject/')->with('success','Record updated !');
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
        $contactsubject = Contactsubject::find($id);

        if(is_null($contactsubject)){
           return redirect('admin/contact-subject')->with('error','Something went wrong.');
        }

        try{
            $contactsubject->delete();
           return back()->with('success', 'Record has been deleted');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }
     }
     catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
        
    }

    public function changestatus(Request $request)
    {
        try{
        $id=$request->id;
        $contactsubject = Contactsubject::find($id);

        if(is_null($contactsubject)){
           return redirect('admin/contact-subject')->with('error','Something went wrong.');
        }


        if(isset($request->status)){
            $contactsubject->status = 1;
          }else{
            $contactsubject->status = 0;
        }

        try{
            $contactsubject->save();
           return back()->with('success','Record updated !');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }


    }

    public function contactenquiry(Request $request)
    {
        $contactenquirydata = \DB::table('contact_enquiry')->select('id','name','email','number','subject','message');

          if($request->ajax()){

            return DataTables::of($contactenquirydata)
            ->addIndexColumn()
            ->addColumn('name',function($row){
                return $row->name;
            })
            ->addColumn('email',function($row){
                return $row->email;
            })
            ->addColumn('number',function($row){
                return $row->number;
            })
            ->addColumn('subject',function($row){
                $contactsubject=Contactsubject::find($row->subject);
                if($contactsubject)
                {
                  $contactsubjectname=$contactsubject->name;
                }
                else{
                  $contactsubjectname="NA";
                }
                
                return $contactsubjectname;
            })
            ->addColumn('message',function($row){
                return $row->message;
            })
            ->rawColumns(['name','email','number','subject','message'])
            ->make(true);

          }

        return view('admin.contact.contactenquiry', compact('contactenquirydata'));
    }
}
