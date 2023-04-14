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

        $subscription = \DB::table('subscriptions')->where('created_at','>=',$filter_start_date)->where('created_at','<=',$filter_end_date)->select('id','title','description','price','subscription_plan','subscription_tenure','subscription_status','flag');
      }
      elseif($request->filter_start_date!="" && $request->filter_end_date=="")
      {
        $filter_start_date=date('Y-m-d',strtotime($request->filter_start_date));

        $subscription = \DB::table('subscriptions')->where('created_at','>=',$filter_start_date)->select('id','title','description','price','subscription_plan','subscription_tenure','subscription_status','flag');
      }
      elseif($request->filter_start_date=="" && $request->filter_end_date!="")
      {
        $filter_end_date=date('Y-m-d',strtotime($request->filter_end_date));

          $filter_end_date=date('Y-m-d', strtotime("+1 day", strtotime($filter_end_date)));

        $subscription = \DB::table('subscriptions')->where('created_at','<=',$filter_end_date)->select('id','title','description','price','subscription_plan','subscription_tenure','subscription_status','flag');
      }
      else{
        $subscription = \DB::table('subscriptions')->select('id','title','description','price','subscription_plan','subscription_tenure','subscription_status','flag');
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

              $btn = '';
              $btn .= '<div class="admin-table-action-block">';
              if($row->flag == "1"){
                $btn   .=  '<a href="' . route('subscription.edit', $row->id) . '" data-toggle="tooltip" data-original-title="Edit" class="btn btn-primary btn-floating"><i class="fa fa-pencil"></i></a>';
              }
              $btn   .= '<button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-status="'.$row->subscription_status.'" data-target="#changestatusModal' . $row->id . '">Change Status </button></div>';

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
          'days'=>'Day',
          'months'=>'Month',
          'years'=>'Year'
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
        		$paystack_create_payment_page=$this->createpaymentpage($input);

        		if($paystack_create_payment_page['code']==200)
        		{
        			$input['paystack_slug']=$paystack_create_payment_page['slug'];
        			$quiz = Subscription::create($input);
		           return redirect('/admin/subscription/')->with('success', 'Subscription Plan has been added');
        		}
        		else{
        			return back()->with('error',$paystack_create_payment_page['message']);
        		}
		         
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


    public function createpaymentpage($input)
    {

		  $url = "https://api.paystack.co/page";
		  $custom=[
		  	'plan_date'=>$input['subscription_tenure'],
		  	'plan_time'=>$input['subscription_plan']
		  ];

		  $fields = [
		    'name' => $input['title'],
		    'description' => $input['description'],
		    'amount' => $input['price']*100,
		    'redirect_url'=>url('/').'/successpage',
		    'custom_fields'=>$custom
		  ];

		  $fields_string = http_build_query($fields);

		  //open connection
		  $ch = curl_init();
		  
		  //set the url, number of POST vars, POST data
		  curl_setopt($ch,CURLOPT_URL, $url);
		  curl_setopt($ch,CURLOPT_POST, true);
		  curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    "Authorization: Bearer ".env('Paystack_secret_key'),
		    "Cache-Control: no-cache",
		  ));
		  
		  //So that curl_exec returns the contents of the cURL; rather than echoing it
		  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		  
		  //execute post
		  $result = curl_exec($ch);
		  curl_close($ch);

		  $resultarray=json_decode($result);
		  if($resultarray->status==1)
		  {
		  	$successdata=$resultarray->data;
		  	$returndata=array('code'=>200,'message'=>$resultarray->message,'slug'=>$successdata->slug);
		  }
		  else{
		  	$returndata=array('code'=>400,'message'=>$resultarray->message);
		  }
		  return $returndata;
    }

    public function updatepaymentpage($subscriptiondata)
    {
		  $url = "https://api.paystack.co/page/".$subscriptiondata['paystack_slug'];

		  if($subscriptiondata['status']=="1")
		  {
		  	$activestatus='true';
		  }
		  else{
		  	$activestatus='false';
		  }

		  $custom=[
		  	'plan_date'=>$subscriptiondata['plan_date'],
		  	'plan_time'=>$subscriptiondata['plan_time']
		  ];

		  $fields = [
		    'name' => $subscriptiondata['title'],
		    'description' => $subscriptiondata['description'],
		    'custom_fields'=>$custom,
		    'active'=>$activestatus
		  ];

		  $fields_string = http_build_query($fields);

		  //open connection
		  $ch = curl_init();
		  
		  //set the url, number of POST vars, POST data
		  curl_setopt($ch,CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		  curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    "Authorization: Bearer ".env('Paystack_secret_key'),
		    "Cache-Control: no-cache",
		  ));
		  
		  //So that curl_exec returns the contents of the cURL; rather than echoing it
		  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		  
		  //execute post
		  $result = curl_exec($ch);
		  curl_close($ch);
		  $resultarray=json_decode($result);
		  if($resultarray->status==1)
		  {
		  	$successdata=$resultarray->data;
		  	$returndata=array('code'=>200,'message'=>$resultarray->message);
		  }
		  else{
		  	$returndata=array('code'=>400,'message'=>$resultarray->message);
		  }
		  return $returndata;

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
              'days'=>'Day',
              'months'=>'Month',
              'years'=>'Year'
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
            $subscription->subscription_tenure = $request->subscription_tenure;
            $subscription->subscription_plan = $request->subscription_plan;
            $subscription->subscription_status=$statusvalue;
        } 

         try{

         	$subscriptiondata=array(
        		'paystack_slug'=>$subscription->paystack_slug,
        		'title'=>$subscription->title,
        		'status'=>$subscription->subscription_status,
        		'description'=>$subscription->description,
        		'amount'=>$subscription->price,
        		'plan_date'=>$subscription->subscription_tenure,
        		'plan_time'=>$subscription->subscription_plan
        	);

        	$paystack_update_payment_page=$this->updatepaymentpage($subscriptiondata);
        	if($paystack_update_payment_page['code']==200)
        	{
        		$subscription->save();
           		return redirect('admin/subscription/')->with('success','Subscription Plan updated !');
        	}
        	else{
        		return back()->with('error',$paystack_update_payment_page['message']); 
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
        	$subscriptiondata=array(
        		'paystack_slug'=>$subscription->paystack_slug,
        		'title'=>$subscription->title,
        		'status'=>$subscription->subscription_status,
        		'description'=>$subscription->description,
        		'amount'=>$subscription->price,
        		'plan_date'=>$subscription->subscription_tenure,
        		'plan_time'=>$subscription->subscription_plan
        	);

        	$paystack_update_payment_page=$this->updatepaymentpage($subscriptiondata);
        	if($paystack_update_payment_page['code']==200)
        	{
        		$subscription->save();
           		return back()->with('success','Subscription Plan updated !');
        	}
        	else{
        		return back()->with('error',$paystack_update_payment_page['message']); 
        	}
            
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }


    }
}
