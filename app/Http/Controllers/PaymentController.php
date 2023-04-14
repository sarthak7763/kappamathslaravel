<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Topic;
use App\Usersubscriptions;

class PaymentController extends Controller
{
  public function index(Request $request)
  {
      try {
        if($request->user_id){
          $userid=$request->user_id;
          $clear_filter=1;
          $data = Usersubscriptions::where('user_id',$request->user_id)->orderBy('id','DESC')->get();
        }else{
          $userid="";
          $clear_filter=0;
          $data = Usersubscriptions::orderBy('id','DESC')->get();
        }
        $users = User::where('role','S')->get();
        return view('admin.payment_history.index', compact('data','users','userid','clear_filter'));
      }
      catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
  }
}
