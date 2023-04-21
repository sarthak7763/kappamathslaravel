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

}