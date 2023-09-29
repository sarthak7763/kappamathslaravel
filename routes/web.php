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

Route::post('/password/adminforgotpassword','Auth\ForgotPasswordController@adminforgotpassword')->name('adminforgotpassword');

Route::get('/password/reset-password','Auth\ForgotPasswordController@resetpassword')->name('resetpassword');

Route::get('/password/reset-password/{code}','Auth\ForgotPasswordController@resetpassword')->name('resetpassword');

Route::post('/password/adminresetpassword','Auth\ForgotPasswordController@adminresetpassword')->name('adminresetpassword');

  Auth::routes();

  Route::get('/home', function(){
    return redirect()->route('login');
});

  Route::get('/user/forgot-password/','Userforgotpassword@index');

  Route::get('/user/forgot-password/{code}','Userforgotpassword@index');
  
  Route::post('/resetuserpassword','Userforgotpassword@resetuserpassword')->name('resetuserpassword');

  Route::resource('/admin/users', 'UsersController');
  Route::post('/admin/users/changestatus','UsersController@changestatus')->name('userchangestatus');

  Route::get('/admin/users/result/{id}','UsersController@userresult');

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

  

  // route for processing payment\
  Route::post('payment/paypal_post', 'PaypalController@paypal_post')->name('paypal_post');
  // Handle status
  Route::get('payment/paypal_success', 'PaypalController@paypal_success')->name('paypal_success');
  Route::get('payment/paypal_cancel', 'PaypalController@paypal_cancel')->name('paypal_cancel');

});


Route::group(['middleware'=> 'isadmin'], function(){

  Route::get('/print/report/aspdf/{id}/{userid}','AllReportController@pdfreport')->name('pdf.report');

  Route::delete('delete/sheet/quiz/{id}','TopicController@deleteperquizsheet')->name('del.per.quiz.sheet');

  Route::any('/admin','DashboardController@index');

  Route::delete('reset/response/{topicid}/{userid}','AllReportController@delete');

  Route::any('/admin/all_reports', 'AllReportController@index');

  Route::any('/admin/top_report', 'TopReportController@index');

  Route::resource('/admin/quiz-topics', 'QuizTopicController');

  Route::post('/admin/quiz-topics/getsubjectcategorylist','QuizTopicController@getsubjectcategorylist')->name('getquizsubjectcategorylist');

  Route::post('/admin/quiz-topics/getcoursetopiclist','QuizTopicController@getcoursetopiclist')->name('getcoursetopiclist');

  Route::post('/admin/quiz-topics/changestatus','QuizTopicController@changestatus')->name('quiztopicchangestatus');

  Route::resource('/admin/questions', 'QuestionsController');

  Route::post('/admin/questions/','QuestionsController@index')->name('questionsindex');

  Route::get('/admin/quiz_editor','QuestionsController@quizEditor')->name('quizEditor');

  Route::post('/admin/questions/moveQtnAns','QuestionsController@moveQtnAns')->name('moveQtnAns');

  Route::post('/admin/questions/deleteimagefromdb','QuestionsController@deleteimagefromdb')->name('deleteimagefromdb');

  Route::get('/admin/view-question/{id}','QuestionsController@viewquestion')->name('viewquestion');

  Route::get('/admin/import_questions_module','QuestionsController@import_questions_module')->name('import_questions_module');

  Route::post('/admin/questions/import_theory_questions', 'QuestionsController@importTheoryquestionExcelToDB')->name('import_theory_questions');

  Route::post('/admin/questions/submitimporttempquestions', 'QuestionsController@submitimporttempquestions')->name('submitimporttempquestions');

  Route::post('/admin/questions/import_objective_questions', 'QuestionsController@importObjectivequestionExcelToDB')->name('import_objective_questions');

  Route::post('/admin/questions/import_objective_questions_images', 'QuestionsController@importObjectivequestionImageExcelToDB')->name('import_objective_questions_images');
  
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

  Route::resource('/admin/subscription-coupon', 'SubscriptionCodeController');
  
  Route::post('/admin/subscription-coupon/changestatus','SubscriptionCodeController@changestatus')->name('subscriptioncouponchangestatus');

  Route::resource('/admin/course-topic', 'CoursetopicController');

  Route::post('/admin/course-topic/changestatus','CoursetopicController@changestatus')->name('coursetopicchangestatus');

  Route::post('/admin/questions/getquizlist','QuestionsController@getquizlist')->name('getquizlist');

  Route::get('/admin/questions/showquiz/{id}','QuestionsController@showquiz')->name('questions.showquiz');

  Route::post('/admin/questions/storeobjectivequiz','QuestionsController@storeobjectivequiz')->name('storeobjectivequiz');

  Route::post('/admin/questions/storetheoryquiz','QuestionsController@storetheoryquiz')->name('storetheoryquiz');

    Route::patch('/admin/questions/updatetheoryquiz/{id}','QuestionsController@updatetheoryquiz')->name('updatetheoryquiz');

  Route::post('/admin/questions/changestatus','QuestionsController@changestatus')->name('questionchangestatus');

  Route::get('/admin/questions/create/{id}','QuestionsController@create')->name('questions.create'); 

  Route::post('/admin/course-topic/getsubjectcategorylist','CoursetopicController@getsubjectcategorylist')->name('getsubjectcategorylist');

  Route::post('/admin/postAcceptor','QuestionsController@postAcceptor');

  Route::resource('/admin/subscription', 'SubscriptionController');
  Route::post('/admin/subscription/changestatus','SubscriptionController@changestatus')->name('subscriptionchangestatus');

  Route::resource('/admin/theory-excel-instructions', 'TheoryExcelController');
  Route::post('/admin/theory-excel-instructions/changestatus','TheoryExcelController@changestatus')->name('theoryexcelchangestatus');

  Route::resource('/admin/objective-excel-instructions', 'ObjectiveExcelController');
  Route::post('/admin/objective-excel-instructions/changestatus','ObjectiveExcelController@changestatus')->name('objectiveexcelchangestatus');

  Route::resource('/admin/notifications', 'NotificationController');
  Route::post('/admin/notifications/changestatus','NotificationController@changestatus')->name('notificationchangestatus');

  Route::post('/admin/notifications/sendNotification','NotificationController@sendNotification')->name('sendNotification');

  Route::post('/admin/notifications/sendNotificationtomultipledevices','NotificationController@sendNotificationtomultipledevices')->name('sendNotificationtomultipledevices');

  Route::resource('/admin/faq', 'ExamInformationController');
  Route::post('/admin/faq/changestatus','ExamInformationController@changestatus')->name('examinformationchangestatus');

  Route::resource('/admin/bulletin', 'BulletinController');
  Route::post('/admin/bulletin/changestatus','BulletinController@changestatus')->name('bulletinchangestatus');

  Route::resource('/admin/cms-pages', 'CmsPagesController');
  Route::post('/admin/cms-pages/changestatus','CmsPagesController@changestatus')->name('cmspageschangestatus');

  Route::resource('/admin/contact-subject', 'ContactController');
  Route::post('/admin/contact-subject/changestatus','ContactController@changestatus')->name('contactsubjectchangestatus');

  Route::get('/admin/contact-enquiry','ContactController@contactenquiry')->name('contactenquirypage');

  Route::get('/admin/home_banner/','HomeBannerController@index');
  Route::post('/admin/home_banner/submithomebannerinfo','HomeBannerController@submithomebannerinfo')->name('submithomebannerinfo');

  Route::get('/admin/get_objective_question_sample_export','QuestionsController@get_objective_question_sample_export')->name('get_objective_question_sample_export');

  Route::get('/admin/get_theory_question_sample_export','QuestionsController@get_theory_question_sample_export')->name('get_theory_question_sample_export');

  Route::get('/admin/get_objective_question_images_sample_export','QuestionsController@get_objective_question_images_sample_export')->name('get_objective_question_images_sample_export');

  Route::any('/admin/payment', 'PaymentController@index')->name('admin.payment');
  
});

Route::get('/cms-pages/{slug}','CmsPagesController@viewcmspagecontent')->name('viewcmspagecontent');

Route::get('/cms-pages','CmsPagesController@viewcmspagecontent')->name('viewcmspagecontent');

Route::get('/view-quiz-question/{slug}','QuestionsController@viewquizquestion')->name('viewquizquestion');




Route::fallback( function () {
    abort( 405 );
});