<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Homebanner extends Model
{
	protected $table="home_banner";
    protected $fillable = [
      'banner_type', 'title', 'sub_title', 'event_date','event_time','event_link'
    ];
}
