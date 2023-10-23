<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SubscriptionCoupon;
use App\Subscription;
use App\User;
use Exception;
use Yajra\Datatables\DataTables;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class SubscriptionCodeController extends Controller
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

          $subscriptioncoupon = \DB::table('subscription_coupon')->where('created_at','>=',$filter_start_date)->where('created_at','<=',$filter_end_date)->select('id','coupon_name','coupon_date','coupon_time','coupon_status','coupon_type');
        }
        elseif($request->filter_start_date!="" && $request->filter_end_date=="")
        {
           $filter_start_date=date('Y-m-d',strtotime($request->filter_start_date));

          $subscriptioncoupon = \DB::table('subscription_coupon')->where('created_at','>=',$filter_start_date)->select('id','coupon_name','coupon_date','coupon_time','coupon_status','coupon_type');
        }
        elseif($request->filter_start_date=="" && $request->filter_end_date!="")
        {
          $filter_end_date=date('Y-m-d',strtotime($request->filter_end_date));

          $filter_end_date=date('Y-m-d', strtotime("+1 day", strtotime($filter_end_date)));

          $subscriptioncoupon = \DB::table('subscription_coupon')->where('created_at','<=',$filter_end_date)->select('id','coupon_name','coupon_date','coupon_time','coupon_status','coupon_type');
        }
        else{
          $subscriptioncoupon = \DB::table('subscription_coupon')->select('id','coupon_name','coupon_date','coupon_time','coupon_status','coupon_type');
        }
        

          if($request->ajax()){

            return DataTables::of($subscriptioncoupon)
            ->addIndexColumn()
            ->addColumn('coupon_type',function($row){
                if($row->coupon_type!="")
                {
                    if($row->coupon_type=="1")
                    {
                      $coupontypename="Voucher";
                    }
                    else{
                      $coupontypename="Coupon";
                    }
                }
                else{
                    $coupontypename="NA";
                }

                return $coupontypename;
            })
            ->addColumn('coupon_name',function($row){
                return $row->coupon_name;
            })
            ->addColumn('coupon_date',function($row){
                return $row->coupon_date.' '.$row->coupon_time;
            })
            ->addColumn('coupon_status',function($row){

            if($row->coupon_status=="1")
            {
                $statusvalue="Active";
            }
            else{
                $statusvalue="suspend";
            }

                return $statusvalue;
            })
            ->addColumn('action',function($row){

            if($row->coupon_status=="1")
            {
                $checked="checked";
            }
            else{
                $checked="";
            }

              $btn = '<div class="admin-table-action-block">

                    <a href="' . route('coupon-subscription.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>

                    <button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->coupon_status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';
                   

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
                //         <form method="POST" action="' . route("course-category.destroy", $row->id) . '">
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

                       <form method="POST" action="' . route("subscriptioncouponchangestatus") . '">
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
            ->rawColumns(['coupon_type','coupon_name','coupon_date','coupon_status','action'])
            ->make(true);

          }

        return view('admin.subscriptioncode.index', compact('subscriptioncoupon'));
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
        $useralldata=User::where('status','1')->where('role','S')->get();
          if(!empty($useralldata))
          {
            $userlist=$useralldata->toArray();
          }
          else{
            $userlist=[];
          }

          $montharray=array(
            'days'=>'Day',
            'months'=>'Month',
            'years'=>'Year'
          );

          $subscriptionalldata=Subscription::where('subscription_status','1')->get();
          if(!empty($subscriptionalldata))
          {
            $subscriptionlist=$subscriptionalldata->toArray();
          }
          else{
            $subscriptionlist=[];
          }

          return view('admin.subscriptioncode.create', compact('userlist','subscriptionlist','montharray'));
      }catch(\Exception $e){
                  return redirect('admin/coupon-subscription/')->with('deleted','Something went wrong.');     
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
            'coupon_type'=>'required',
            'coupon_title' => 'required',
            'coupon_start_date'=>'required',
            'coupon_end_date'=>'required',
            'user_type'=>'required',
            'coupon_use_per_user'=>'required',
            'coupon_subscription_type'=>'required'
        ]);

        if($request->coupon_type!="")
        {
            if($request->coupon_type=="2")
            {
                $request->validate([
                  'coupon_discount_type'=>'required',
                  'coupon_discount'=>'required',
                  'minimum_transaction_amount'=>'required',
              ]);
            }
        }

        if($request->coupon_discount_type!="")
        {
            if($request->coupon_discount_type=="2")
            {
                $request->validate([
                  'coupon_max_amount' => 'required',
              ]);
            }
        }

        if($request->user_type!="")
        {
            if($request->user_type=="1")
            {
                $request->validate([
                  'coupon_user_limit'=>'required',
              ]);
            }
            else{
              $request->validate([
                  'coupon_users'=>'required|array',
              ]);
            }
        }

        if(isset($request->status)){
          $statusvalue = "1";
        }else{
          $statusvalue = "0";
        }

        try{
      
        $checkcoupon=SubscriptionCoupon::where('coupon_name',$request->coupon_title)->where('coupon_type',$request->coupon_type)->first();
        if($checkcoupon)
        {
        	return back()->with('error','Coupon Title already exists.');
        }
        else{

          if($request->coupon_type!="")
          {
            if($request->coupon_type=="2")
            {
              $coupon_discount_type=$request->coupon_discount_type;
              $coupon_discount=$request->coupon_discount;
              $minimum_transaction_amount=$request->minimum_transaction_amount;
            }
            else{
              $coupon_discount_type=0;
              $coupon_discount=0;
              $minimum_transaction_amount=0;
            }
          }
          else{
              $coupon_discount_type=0;
              $coupon_discount=0;
              $minimum_transaction_amount=0;
          }


          if($request->coupon_discount_type!="")
          {
            if($request->coupon_discount_type=="2")
            {
              $coupon_max_amount=$request->coupon_max_amount;
            }
            else{
              $coupon_max_amount=0;
            }
          }
          else{
              $coupon_max_amount=0;
          }


          if($request->user_type!="")
          {
            if($request->user_type=="1")
            {
              $coupon_user_limit=$request->coupon_user_limit;
              $coupon_users=0;
            }
            else{
              $coupon_user_limit=0;
              $coupon_users=implode(',',$request->coupon_users);
            }
          }
          else{
              $coupon_user_limit=0;
              $coupon_users=0;
          }

        	try{
                $subscriptioncoupon = new SubscriptionCoupon;
                $subscriptioncoupon->coupon_name = $request->coupon_title;
                $subscriptioncoupon->coupon_start_date = date('Y-m-d',strtotime($request->coupon_start_date));
                $subscriptioncoupon->coupon_end_date = date('Y-m-d',strtotime($request->coupon_end_date));
                $subscriptioncoupon->coupon_users = $coupon_users;
                $subscriptioncoupon->coupon_user_limit = $coupon_user_limit;
                $subscriptioncoupon->coupon_use_per_user = $request->coupon_use_per_user;
                $subscriptioncoupon->coupon_type = $request->coupon_type;
                $subscriptioncoupon->coupon_discount_type = $coupon_discount_type;
                $subscriptioncoupon->coupon_discount = $coupon_discount;
                $subscriptioncoupon->coupon_max_amount = $coupon_max_amount;
                $subscriptioncoupon->minimum_transaction_amount = $minimum_transaction_amount;
                $subscriptioncoupon->coupon_subscription_type = $request->coupon_subscription_type;
                $subscriptioncoupon->coupon_description = $request->coupon_description;
                $subscriptioncoupon->coupon_status = $statusvalue;
                $subscriptioncoupon->save();

                if($coupon_users!=0)
                {
                  if($request->coupon_type=="1")
                  {
                    $coupontype="Voucher";
                  }
                  else{
                    $coupontype="Coupon";
                  }

                  $coupon_users_array=explode(',',$coupon_users);
                  if(count($coupon_users_array) > 0)
                  {
                  	$coupon_image=url('/').'images/logo/logo_1669793364logo.png';
                  $coupontitle="Purchase a subscription";
                  $coupon_message="Purchase a subscription now and get amazing discount. Use ".$coupontype." ".$request->coupon_title."";
                  sendNotificationtoparticulardevices($coupon_users_array,$coupon_image,$coupontitle,$coupon_message);
                  }
                }
                else{
                  if($request->coupon_type=="1")
                  {
                    $coupontype="Voucher";
                  }
                  else{
                    $coupontype="Coupon";
                  }

                  $coupon_image=url('/').'images/logo/logo_1669793364logo.png';
                  $coupontitle="Purchase a subscription";
                  $coupon_message="Purchase a subscription now and get amazing discount. Use ".$coupontype." ".$request->coupon_title."";
                  sendNotificationtomultipledevices($coupon_image,$coupontitle,$coupon_message);
                }

               return redirect('admin/coupon-subscription/')->with('success','Coupon has been added.');

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
    	
        $useralldata=User::where('status','1')->where('role','S')->get();
          if(!empty($useralldata))
          {
            $userlist=$useralldata->toArray();
          }
          else{
            $userlist=[];
          }

          $montharray=array(
            'days'=>'Day',
            'months'=>'Month',
            'years'=>'Year'
          );

          $subscriptionalldata=Subscription::where('subscription_status','1')->get();
          if(!empty($subscriptionalldata))
          {
            $subscriptionlist=$subscriptionalldata->toArray();
          }
          else{
            $subscriptionlist=[];
          }
	     
	     $subscriptioncoupon = SubscriptionCoupon::findOrFail($id);
       $subscriptioncoupon->coupon_title = $subscriptioncoupon->coupon_name;
       $subscriptioncoupon->coupon_start_date = date('m/d/Y',strtotime($subscriptioncoupon->coupon_start_date));
        $subscriptioncoupon->coupon_end_date = date('m/d/Y',strtotime($subscriptioncoupon->coupon_end_date));
	     return view('admin.subscriptioncode.edit',compact('subscriptioncoupon','subscriptionlist','montharray','userlist'));
     }
     catch(\Exception $e){
                  return redirect('admin/coupon-subscription/')->with('deleted','Something went wrong.');     
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
            'coupon_type'=>'required',
            'coupon_title' => 'required',
            'coupon_start_date'=>'required',
            'coupon_end_date'=>'required',
            'user_type'=>'required',
            'coupon_use_per_user'=>'required',
            'coupon_subscription_type'=>'required',
        ]);

        if($request->coupon_type!="")
        {
            if($request->coupon_type=="2")
            {
                $request->validate([
                  'coupon_discount_type'=>'required',
                  'coupon_discount'=>'required',
                  'minimum_transaction_amount'=>'required',
              ]);
            }
        }


        if($request->coupon_discount_type!="")
        {
            if($request->coupon_discount_type=="2")
            {
                $request->validate([
                  'coupon_max_amount' => 'required',
              ]);
            }
        }

        if($request->user_type!="")
        {
            if($request->user_type=="1")
            {
                $request->validate([
                  'coupon_user_limit'=>'required',
              ]);
            }
            else{
              $request->validate([
                  'coupon_users'=>'required|array',
              ]);
            }
        }

        $subscriptioncoupon = SubscriptionCoupon::find($id);

         if(is_null($subscriptioncoupon)){
		   return redirect('admin/coupon-subscription')->with('error','Something went wrong.');
		}

      if(isset($request->status)){
        $statusvalue = 1;
      }else{
        $statusvalue = 0;
      }


          if($request->coupon_type!="")
          {
            if($request->coupon_type=="2")
            {
              $coupon_discount_type=$request->coupon_discount_type;
              $coupon_discount=$request->coupon_discount;
              $minimum_transaction_amount=$request->minimum_transaction_amount;
            }
            else{
              $coupon_discount_type=0;
              $coupon_discount=0;
              $minimum_transaction_amount=0;
            }
          }
          else{
              $coupon_discount_type=0;
              $minimum_transaction_amount=0;
              $coupon_discount=0;
          }

          if($request->coupon_discount_type!="")
          {
            if($request->coupon_discount_type=="2")
            {
              $coupon_max_amount=$request->coupon_max_amount;
            }
            else{
              $coupon_max_amount=0;
            }
          }
          else{
              $coupon_max_amount=0;
          }

          if($request->user_type!="")
          {
            if($request->user_type=="1")
            {
              $coupon_user_limit=$request->coupon_user_limit;
              $coupon_users=0;
            }
            else{
              $coupon_user_limit=0;
              $coupon_users=implode(',',$request->coupon_users);
            }
          }
          else{
              $coupon_user_limit=0;
              $coupon_users=0;
          }

          $db_coupon_users=$subscriptioncoupon->coupon_users;

          if($subscriptioncoupon->coupon_name==$request->coupon_title)
          {
              $subscriptioncoupon->coupon_start_date = date('Y-m-d',strtotime($request->coupon_start_date));
              $subscriptioncoupon->coupon_end_date = date('Y-m-d',strtotime($request->coupon_end_date));
              $subscriptioncoupon->coupon_users = $coupon_users;
              $subscriptioncoupon->coupon_user_limit = $coupon_user_limit;
              $subscriptioncoupon->coupon_use_per_user = $request->coupon_use_per_user;
              $subscriptioncoupon->coupon_type = $request->coupon_type;
              $subscriptioncoupon->coupon_discount_type = $coupon_discount_type;
              $subscriptioncoupon->coupon_discount = $coupon_discount;
              $subscriptioncoupon->coupon_max_amount = $coupon_max_amount;
              $subscriptioncoupon->minimum_transaction_amount = $minimum_transaction_amount;
              $subscriptioncoupon->coupon_subscription_type = $request->coupon_subscription_type;
              $subscriptioncoupon->coupon_description = $request->coupon_description;
              $subscriptioncoupon->coupon_status = $statusvalue;
          }
          else{
          		try{
		        $checkcoupon=SubscriptionCoupon::where('coupon_name',$request->coupon_title)->where('coupon_type',$request->coupon_type)->first();
		        if($checkcoupon)
		        {
		        	return back()->with('error','Coupon Title already exists.');
		        }
		    }
		    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

              $subscriptioncoupon->coupon_name = $request->coupon_title;
              $subscriptioncoupon->coupon_start_date = date('Y-m-d',strtotime($request->coupon_start_date));
              $subscriptioncoupon->coupon_end_date = date('Y-m-d',strtotime($request->coupon_end_date));
              $subscriptioncoupon->coupon_users = $coupon_users;
              $subscriptioncoupon->coupon_user_limit = $coupon_user_limit;
              $subscriptioncoupon->coupon_use_per_user = $request->coupon_use_per_user;
              $subscriptioncoupon->coupon_type = $request->coupon_type;
              $subscriptioncoupon->coupon_discount_type = $coupon_discount_type;
              $subscriptioncoupon->coupon_discount = $coupon_discount;
              $subscriptioncoupon->coupon_max_amount = $coupon_max_amount;
              $subscriptioncoupon->minimum_transaction_amount = $minimum_transaction_amount;
              $subscriptioncoupon->coupon_subscription_type = $request->coupon_subscription_type;
              $subscriptioncoupon->coupon_description = $request->coupon_description;
              $subscriptioncoupon->coupon_status = $statusvalue;

          }
         try{
            $subscriptioncoupon->save();

            if($db_coupon_users!=0)
            {
            	if($coupon_users!=0)
                {
                  if($request->coupon_type=="1")
                  {
                    $coupontype="Voucher";
                  }
                  else{
                    $coupontype="Coupon";
                  }

                  $coupon_users_array=explode(',',$coupon_users);

                  $db_coupon_users_array=explode(',',$db_coupon_users);

                  $result_coupon_users=array_diff($coupon_users_array,$db_coupon_users_array);

                  if(count($result_coupon_users) > 0)
                  {
                  $coupon_image=url('/').'images/logo/logo_1669793364logo.png';
                  $coupontitle="Purchase a subscription";
                  $coupon_message="Purchase a subscription now and get amazing discount. Use ".$coupontype." ".$request->coupon_title."";
                  sendNotificationtoparticulardevices($result_coupon_users,$coupon_image,$coupontitle,$coupon_message);
                  }
                }
                else{
                  if($request->coupon_type=="1")
                  {
                    $coupontype="Voucher";
                  }
                  else{
                    $coupontype="Coupon";
                  }

                  $coupon_image=url('/').'images/logo/logo_1669793364logo.png';
                  $coupontitle="Purchase a subscription";
                  $coupon_message="Purchase a subscription now and get amazing discount. Use ".$coupontype." ".$request->coupon_title."";
                  sendNotificationtomultipledevices($coupon_image,$coupontitle,$coupon_message);
                }
            }
            else{
            	if($coupon_users!=0)
                {
                  if($request->coupon_type=="1")
                  {
                    $coupontype="Voucher";
                  }
                  else{
                    $coupontype="Coupon";
                  }

                  $coupon_users_array=explode(',',$coupon_users);
                  if(count($coupon_users_array) > 0)
                  {
                  	$coupon_image=url('/').'images/logo/logo_1669793364logo.png';
                  $coupontitle="Purchase a subscription";
                  $coupon_message="Purchase a subscription now and get amazing discount. Use ".$coupontype." ".$request->coupon_title."";
                  sendNotificationtoparticulardevices($coupon_users_array,$coupon_image,$coupontitle,$coupon_message);
                  }
                }
            }

          return redirect('admin/coupon-subscription/')->with('success','Coupon updated !.');

         }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
        $subscriptioncoupon = SubscriptionCoupon::find($id);

        if(is_null($subscriptioncoupon)){
		   return redirect('admin/coupon-subscription')->with('error','Something went wrong.');
		}

        try{
            $subscriptioncoupon->delete();
           return back()->with('success', 'Coupon has been deleted');
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
        $subscriptioncoupon = SubscriptionCoupon::find($id);

        if(is_null($subscriptioncoupon)){
		   return redirect('admin/coupon-subscription')->with('error','Something went wrong.');
		}

        if(isset($request->status)){
            $subscriptioncoupon->coupon_status = 1;
          }else{
            $subscriptioncoupon->coupon_status = 0;
        }

        try{
            $subscriptioncoupon->save();
           return back()->with('success','Coupon updated !');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

    }
    catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
  }


}
