<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications;
use App\UserNotification;
use App\User;
use Yajra\Datatables\DataTables;
use Exception;
use DB;
use Illuminate\Validation\ValidationException;
use Validator;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $notifications = \DB::table('notifications')->select('id','title','message','status')->where('send_by','0')->orderBy('id','DESC');

          if($request->ajax()){

            return DataTables::of($notifications)
            ->addIndexColumn()
            ->addColumn('title',function($row){
                return $row->title;
            })
            ->addColumn('message',function($row){
                return $row->message;
            })
            ->addColumn('status',function($row){

            if($row->status=="1")
            {
                $statusvalue="Active";
            }
            else{
                $statusvalue="suspend";
            }

                return $statusvalue;
            })

            ->addColumn('action',function($row){

            if($row->status=="1")
            {
                $checked="checked";
            }
            else{
                $checked="";
            }

              $btn = '<div class="admin-table-action-block">

                    <button type="button" class="btn btn-danger changestatusbtn" data-toggle="modal" data-target="#deleteModal' . $row->id . '">Delete </button></div>';
                   

                     $btn .= '<div id="deleteModal' . $row->id . '" class="delete-modal modal fade" role="dialog">
                  <div class="modal-dialog modal-sm">
                    <!-- Modal content-->
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="delete-icon"></div>
                      </div>
                      <div class="modal-body text-center">
                        <h4 class="modal-heading">Are You Sure ?</h4>
                        <p>Do you really want to delete these records? This process cannot be undone.</p>
                      </div>
                      <div class="modal-footer">
                        <form method="POST" action="' . route("notifications.destroy", $row->id) . '">
                          ' . method_field("DELETE") . '
                          ' . csrf_field() . '
                            <button type="reset" class="btn btn-gray translate-y-3" data-dismiss="modal">No</button>
                            <button type="submit" class="btn btn-danger">Yes</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>';

                // $btn .= '<div id="changestatusModal' . $row->id . '" class="delete-modal modal fade" role="dialog">
                //   <div class="modal-dialog modal-sm">
                //     <!-- Modal content-->
                //     <div class="modal-content">
                //       <div class="modal-header">
                //         <button type="button" class="close" data-dismiss="modal">&times;</button>
                //         <div class="delete-icon"></div>
                //       </div>

                //        <form method="POST" action="' . route("notificationchangestatus") . '">
                //           ' . method_field("POST") . '
                //           ' . csrf_field() . '
                //       <div class="modal-body text-center">
                //         <h4 class="modal-heading">Are You Sure ?</h4>
                //         <p>Do you really want to Change the  status of this record? This process cannot be undone.</p>

                //         <div class="row">
                //         <div class="col-md-6">
                //         <div class="form-group">
                //             <label for="">Status: </label>
                //              <input '.$checked.' type="checkbox" class="toggle-input statusvalue" name="status" id="toggle_status'.$row->id.'">
                //              <label for="toggle_status'.$row->id.'"></label>
                //             <br>
                //           </div>
                //           </div>
                //           </div>

                //       </div>
                //       <div class="modal-footer">
                //           <input type="hidden" name="id" value="'.$row->id.'">
                //             <button type="reset" class="btn btn-gray translate-y-3" data-dismiss="modal">No</button>
                //             <button type="submit" class="btn btn-danger">Yes</button>
                //       </div>
                //     </div>
                //     </form>
                //   </div>
                // </div>';

              return $btn;
            })
            ->escapeColumns(['action'])
            ->rawColumns(['title','message','status','action'])
            ->make(true);

          }

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        return view('admin.notifications.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      try{
        $input = $request->all();
        $request->validate([
          'title' => 'required|string',
          'message'=>'required'
        ]);

        $input['status'] = "1";

        if($file = $request->file('image')) {
          try{
            $request->validate([
              'image' => 'required|mimes:jpeg,png,jpg|max:1024'
            ]);
          } catch(\Exception $e){
            if($e instanceof ValidationException){
              $listmessage=[];
              foreach($e->errors() as $key=>$list){
                $listmessage[$key]=$list[0];
              }
              if(count($listmessage) > 0){
                return back()->with('valid_error',$listmessage);
              }else{
                return back()->with('error','Something went wrong.');
              }
                
            }else{
              return back()->with('error','Something went wrong.');
            }      
          }

          $name = 'notifications_'.time().$file->getClientOriginalName(); 
          $file->move('images/notifications/', $name);
          $image = $name;
        }else{
          $image="";
        }
        $input['image']=$image;
        try{

          $quiz = Notifications::create($input);
          if($quiz){
            $users = User::where('role','S')->get();
            foreach($users as $value){
              $user_notification = [
                'user_id'         => $value['id'],
                'notification_id' => $quiz->id,
                'notification_type'=>'admin'
              ];
              UserNotification::create($user_notification);
            }

            $sendnotify=$this->sendNotificationtomultipledevices($quiz);
          }

          return redirect('/admin/notifications/')->with('success', 'Notification has been added');

        }catch(\Exception $e){
          return back()->with('error',$e->getMessage());     
        }

      }catch(\Exception $e){
        if($e instanceof ValidationException){
          $listmessage=[];
          foreach($e->errors() as $key=>$list){
            $listmessage[$key]=$list[0];
          }
          if(count($listmessage) > 0){
            return back()->with('valid_error',$listmessage);
          }else{
            return back()->with('error','Something went wrong.');
          }
            
        }else{
            return back()->with('error','Something went wrong.');
        }      
      }
         
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      try{
        $notifications = Notifications::findOrFail($id);
        return view('admin.notifications.edit',compact('notifications'));
      }catch(\Exception $e){
        return redirect('admin/notifications/')->with('error','Something went wrong.');     
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
          $request->validate([
            'title' => 'required|string',
            'message'=>'required'
          ]);

          $notifications = Notifications::find($id);
          if(is_null($notifications)){
            return redirect('admin/notifications')->with('error','Something went wrong.');
          }

          if(isset($request->status)){
            $statusvalue = 1;
          }else{
            $statusvalue = 0;
          }

          if ($file = $request->file('image')) {
            try{
              $request->validate([
                'image' => 'required|mimes:jpeg,png,jpg'
              ]);
            }catch(\Exception $e){
              if($e instanceof ValidationException){
                $listmessage=[];
                foreach($e->errors() as $key=>$list){
                  $listmessage[$key]=$list[0];
                }
                if(count($listmessage) > 0){
                  return back()->with('valid_error',$listmessage);
                }else{
                  return back()->with('error','Something went wrong.');
                }
                  
              }else{
                return back()->with('error','Something went wrong.');
              }      
            }
            $name = 'subject_'.time().$file->getClientOriginalName(); 
            $file->move('images/notifications/', $name);
            $notificationimage = $name;
          }else{
            $notificationimage="";
          }

        if($notifications->title==$request->title)
        {
            if($notificationimage!="")
            {
              $notifications->message = $request->message;
              $notifications->image=$notificationimage;
              $notifications->status=$statusvalue;
            }
            else{
              $notifications->message = $request->message;
              $notifications->status=$statusvalue;
            }
            
        }
        else{
            try{
                $subscriptiondata=Notifications::where('title',$request->title)->first();
                if($subscriptiondata)
                {
                    return back()->with('error','Title already exists.');
                }
            }
            catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }

            if($notificationimage!="")
            {
              $notifications->title=$request->title;
              $notifications->message = $request->message;
              $notifications->image=$notificationimage;
              $notifications->status=$statusvalue;
            }
            else{
              $notifications->title=$request->title;
              $notifications->message = $request->message;
              $notifications->status=$statusvalue;
            }
            
        } 

         try{
            $notifications->save();
          return redirect('/admin/notifications/')->with('success','Notification updated !');
         }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

     }
     catch(\Exception $e){          
              if($e instanceof ValidationException){
                $listmessage=[];
                    foreach($e->errors() as $key=>$list)
                    {
                        $listmessage[$key]=$list[0];
                    }

                    if(count($listmessage) > 0)
                    {
                        return back()->with('valid_error',$listmessage);
                    }
                    else{
                        return back()->with('error','Something went wrong.');
                    }
                    
                }
                else{
                    return back()->with('error','Something went wrong.');
                }

            }
               

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
        $notifications = Notifications::find($id);
        UserNotification::where('notification_id',$id)->delete();
        if(is_null($notifications)){
           return redirect('admin/notifications')->with('error','Something went wrong.');
        }

        try{
            $notifications->delete();
           return back()->with('success', 'Notification has been deleted');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }
     }
     catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }
        
    }

    public function changestatus(Request $request)
    {
        try{
        $id=$request->id;
        $notifications = Notifications::find($id);

        if(is_null($notifications)){
           return redirect('admin/notifications')->with('error','Something went wrong.');
        }


        if(isset($request->status)){
            $notifications->status = 1;
          }else{
            $notifications->status = 0;
        }

        try{
            $notifications->save();
           return back()->with('success','Notification updated !');
        }catch(\Exception $e){
            return back()->with('error',$e->getMessage());
         }

     }
     catch(\Exception $e){
                  return back()->with('error','Something went wrong.');     
               }


    }


    public function sendNotificationtomultipledevices($quiz)
    {
        $firebaseTokendata = User::where('push_notifications','1')->where('status','1')->get();

        if($firebaseTokendata)
        {
          $firebaseTokendataarray=$firebaseTokendata->toArray();

          $firebasetokens=[];
          foreach($firebaseTokendata as $list)
          {
            if($list['device_id']!="")
            {
              $firebasetokens[]=$list['device_id'];
            }
          }

          if(count($firebasetokens) > 0)
          {
            $SERVER_API_KEY = env('FCM_SERVER_KEY');
            if($quiz->image!="")
            {
              $quiz_image=url('/').'/images/notifications/'.$quiz->image;
            }
            else{
              $quiz_image="";
            }

          $data = [
              "registration_ids" => $firebasetokens,
              "notification" => [
                  "title" => $quiz->title,
                  "body" => $quiz->message,
                  "image"=>$quiz_image,
              ]
          ];

          $dataString = json_encode($data);

            $headers = [
              'Authorization: key=' . $SERVER_API_KEY,
              'Content-Type: application/json',
          ];
      
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                     
            $response = curl_exec($ch);

            $responsearray=json_decode($response);
        
            return true;

          }
          else{
            return false;
          }
        }
        else{
          return false;
        }
    }


    public function sendNotification($quiz)
    {
        $firebaseTokendata = User::where('email','nahap96510@pyadu.com')->where('push_notifications','1')->get()->first();

        if($firebaseTokendata)
        {
          $firebaseTokendataarray=$firebaseTokendata->toArray();
          $firebaseToken=$firebaseTokendataarray['device_id'];
          $SERVER_API_KEY = env('FCM_SERVER_KEY');

          if($quiz->image!="")
          {
            $quiz_image=url('/').'/images/notifications/'.$quiz->image;
          }
          else{
            $quiz_image="";
          }

          $data = [
            "to" => $firebaseToken,
            "notification" => [
                "title" => $quiz->title,
                "body" => $quiz->message,
                "image"=>$quiz_image,
                'sound' => true,
                'priority' => "high",
                'vibration'=>true,
                'sound'=> "Enabled",  
            ]
        ];
        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
      
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
                 
        $response = curl_exec($ch);

        $responsearray=json_decode($response);

        return true;

        }
        else{
          return false;
        }
    }

}
