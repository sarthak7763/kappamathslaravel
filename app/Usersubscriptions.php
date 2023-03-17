<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usersubscriptions extends Model
{
  protected $table="user_subscriptions";
    protected $fillable = [
      'user_id', 'subscription_id', 'transaction_id', 'subscription_payment','subscription_start','subscription_end','subscription_status'
    ];
    

}
