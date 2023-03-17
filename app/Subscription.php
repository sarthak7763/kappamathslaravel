<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{

    protected $table="subscriptions";
    protected $fillable = [
      'title','description','price','subscription_plan','subscription_tenure','subscription_status','paystack_slug'
    ];
}
