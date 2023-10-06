<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionCoupon extends Model
{
  protected $table="subscription_coupon";
    protected $fillable = [
      'coupon_name','coupon_date','coupon_time','coupon_users','coupon_user_limit','coupon_use_per_user','coupon_type','coupon_discount','coupon_max_amount','coupon_status','coupon_subscription_type','coupon_description','minimum_transaction_amount'
    ];
    

}
