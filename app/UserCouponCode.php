<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCouponCode extends Model
{
  protected $table="user_coupon_code";
    protected $fillable = [
      'user_id', 'subtotal', 'coupon_discount', 'total_amount','coupon_code','paystack_slug','amount_array_json'
    ];

}
