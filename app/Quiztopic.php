<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quiztopic extends Model
{
    protected $fillable = [
      'subject','category','course_topic','title', 'per_q_mark', 'description', 'timer','quiz_status'
    ];

    public function question(){
      return $this->hasOne('App\Question');
    }

    public function answer(){
      return $this->hasOne('App\Answer');
    }
    
    public function user() {
      return $this->belongsToMany('App\User','topic_user')
         ->withPivot('amount','transaction_id', 'status')
        ->withTimestamps();
    }
}
