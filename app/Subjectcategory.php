<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subjectcategory extends Model
{
	protected $table="subject_category";
    protected $fillable = [
      'subject', 'category_name', 'category_description', 'category_image','category_status'
    ];
}
