<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resultmarks extends Model
{
	protected $table="result_marks";
    protected $fillable = [
      'topic_id','subject','user_id','marks','total_marks','total_questions','result_marks_date','result_marks_end_date','result_timer','random_question_ids','question_ids','result_type'
    ];
    

}
