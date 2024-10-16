<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Setting;
use App\Question;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        view()->composer('*', function ($view)
         {
             $auth = Auth::user();
             $setting = Setting::whereId(1)->first();
             $c_questions = Question::count();
             $view->with(['auth' => $auth, 'setting' => $setting, 'c_questions' => $c_questions]);
         });


    Validator::extend('question_check', function ($attribute, $value, $parameters, $validator) {
        $inputs = $validator->getData();
        $excelquestion = htmlentities($inputs['question']);
        $checkquestion=Question::where('question',$excelquestion)->get()->first();
        if($checkquestion)
        {
            $result = false;
        }
        else{
            $result = true;
        }
        return $result;
    });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
