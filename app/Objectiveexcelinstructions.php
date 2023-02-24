<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Objectiveexcelinstructions extends Model
{
  protected $table="objective_excel_instructions";
    protected $fillable = [
      'quiz_id', 'question', 'a','b','c','d','correct_answer','answer_explaination', 'question_image','question_video_link','answer_explaination_image','answer_explaination_video_link'
    ];
    

}
