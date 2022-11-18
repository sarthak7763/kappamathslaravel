<?php

use App\User;
use App\Quiztopic;
use App\Answer;
use App\copyrighttext;
use App\Question;
use Illuminate\Support\Facades\Auth;
use App\Page;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware'=> 'coming_soon'], function(){

Route::get('/', function(){
    return redirect()->route('login');
});

Route::post('/login/checkwebuserlogin','Auth\LoginController@checkwebuserlogin')->name('checkwebuserlogin');

  Auth::routes();

  Route::get('/home', function(){
    return redirect()->route('login');
});

  Route::get('/user/forgot-password/{code}','Userforgotpassword@index');
  
  Route::post('/resetuserpassword','Userforgotpassword@resetuserpassword')->name('resetuserpassword');

  Route::resource('/admin/users', 'UsersController');
  Route::post('/admin/users/changestatus','UsersController@changestatus')->name('userchangestatus');

  Route::get('/admin/profile', function(){
    if (Auth::check()) {
      return view('admin.users.profile');
    } else {
      return redirect('/');
    }
  });
  
  Route::get('/admin/my_reports', 'MyReportsController@index')->name('my_report');
  Route::get('/admin/my_reports/{my_reports}', 'MyReportsController@show')->name('my_report_show');

  Route::get('admin/moresettings/socialicons/','SocialController@index')->name('socialicons.index');
  Route::post('/admin/moresettings/socialicons/insert','SocialController@store')->name('social.store');
  Route::put('/admin/moresettings/socialicons/active/{id}','SocialController@active')->name('social.active');
  Route::put('/admin/moresettings/socialicons/deactive/{id}','SocialController@deactive')->name('social.deactive');
  Route::delete('/admin/moresettings/socialicons/delete/{id}','SocialController@destroy')->name('social.delete');

  Route::get('/admin/custom-style-settings', 'CustomStyleController@addStyle')->name('customstyle');
  Route::post('/admin/custom-style-settings/addcss','CustomStyleController@storeCSS')->name('css.store');
  Route::post('/admin/custom-style-settings/addjs','CustomStyleController@storeJS')->name('js.store');

  //payment gateway
  Route::get('/admin/mail','ApiController@setApiView')->name('api.setApiView');
  Route::post('/admin/mail','ApiController@changeEnvKeys')->name('api.update');

  Route::get('admin/sociallogin/','ApiController@facebook')->name('set.facebook');
  Route::post('admin/facebook','ApiController@updateFacebookKey')->name('key.facebook');
  Route::post('admin/google','ApiController@updateGoogleKey')->name('key.google');
  Route::post('admin/gitlab','ApiController@updategitlabKey')->name('key.gitlab');

  Route::delete('admin/ans/{id}','Anscontroller@destroy')->name('ans.del');

  Route::get('/admin/payment', 'PaymentController@index')->name('admin.payment');

  // route for processing payment\
  Route::post('payment/paypal_post', 'PaypalController@paypal_post')->name('paypal_post');
  // Handle status
  Route::get('payment/paypal_success', 'PaypalController@paypal_success')->name('paypal_success');
  Route::get('payment/paypal_cancel', 'PaypalController@paypal_cancel')->name('paypal_cancel');

});


Route::group(['middleware'=> 'isadmin'], function(){

  Route::get('/print/report/aspdf/{id}/{userid}','AllReportController@pdfreport')->name('pdf.report');

  Route::delete('delete/sheet/quiz/{id}','TopicController@deleteperquizsheet')->name('del.per.quiz.sheet');

  Route::get('/admin', function()
  {
    $user = User::where('role', '!=', 'A')->count();
    $question = Question::count();
    $quiz = Quiztopic::count();
    $user_latest = User::where('id', '!=', Auth::id())->orderBy('created_at', 'desc')->get();
    return view('admin.dashboard', compact('user', 'question', 'quiz', 'user_latest'));
    //remove the answer line comment
    // return view('admin.dashboard', compact('user', 'question', 'answer', 'quiz', 'user_latest'));

  });

  Route::delete('reset/response/{topicid}/{userid}','AllReportController@delete');

  Route::resource('/admin/all_reports', 'AllReportController');
  Route::resource('/admin/top_report', 'TopReportController');

  Route::resource('/admin/quiz-topics', 'QuizTopicController');

  Route::post('/admin/quiz-topics/getsubjectcategorylist','QuizTopicController@getsubjectcategorylist')->name('getquizsubjectcategorylist');

  Route::post('/admin/quiz-topics/getcoursetopiclist','QuizTopicController@getcoursetopiclist')->name('getcoursetopiclist');

  Route::post('/admin/quiz-topics/changestatus','QuizTopicController@changestatus')->name('quiztopicchangestatus');

  Route::resource('/admin/questions', 'QuestionsController');
  Route::post('/admin/questions/import_questions', 'QuestionsController@importExcelToDB')->name('import_questions');
  Route::resource('/admin/answers', 'AnswersController');
  Route::resource('/admin/settings', 'SettingController');

  Route::post('/admin/users/destroy', 'DestroyAllController@AllUsersDestroy');
  Route::post('/admin/answers/destroy', 'DestroyAllController@AllAnswersDestroy');
  
  Route::get('/admin/pages','PagesController@index')->name('pages.index');
  Route::get('/admin/pages/add','PagesController@add')->name('pages.add');
  Route::post('/admin/pages/add','PagesController@store')->name('pages.store');
  Route::get('pages/{slug}','PagesController@show')->name('page.show');
  Route::get('/admin/pages/edit/{id}','PagesController@edit')->name('pages.edit');
  Route::put('/admin/pages/edit/{id}','PagesController@update')->name('pages.update');
  Route::delete('/delete/pages/{id}','PagesController@destroy')->name('pages.delete'); 

  Route::get('admin/moresettings/faq/','FAQController@index')->name('faq.index');
  Route::get('admin/moresettings/faq/add','FAQController@create')->name('faq.add');
  Route::post('/admin/moresettings/faq/insert','FAQController@store')->name('faq.store');
  Route::get('/admin/moresettings/faq/edit/{id}','FAQController@edit')->name('faq.edit');
  Route::put('/admin/moresettings/faq/edit/{id}','FAQController@update')->name('faq.update');
  Route::delete('/faq/delete/{id}','FAQController@destroy')->name('faq.delete');

  Route::get('admin/moresettings/copyright','CopyrighttextController@index')->name('copyright.index');
  Route::put('admin/moresettings/copyright/{id}','CopyrighttextController@update')->name('copyright.update');

  Route::get('/admin/mail-settings','Configcontroller@getset')->name('mail.getset');
  Route::post('admin/mail-settings', 'Configcontroller@changeMailEnvKeys')->name('mail.update');

  Route::resource('/admin/subject', 'SubjectController');
  Route::post('/admin/subject/changestatus','SubjectController@changestatus')->name('subjectchangestatus');

  Route::resource('/admin/course-category', 'SubjectcategoryController');
  
  Route::post('/admin/course-category/changestatus','SubjectcategoryController@changestatus')->name('subjectcategorychangestatus');

  Route::resource('/admin/course-topic', 'CoursetopicController');

  Route::post('/admin/course-topic/changestatus','CoursetopicController@changestatus')->name('coursetopicchangestatus');

  Route::post('/admin/questions/getquizlist','QuestionsController@getquizlist')->name('getquizlist');

  Route::post('/admin/questions/storeobjectivequiz','QuestionsController@storeobjectivequiz')->name('storeobjectivequiz');

  Route::post('/admin/questions/storetheoryquiz','QuestionsController@storetheoryquiz')->name('storetheoryquiz');

    Route::patch('/admin/questions/updatetheoryquiz/{id}','QuestionsController@updatetheoryquiz')->name('updatetheoryquiz');

  Route::post('/admin/questions/changestatus','QuestionsController@changestatus')->name('questionchangestatus');

  Route::get('/admin/questions/create/{id}','QuestionsController@create')->name('questions.create'); 

  Route::post('/admin/course-topic/getsubjectcategorylist','CoursetopicController@getsubjectcategorylist')->name('getsubjectcategorylist');

  Route::post('/admin/postAcceptor','QuestionsController@postAcceptor');

  Route::resource('/admin/subscription', 'SubscriptionController');
  Route::post('/admin/subscription/changestatus','SubscriptionController@changestatus')->name('subscriptionchangestatus');

  Route::resource('/admin/notifications', 'NotificationController');
  Route::post('/admin/notifications/changestatus','NotificationController@changestatus')->name('notificationchangestatus');

  Route::resource('/admin/exam-information', 'ExamInformationController');
  Route::post('/admin/exam-information/changestatus','ExamInformationController@changestatus')->name('examinformationchangestatus');

  Route::resource('/admin/bulletin', 'BulletinController');
  Route::post('/admin/bulletin/changestatus','BulletinController@changestatus')->name('bulletinchangestatus');

  Route::resource('/admin/cms-pages', 'CmsPagesController');
  Route::post('/admin/cms-pages/changestatus','CmsPagesController@changestatus')->name('cmspageschangestatus');

});



Route::fallback( function () {
    abort( 405 );
});