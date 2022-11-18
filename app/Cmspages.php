<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cmspages extends Model
{

    protected $table="cms_pages";
    protected $fillable = [
      'name','slug','description','status'
    ];
}
