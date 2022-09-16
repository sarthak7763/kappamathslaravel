<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	protected $casts = [
        'comingsoon_enabled_ip' => 'array',
    ];
    protected $fillable = [
      'welcome_txt',
      'logo',
      'favicon',
      'wel_mail',
      'coming_soon',
      'comingsoon_enabled_ip',
      'w_email',
      'currency_code',
      'currency_symbol',
      'google_login',
      'fb_login',
      'gitlab_login',
      'right_setting',
      'element_setting',
    ];

}
