<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
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
      'a_image',
      'b_image',
      'c_image',
      'd_image',
      'question_status',
      'option_status'
    ];

    public function answers() {
      return $this->hasOne('App\Answer');
    }

    public function topic() {
      return $this->belongsTo('App\Topic');
    }
}
