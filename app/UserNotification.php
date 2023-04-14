<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getNotification(){
        return $this->hasOne(Notifications::class, 'id', 'notification_id');
    }
}
