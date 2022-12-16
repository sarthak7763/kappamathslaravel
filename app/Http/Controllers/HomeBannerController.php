<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Homebanner;
use App\Subject;
use Exception;
use Yajra\Datatables\DataTables;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class HomeBannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
        $subjectalldata=Subject::where('status','1')->get();
          if(!empty($subjectalldata))
          {
            $subjectlist=$subjectalldata->toArray();
          }
          else{
            $subjectlist=[];
          }

          $homebannerdata=Homebanner::orderBy('id','DESC')->get()->first();
          if($homebannerdata)
          {
            $homebannerdataarray=$homebannerdata->toArray();
            $homebannerarray=array(
              'banner_type'=>$homebannerdataarray['banner_type'],
              'title'=>$homebannerdataarray['title'],
              'sub_title'=>$homebannerdataarray['sub_title'],
              'event_date'=>$homebannerdataarray['event_date'],
              'event_link'=>$homebannerdataarray['event_link']
            );
          }
          else{
            $homebannerarray=array(
              'banner_type'=>'',
              'title'=>'',
              'sub_title'=>'',
              'event_date'=>'',
              'event_link'=>''
            );
          }


          return view('admin.home_banner.index', compact('subjectlist','homebannerarray'));
      }catch(\Exception $e){
                  return redirect('admin/')->with('deleted','Something went wrong.');     
               }
    }

    public function submithomebannerinfo(Request $request)
    {
      try{
       $input = $request->all();

        $request->validate([
            'banner_type'=>'required',
            'title' => 'required|string',
            'sub_title'=>'required|string',
            'event_date'=>'required'
        ]);

        $homebannerdata=Homebanner::orderBy('id','DESC')->get()->first();
        if($homebannerdata)
        {
            $homebannerdataarray=$homebannerdata->toArray();
            $homebannerid=$homebannerdataarray['id'];

            $homebanner = Homebanner::find($homebannerid);

            if(is_null($homebanner)){
             return redirect('admin/home_banner')->with('error','Something went wrong.');
          }

          $homebanner->banner_type = $request->banner_type;
          $homebanner->title = $request->title;
          $homebanner->sub_title = $request->sub_title;
          $homebanner->event_date = $request->event_date;
          $homebanner->event_link=$request->event_link;
          try{
          $homebanner->save();
          return redirect('admin/home_banner/')->with('success','Banner updated !.');
        }
          catch(\Exception $e){
              return back()->with('error',$e->getMessage());
           }

        }
        else{

            $homebanner = new Homebanner;
            $homebanner->banner_type = $request->banner_type;
            $homebanner->title = $request->title;
            $homebanner->sub_title = $request->sub_title;
            $homebanner->event_date = $request->event_date;
            $homebanner->event_link=$request->event_link;

            try{
            $homebanner->save();
            return redirect('admin/home_banner/')->with('success','Banner saved !.');
          }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }


        }
      }
      catch(\Exception $e){
                  
                  if($e instanceof ValidationException){
                        $listmessage="";
                        foreach($e->errors() as $list)
                        {
                            $listmessage.=$list[0].'<br/>';
                        }

                        if($listmessage!="")
                        {
                            return back()->with('error',$listmessage);
                        }
                        else{
                            return back()->with('error','Something went wrong12.');
                        }
                        
                    }
                    else{
                        return back()->with('error','Something went wrong11.');
                    }

               }

    }


}
