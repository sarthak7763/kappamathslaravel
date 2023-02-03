<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Theoryquizresult extends Model
{
  protected $table="theory_quiz_result";
    protected $fillable = [
      'topic_id', 'user_id', 'result_type', 'random_questions','result_date'
    ];
    

}
