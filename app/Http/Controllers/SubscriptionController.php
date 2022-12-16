<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subscription;
use Yajra\Datatables\DataTables;
use Exception;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class SubscriptionController extends Controller
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

        $subscription = \DB::table('subscriptions')->where('created_at','>=',$filter_start_date)->where('created_at','<=',$filter_end_date)->select('id','title','description','price','subscription_plan','subscription_tenure','subscription_status');
      }
      elseif($request->filter_start_date!="" && $request->filter_end_date=="")
      {
        $filter_start_date=date('Y-m-d',strtotime($request->filter_start_date));

        $subscription = \DB::table('subscriptions')->where('created_at','>=',$filter_start_date)->select('id','title','description','price','subscription_plan','subscription_tenure','subscription_status');
      }
      elseif($request->filter_start_date=="" && $request->filter_end_date!="")
      {
        $filter_end_date=date('Y-m-d',strtotime($request->filter_end_date));

          $filter_end_date=date('Y-m-d', strtotime("+1 day", strtotime($filter_end_date)));

        $subscription = \DB::table('subscriptions')->where('created_at','<=',$filter_end_date)->select('id','title','description','price','subscription_plan','subscription_tenure','subscription_status');
      }
      else{
        $subscription = \DB::table('subscriptions')->select('id','title','description','price','subscription_plan','subscription_tenure','subscription_status');
      }
        

          if($request->ajax()){

            return DataTables::of($subscription)
            ->addIndexColumn()
            ->addColumn('title',function($row){
                return $row->title;
            })
            ->addColumn('price',function($row){
                return $row->price;
            })
            ->addColumn('subscription_date',function($row){
                return $row->subscription_tenure.' '.$row->subscription_plan;
            })
            ->addColumn('status',function($row){

            if($row->subscription_status=="1")
            {
                $statusvalue="Active";
            }
            else{
                $statusvalue="suspend";
            }

                return $statusvalue;
            })

            ->addColumn('action',function($row){

            if($row->subscription_status=="1")
            {
                $checked="checked";
            }
            else{
                $checked="";
            }

              $btn = '<div class="admin-table-action-block">

                    <a href="' . route('subscription.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

                    <button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->subscription_status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';
                   

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

                       <form method="POST" action="' . route("subscriptionchangestatus") . '">
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
            ->rawColumns(['title','price','subscription_date','status','action'])
            ->make(true);

          }

        return view('admin.subscription.index', compact('subscription'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        $montharray=array(
          'day'=>'Day',
          'month'=>'Month',
          'year'=>'Year'
        );

        return view('admin.subscription.create',compact('montharray'));
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
          'title' => 'required|string',
          'price' => 'required',
          'subscription_tenure' => 'required',
          'subscription_plan' => 'required'
        ]);

        if(isset($request->status)){
          $input['subscription_status'] = "1";
        }else{
          $input['subscription_status'] = "0";
        }

        try{
        $subscriptiondata=Subscription::where('title',$request->title)->first();
        if($subscriptiondata)
        {
        	return back()->with('error','Title already exists.');
        }
        else{
        	try{
		         $quiz = Subscription::create($input);
		           return redirect('/admin/subscription/')->with('success', 'Subscription Plan has been added');
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

            $montharray=array(
              'day'=>'Day',
              'month'=>'Month',
              'year'=>'Year'
            );

            $subscription = Subscription::findOrFail($id);
           return view('admin.subscription.edit',compact('subscription','montharray'));
        }
        catch(\Exception $e){
                  return redirect('admin/subscription/')->with('error','Something went wrong.');     
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
          'title' => 'required|string',
          'price' => 'required',
          'subscription_tenure' => 'required',
          'subscription_plan' => 'required'
        ]);

          $subscription = Subscription::find($id);
          if(is_null($subscription)){
           return redirect('admin/subscription')->with('error','Something went wrong.');
        }

        if(isset($request->status)){
            $statusvalue = 1;
          }else{
            $statusvalue = 0;
          }

        if($subscription->title==$request->title)
        {
            $subscription->description = $request->description;
            $subscription->price = $request->price;
            $subscription->subscription_tenure = $request->subscription_tenure;
            $subscription->subscription_plan = $request->subscription_plan;
            $subscription->subscription_status=$statusvalue;
        }
        else{
            try{
                $subscriptiondata=Subscription::where('title',$request->title)->first();
                if($subscriptiondata)
                {
                    return back()->with('error','Title already exists.');
                }
            }
            catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

            $subscription->title=$request->title;
            $subscription->description = $request->description;
            $subscription->price = $request->price;
            $subscription->subscription_date = $request->subscription_date;
            $subscription->subscription_plan = $request->subscription_plan;
            $subscription->subscription_status=$statusvalue;
        } 

         try{
            $subscription->save();
          return redirect('admin/subscription/')->with('success','Subscription Plan updated !');
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
        $subscription = Subscription::find($id);

        if(is_null($subscription)){
           return redirect('admin/subscription')->with('error','Something went wrong.');
        }

        try{
            $subscription->delete();
           return back()->with('success', 'Subscription has been deleted');
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
        $subscription = Subscription::find($id);

        if(is_null($subscription)){
           return redirect('admin/subscription')->with('error','Something went wrong.');
        }


        if(isset($request->status)){
            $subscription->subscription_status = 1;
          }else{
            $subscription->subscription_status = 0;
        }

        try{
            $subscription->save();
           return back()->with('success','Subscription Plan updated !');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }


    }
}
