<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Topic;
use App\topic_user;

class PaymentController extends Controller
{
  public function index()
    {
      $auth = Auth::User();
        if($auth->role == 'A'){
           $data = Topic::all();
        }
        else{
             $data = $auth->topic()->get();
        }
       return view('admin.payment', compact('data'));
     }
}
