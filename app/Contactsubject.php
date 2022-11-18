<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contactsubject extends Model
{

    protected $table="contact_subject";
    protected $fillable = [
      'name','status'
    ];
}
