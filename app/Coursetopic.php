<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coursetopic extends Model
{
    protected $fillable = [
      'category', 'topic_name', 'topic_description', 'topic_image','topic_status'
    ];
}
