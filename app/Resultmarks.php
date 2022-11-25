<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resultmarks extends Model
{
	protected $table="result_marks";
    protected $fillable = [
      'topic_id', 'user_id','marks','total_marks','total_questions','result_marks_date','result_timer'
    ];
    

}
