<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
  protected $table="results";
    protected $fillable = [
      'topic_id', 'user_id', 'question_id', 'user_answer', 'answer','marks','result_date'
    ];
    

}
