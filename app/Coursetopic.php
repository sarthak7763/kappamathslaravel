<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coursetopic extends Model
{
    protected $fillable = [
      'subject','category','topic_name','topic_description','topic_image','topic_video_id','topic_status','sort_order'
    ];
}
