<?php

namespace App\Http\Middleware;

use Closure;
use App\Setting;

class ComingSoon
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $setting = Setting::first();
        //dd($_SERVER['REMOTE_ADDR']);
        if(isset($setting) && $setting->coming_soon =='1'){
            if(isset($setting->comingsoon_enabled_ip) && $setting->comingsoon_enabled_ip != NULL){
                $macaddress = $_SERVER['REMOTE_ADDR'];
                $enabled_ip = $setting->comingsoon_enabled_ip;
                //dd($enabled_ip);
                if(in_array($macaddress, $enabled_ip,true))
                {
                    return $next($request);
                   
                }
                else{
                  
                    abort(503);
                   
                }
            }else{
                return $next($request);
            }
            
        }
        else{
            return $next($request);
        }
    }
}
