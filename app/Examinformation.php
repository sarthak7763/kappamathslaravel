<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Examinformation extends Model
{

    protected $table="exam_information";
    protected $fillable = [
      'question','answer','status'
    ];
}
