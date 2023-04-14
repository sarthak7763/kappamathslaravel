<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usersubscriptions extends Model
{
  protected $table="user_subscriptions";
    protected $fillable = [
      'user_id', 'subscription_id', 'transaction_id', 'subscription_payment','subscription_start','subscription_end','subscription_status'
    ];

    public function getUser(){
      return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getSubscription(){
      return $this->hasOne(Subscription::class, 'id', 'subscription_id');
    }

}
