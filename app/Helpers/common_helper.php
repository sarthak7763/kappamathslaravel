<?php
use Illuminate\Http\Request;
use App\Subscription;
use App\Usersubscriptions;
use App\User;

function checkvimeovideoid($videoid)
{
	if($videoid!="")
	{
		$curlSession = curl_init();
     		curl_setopt($curlSession, CURLOPT_URL, 'https://player.vimeo.com/video/'.$videoid.'/config');
              curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
              curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

              $jsonData = json_decode(curl_exec($curlSession));
              curl_close($curlSession);

              if(isset($jsonData->message))
              {
                 $data=array('code'=>'400','message'=>$jsonData->message);
                  return $data;

              }
              else{

                  if(count($jsonData->request->files->progressive) >0)
                  {
                    $subtopicvideourl=$jsonData->request->files->progressive[0]->url;
                  }
                  else{
                    $subtopicvideourl="";
                  }

                  $sub_topic_image=$jsonData->video->thumbs->base;

              	  $data=array('code'=>'200','message'=>'Successfully','subtopicvideourl'=>$subtopicvideourl,'sub_topic_image'=>$sub_topic_image);
                  return $data;
              }
	}
	else{
		$data=array('code'=>'400','message'=>'Please enter valid Video ID');
        return $data;
	}
	
}

function getVideoDetails($video_id)
    {
      if($video_id=="")
      {
        $data=array('code'=>'400','message'=>'Please enter valid Video ID');
        return $data;
      }

        $vimeo_api_key=env('Vimeo_access_token');   
        $options = array('http' => array(
            'method'  => 'GET',
            'header' => 'Authorization: Bearer '.$vimeo_api_key
        ));
        $context  = stream_context_create($options);

        $hash = json_decode(file_get_contents("https://api.vimeo.com/videos/{$video_id}",false, $context));
        header("Content-Type: text/plain");

        if(isset($hash->files))
        {
          $videofiles=$hash->files;
          if(isset($videofiles[3]) && $videofiles[3]->rendition=="adaptive")
          {
              $video_url=$videofiles[3]->link;
              $video_rendition=$videofiles[3]->rendition;
          }
          else{
             $video_url=$videofiles[0]->link;
              $video_rendition=$videofiles[0]->rendition;
          }
        }
        else{
          $video_url='';
          $video_rendition='';
        }
        

        $thumbanilsizes=$hash->pictures->sizes;
        if(isset($thumbanilsizes[2]) && $thumbanilsizes[2]->width=="295")
        {
            $thumbnailurl=$thumbanilsizes[2]->link;
            $thumbnailurlwidth=$thumbanilsizes[2]->width;
        }
        else{
            $thumbnailurl=$thumbanilsizes[0]->link;
            $thumbnailurlwidth=$thumbanilsizes[0]->width;
        }

    if($video_url!="")
    {
      return array(
          'code'=>200,
          'message'=>'Successfully',
          'title'=>$hash->name,
          'sub_topic_image'=>$thumbnailurl,
          'thumbnailurlwidth'=>$thumbnailurlwidth,
          'subtopicvideourl'=>$video_url,
          'video_rendition'=>$video_rendition
      );
    }
    else{
      $data=array('code'=>'400','message'=>'Video not found.');
      return $data;
    }
    
  }


function checkusersubscription($userid)
{
    if($userid!="")
    {
        $user_subscriptions_list=Usersubscriptions::where('user_id',$userid)->where('subscription_status',1)->get()->first();
            if($user_subscriptions_list)
            {
              $user_subscriptions_listarr=$user_subscriptions_list->toArray();
              if($user_subscriptions_listarr)
              {
                $currentdate=date('Y-m-d');
                $subscription_start=$user_subscriptions_listarr['subscription_start'];
                $subscription_end=$user_subscriptions_listarr['subscription_end'];

                if($currentdate >= $subscription_start && $currentdate <= $subscription_end)
                {

                  $subscriptionarray=1;
                  return $subscriptionarray;
                }
                else{
                    $subscriptionarray=0;
                    return $subscriptionarray;
                }
              }
              else{
                  $subscriptionarray=0;
                  return $subscriptionarray;
              }
            }
            else{
                $subscriptionarray=0;
                return $subscriptionarray;
            }
    }
    else{
          $subscriptionarray=0;
          return $subscriptionarray;
    }
}

?>