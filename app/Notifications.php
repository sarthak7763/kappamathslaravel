<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{

    protected $table="notifications";
    protected $fillable = [
      'title','message','status','image','send_by'
    ];
}
