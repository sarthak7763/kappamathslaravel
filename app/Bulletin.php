<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{

    protected $table="bulletins";
    protected $fillable = [
      'question','answer','status'
    ];
}
