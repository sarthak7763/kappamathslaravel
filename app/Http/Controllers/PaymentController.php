<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Topic;
use App\Usersubscriptions;
use App\Subscription;
use App\SubscriptionCoupon;
use DB;

class PaymentController extends Controller
{
  public function index(Request $request)
  {
      try {
        if($request->user_id){
          $userid=$request->user_id;
          $clear_filter=1;
          $usersubscriptiondata = Usersubscriptions::where('user_id',$request->user_id)->orderBy('id','DESC')->get();

        }else{
          $userid="";
          $clear_filter=0;
          $usersubscriptiondata = Usersubscriptions::orderBy('id','DESC')->get();
        }

        if($usersubscriptiondata)
        {
          $usersubscriptiondataarray=$usersubscriptiondata->toArray();
          $user_subscription_array=[];
          foreach($usersubscriptiondataarray as $item)
          {
            $userdet=User::find($item['user_id']);
            if(is_null($userdet))
            {
              $username="";
            }
            else{
              $username=$userdet->name;
            }



            $subscriptiondet=Subscription::find($item['subscription_id']);
            if(is_null($userdet))
            {
              $subscriptionname="";
            }
            else{
              $subscriptionname=$subscriptiondet->title;
            }

            $coupondet=SubscriptionCoupon::find($item['coupon_code_id']);
            if(is_null($coupondet))
            {
              $couponname="";
              $coupon_type="";
            }
            else{
              $couponname=$coupondet->coupon_name;
              if($coupondet->coupon_type=="1")
              {
                $coupon_type="Voucher Code";
              }
              else{
                $coupon_type="Coupon Code";
              }
            }

            $usercouponcodedet=DB::table('user_coupon_code')->where('id',$item['user_coupon_code_id'])->get()->first();
            if($usercouponcodedet)
            {
              $coupon_discount=$usercouponcodedet->coupon_discount;
              $total_amount=$usercouponcodedet->total_amount;
              $subtotal=$usercouponcodedet->subtotal;
            }
            else{
              $coupon_discount="";
              $total_amount="";
              $subtotal="";
            }

            $user_subscription_array[]=array(
              'username'=>$username,
              'subscriptionname'=>$subscriptionname,
              'couponname'=>$couponname,
              'coupon_type'=>$coupon_type,
              'coupon_discount'=>$coupon_discount,
              'total_amount'=>$total_amount,
              'subtotal'=>$subtotal,
              'transaction_id'=>$item['transaction_id'],
              'subscription_payment'=>$item['subscription_payment'],
              'subscription_start'=>$item['subscription_start'],
              'subscription_end'=>$item['subscription_end'],
              'subscription_status'=>$item['subscription_status']
            );

          }
        }
        else{
          $user_subscription_array=[];
        }

        $users = User::where('role','S')->get();
        return view('admin.payment_history.index', compact('user_subscription_array','users','userid','clear_filter'));
      }
      catch(\Exception $e){
                  return back()->with('error',$e->getMessage());     
               }
  }
}
