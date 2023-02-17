<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Theoryexcelinstructions extends Model
{
  protected $table="theory_excel_instructions";
    protected $fillable = [
      'quiz_id', 'question', 'answer_explaination', 'question_image','question_video_link','answer_explaination_image','answer_explaination_video_link'
    ];
    

}
