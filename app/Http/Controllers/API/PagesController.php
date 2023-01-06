<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Bulletin;
use App\Examinformation;
use App\Notifications;
use App\Subscription;
use App\Cmspages;
use App\Contactsubject;
use App\Contactenquiry;
use Validator;
use Hash;

class PagesController extends BaseController
{

    public function getallsubscriptionlist()
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$subscriptiondata=Subscription::where('subscription_status','1')->get();
	        	if($subscriptiondata)
	        	{
	        		$subscriptiondataarray=$subscriptiondata->toArray();
	        		if($subscriptiondataarray)
	        		{
	        			$subscriptionlist=[];
	        			foreach($subscriptiondataarray as $list)
	        			{

	        				$subscription_date=$list['subscription_tenure'].' '.$list['subscription_plan'];

	        				$subscriptionlist[]=array(
	        					'subscription_id'=>$list['id'],
	        					'title'=>$list['title'],
	        					'price'=>$list['price'],
	        					'subscription_date'=>$subscription_date,
	        					'description'=>$list['description']
	        				);
	        			}
	        		}
	        		else{
	        			$subscriptionlist=[];
	        		}
	        	}
	        	else{
	        		$subscriptionlist=[];
	        	}

	        	$success['subscriptionlist'] =  $subscriptionlist;
                return $this::sendResponse($success, 'Subscription List.');
		        
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }  
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>$e->getMessage()]);    
               }
    }

    public function getallfaqlist()
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$examinformationdata=Examinformation::where('status','1')->get();
	        	if($examinformationdata)
	        	{
	        		$examinformationdataarray=$examinformationdata->toArray();
	        		if($examinformationdataarray)
	        		{
	        			$faqlist=[];
	        			foreach($examinformationdataarray as $list)
	        			{
	        				$createddate=date('d M, Y',strtotime($list['created_at']));

	        				$faqlist[]=array(
	        					'faq_id'=>$list['id'],
	        					'question'=>$list['question'],
	        					'answer'=>$list['answer'],
	        					'date'=>$createddate
	        				);
	        			}
	        		}
	        		else{
	        			$faqlist=[];
	        		}
	        	}
	        	else{
	        		$faqlist=[];
	        	}

	        	$success['faqlist'] =  $faqlist;
                return $this::sendResponse($success, 'Exam Information List.');
		        
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }  
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }

    public function getallnotificationslist()
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$notificationdata=Notifications::where('status','1')->get();
	        	if($notificationdata)
	        	{
	        		$notificationdataarray=$notificationdata->toArray();
	        		if($notificationdataarray)
	        		{
	        			$notificationlist=[];
	        			foreach($notificationdataarray as $list)
	        			{
	        				$createddate=date('d M, Y',strtotime($list['created_at']));

	        				if($list['image']!="")
					        {
					        	$notification_image=url('/').'/images/notifications/'.$list['image'];
					        }
					        else{
					        	$notification_image='';
					        }
	        				
	        				$notificationlist[]=array(
	        					'notification_id'=>$list['id'],
	        					'title'=>$list['title'],
	        					'image'=>$notification_image,
	        					'message'=>$list['message'],
	        					'date'=>$createddate
	        				);
	        			}
	        		}
	        		else{
	        			$notificationlist=[];
	        		}
	        	}
	        	else{
	        		$notificationlist=[];
	        	}

	        	$success['notificationlist'] =  $notificationlist;
                return $this::sendResponse($success, 'Notification List.');
		        
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }  
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }

    public function getcmspagecontent(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$request->validate([
		            'slug'=>'required'
		        ]);

		        $pageslug=$request->slug;
		        $cmspagedata=Cmspages::where('slug',$pageslug)->get()->first();
		        if($cmspagedata)
		        {
		        	$cmspagedataarray=$cmspagedata->toArray();
		        	$pagedet=array(
		        		'name'=>$cmspagedataarray['name'],
		        		'description'=>$cmspagedataarray['description']
		        	);

		        	$success['pagedet'] =  $pagedet;
                	return $this::sendResponse($success, 'CMS Page Details.');
		        }
		        else{
		        	return $this::sendError('Unauthorised.', ['error'=>'Something went wrong.']); 
		        }
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }
	    }
	    catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }


    public function getcontactsubjectlist()
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {
	        	$contactsubjectdata=Contactsubject::where('status','1')->get();
	        	if($contactsubjectdata)
	        	{
	        		$contactsubjectdataarray=$contactsubjectdata->toArray();
	        		if($contactsubjectdataarray)
	        		{
	        			$contactsubjectlist=[];
	        			foreach($contactsubjectdataarray as $list)
	        			{
	        				$createddate=date('d M, Y',strtotime($list['created_at']));
	        				
	        				$contactsubjectlist[]=array(
	        					'subject_id'=>$list['id'],
	        					'name'=>$list['name']
	        				);
	        			}
	        		}
	        		else{
	        			$contactsubjectlist=[];
	        		}
	        	}
	        	else{
	        		$contactsubjectlist=[];
	        	}

	        	$success['contactsubjectlist'] =  $contactsubjectlist;
                return $this::sendResponse($success, 'Contact Subject List.');
		        
	        }
	        else{
	        	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']); 
	        }  
    	}
    	catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }

    public function sendcontactenquiry(Request $request)
    {
    	try{
	        $user=auth()->user();
	        if($user)
	        {

		        $validator = Validator::make($request->all(), [
		            'name' =>'required',
		            'number'=>'required',
		            'email'=>'required',
		            'subject'=>'required',
		            'message'=>'required'
		        ]);

		        if($validator->fails()){
		            return $this::sendValidationError('Validation Error.',['error'=>$validator->messages()->all()[0]]);       
		        }

	        	$name=$request->name;
	        	$number=$request->number;
	        	$email=$request->email;
	        	$subject=$request->subject;
	        	$message=$request->message;

	        	try{

	        	$contactsubject = Contactsubject::find($subject);
		          if(is_null($contactsubject)){
		           return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);
		        }

                  $contactenquiry = new Contactenquiry;
                  $contactenquiry->name=$name;
                  $contactenquiry->email = $email;
                  $contactenquiry->number = $number;
                  $contactenquiry->subject = $subject;
                  $contactenquiry->message = $message;
                  $contactenquiry->status=1;
                  $contactenquiry->save();

                  $success=[];
                	return $this::sendResponse($success, 'successfully send.');
                 
              }catch(\Exception $e){
                return $this::sendError('Unauthorised Exception.', ['error'=>'Something went wrong']);     
             }

		    }
		    else{
		    	return $this::sendUnauthorisedError('Unauthorised.', ['error'=>'Please login again.']);
		    }
		}
		catch(\Exception $e){
                  return $this::sendExceptionError('Unauthorised Exception.', ['error'=>'Something went wrong']);    
               }
    }

    





}