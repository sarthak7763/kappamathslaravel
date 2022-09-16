<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::all();
        return view('admin.settings', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        // return $request;
        $setting = Setting::findOrFail($id);

        $request->validate([
          'logo' => 'image|mimes:jpeg,png,jpg',
          'favicon' => 'mimes:ico',
          'userquiz' => 'int',
          'w_email' => 'required|email',
          'currency_code'=>'required',
          'currency_symbol'=>'required'

        ]);

        $input = $request->all();

         if (isset($request->APP_DEBUG)) {
            $env_update = $this->changeEnv(['APP_DEBUG' => 'true']);
        } else {
            $env_update = $this->changeEnv(['APP_DEBUG' => 'false']);
        }

         $env_update = $this->changeEnv([
            'APP_URL' => preg_replace('/\s+/', '', $request->APP_URL),
        ]);
       

        if ($file = $request->file('logo')) 
        {
            $name = 'logo_'.time().$file->getClientOriginalName();
            unlink(public_path().'/images/logo/'.$setting->logo);
            $file->move('images/logo/', $name);
            $input['logo'] = $name;
        }

        if ($file2 = $request->file('favicon')) 
        {
            $name2 = $file2->getClientOriginalName();
            unlink(public_path().'/images/logo/'.$setting->favicon);
            $file2->move('images/logo/', $name2);
            $input['favicon'] = $name2;
        }

        if(isset($request->rightclick)){
            $input['right_setting'] = 1;
        }
        else{
            $input['right_setting'] = 0;
        }

        if(isset($request->inspect)){
            $input['element_setting'] = 1;
        }
        else{
            $setting->element_setting = 0;
        }

        if($request->wel_mail){
           $input['wel_mail'] = 1;
        }
        else{
            $input['wel_mail'] = 0;
        }

        if(isset($request->coming_soon))
        {
            $request->validate([
                'comingsoon_enabled_ip' => 'required'
            ]);
           $input['coming_soon'] = 1;
           $input['comingsoon_enabled_ip'] = $request->comingsoon_enabled_ip;
        }
        else
        { 
            $input['coming_soon'] = 0;
            $input['comingsoon_enabled_ip'] = NULL;
        }
        //return $input;
        try
        {
            $setting->update($input);
           
            
            return back()->with('updated', 'Settings have been saved !');
        }
        catch(\Exception $e)
        {
            return back()->with('deleted', $e->getMessage());
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
        //
    }

    protected function changeEnv($data = array())
    {
        {
            if (count($data) > 0) {

                // Read .env-file
                $env = file_get_contents(base_path() . '/.env');

                // Split string on every " " and write into array
                $env = preg_split('/\s+/', $env);

                // Loop through given data
                foreach ((array) $data as $key => $value) {
                    // Loop through .env-data
                    foreach ($env as $env_key => $env_value) {
                        // Turn the value into an array and stop after the first split
                        // So it's not possible to split e.g. the App-Key by accident
                        $entry = explode("=", $env_value, 2);

                        // Check, if new key fits the actual .env-key
                        if ($entry[0] == $key) {
                            // If yes, overwrite it with the new one
                            $env[$env_key] = $key . "=" . $value;
                        } else {
                            // If not, keep the old one
                            $env[$env_key] = $env_value;
                        }
                    }
                }

                // Turn the array back to an String
                $env = implode("\n\n", $env);

                // And overwrite the .env with the new data
                file_put_contents(base_path() . '/.env', $env);

                return true;

            } else {

                return false;
            }
        }
    }
}
