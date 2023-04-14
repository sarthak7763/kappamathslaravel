<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\DataTables;
use Exception;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;
use App\Result;
use App\Resultmarks;
use App\Subject;
use App\Subjectcategory;
use App\Coursetopic;
use App\Quiztopic;
use App\Question;
use App\User;
use App\Subscription;
use App\Usersubscriptions;
use Vimeo\Vimeo;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	if($request->date_filter_start!="" || $request->date_filter_end!="")
    	{
            $current_date=date('Y-m-d');

    		$new_filter_date_start=$request->date_filter_start;
            $new_filter_date_end=$request->date_filter_end;

            if($request->date_filter_start!="")
            {
                $filter_date_start=date('Y-m-d',strtotime($request->date_filter_start));

                if($filter_date_start > $current_date)
                {
                    return back()->with('error','Alert! cannot select future dates.');
                }

            }
            else{
                $filter_date_start="";
            }
    		
            if($request->date_filter_end!="")
            {
                $filter_date_end=date('Y-m-d',strtotime($request->date_filter_end));

                if($filter_date_start!="")
                {
                    if($filter_date_end < $filter_date_start)
                    {
                        return back()->with('error','End date must be greater than or equal to start date.');
                    }
                }
                
            }
            else{
                $filter_date_end="";
            }

            if($filter_date_start!="" && $filter_date_end!="")
            {
                $clear_filter=1;
                $user=User::where('role','S')->where('created_at','>=',$filter_date_start)->where('created_at','<=',$filter_date_end)->count();

                $topic = Subjectcategory::where('created_at','>=',$filter_date_start)->where('created_at','<=',$filter_date_end)->count();

                $subtopic = Coursetopic::where('created_at','>=',$filter_date_start)->where('created_at','<=',$filter_date_end)->count();

                $quiz=Quiztopic::where('created_at','>=',$filter_date_start)->where('created_at','<=',$filter_date_end)->count();

                $subscription=Subscription::where('created_at','>=',$filter_date_start)->where('created_at','<=',$filter_date_end)->count();

                $revenue=Usersubscriptions::where('created_at','>=',$filter_date_start)->where('created_at','<=',$filter_date_end)->sum('subscription_payment');

            }
            elseif($filter_date_start!="" && $filter_date_end=="")
            {
                $clear_filter=1;
                $user=User::where('role','S')->where('created_at','>=',$filter_date_start)->count();

                $topic = Subjectcategory::where('created_at','>=',$filter_date_start)->count();

                $subtopic = Coursetopic::where('created_at','>=',$filter_date_start)->count();

                $quiz=Quiztopic::where('created_at','>=',$filter_date_start)->count();

                $subscription=Subscription::where('created_at','>=',$filter_date_start)->count();

                $revenue=Usersubscriptions::where('created_at','>=',$filter_date_start)->sum('subscription_payment');

            }
            elseif($filter_date_start=="" && $filter_date_end!="")
            {
                $clear_filter=1;
                $user=User::where('role','S')->where('created_at','<=',$filter_date_end)->count();

                $topic = Subjectcategory::where('created_at','<=',$filter_date_end)->count();

                $subtopic = Coursetopic::where('created_at','<=',$filter_date_end)->count();

                $quiz=Quiztopic::where('created_at','<=',$filter_date_end)->count();

                $subscription=Subscription::where('created_at','<=',$filter_date_end)->count();

                $revenue=Usersubscriptions::where('created_at','<=',$filter_date_end)->sum('subscription_payment');
            
            }
            else{
                $clear_filter=0;
                $new_filter_date_start="";
                $new_filter_date_end="";
                $user = User::where('role','S')->count();
                $topic = Subjectcategory::count();
                $subtopic = Coursetopic::count();
                $quiz=Quiztopic::count();
                $subscription=Subscription::count();
                $revenue=Usersubscriptions::sum('subscription_payment');
            }
    	}
    	else{
            $clear_filter=0;
    		$new_filter_date_start="";
            $new_filter_date_end="";
    		$user = User::where('role','S')->count();
    		$topic = Subjectcategory::count();
    		$subtopic = Coursetopic::count();
    		$quiz=Quiztopic::count();
    		$subscription=Subscription::count();
    		$revenue=Usersubscriptions::sum('subscription_payment');
    	}
    	return view('admin.dashboard', compact('user','topic','subtopic','quiz','subscription','revenue','new_filter_date_start','new_filter_date_end','clear_filter'));
    }

    public function testvideo()
    {
        $client_id="ecd7d48e0299335886dc51d0ad5b92fac1428165";

        $client_secret="FApdXH5wHL5gNsIqLM4YChGvJGiAmXsiSGX1sDUTaGg3tDsTHNCf+GYQHVw+BbY0k8g0DfgWej3oXMNyXSdzdW/RHwCF3o6rIwX00/CN3cO2b21hiuyk575iPmXURo66";

        $access_token="fbae76cd368c2e5d55929cb96ca7fe2d";

        $client = new Vimeo($client_id,$client_secret,$access_token);
        $video_id ="809305521";
        $response = $client->request("/videos/$video_id");
        print_r($response);
    }

}