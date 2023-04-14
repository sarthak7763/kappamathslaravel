<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tempquestions extends Model
{
    protected $table="temp_questions";
    protected $fillable = [
      'topic_id',
      'question',
      'question_latex',
      'a',
      'a_latex',
      'b',
      'b_latex',
      'c',
      'c_latex',
      'd',
      'd_latex',
      'answer',
      'code_snippet',
      'answer_exp',
      'answer_exp_latex',
      'question_img',
      'question_video_link',
      'question_status'
    ];
}
