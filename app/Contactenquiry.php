<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contactenquiry extends Model
{

    protected $table="contact_enquiry";
    protected $fillable = [
      'name','email','number','subject','message','status'
    ];
}
