<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ForgotController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\PagesController;
use App\Http\Controllers\API\PracticeDashboardController;
use App\Http\Controllers\API\PracticeQuizDashboardController;
use App\Http\Controllers\API\QuizDashboardController;
use App\Http\Controllers\API\QuizResultController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('checkusername',[AuthController::class,'checkusername']);

Route::post('forgotpassword', [ForgotController::class, 'forgotpassword']);
Route::post('resendotp', [ForgotController::class, 'resendotp']);

Route::post('verifyotp', [ForgotController::class, 'verifyotp']);
Route::post('resetpassword', [ForgotController::class, 'resetpassword']);
 
Route::middleware('auth:api')->group(function () {

    //profile controller start

    Route::get('getuserinfo', [ProfileController::class, 'getuserinfo']);
    Route::post('updatepassword', [ProfileController::class, 'updatepassword']);
    Route::post('updateprofile', [ProfileController::class, 'updateprofile']);
    Route::post('updateprofileimage', [ProfileController::class, 'updateprofileimage']);

    Route::post('updateuserpushnotificationsettings', [ProfileController::class, 'updateuserpushnotificationsettings']);

    Route::post('updateuseremailnotificationsettings', [ProfileController::class, 'updateuseremailnotificationsettings']);

    Route::post('updatecoursetopicongoingstatus', [ProfileController::class, 'updatecoursetopicongoingstatus']);

    Route::get('subscribed-courses', [ProfileController::class, 'mysubscribecourselist']);

    //profile controller end

    //dashboard controller start

    Route::get('course-list', [DashboardController::class, 'getallcourseslist']);

    Route::post('course-topics', [DashboardController::class, 'getcoursetopicssubtopicsearchlist']);

    Route::post('course-sub-topics', [DashboardController::class, 'getcoursetopicsandsubtopicslist']);

    Route::post('topic-detail', [DashboardController::class, 'gettopicdetailpage']);

    Route::post('sub-topic-detail', [DashboardController::class, 'getsubtopicdetails']);

    //dashboard controller end

    // pages controller start

    Route::post('subscriptions', [PagesController::class, 'getallsubscriptionlist']);

    Route::post('faq', [PagesController::class, 'getallfaqlist']);

    Route::post('notifications', [PagesController::class, 'getallnotificationslist']);

    Route::post('bulletins', [PagesController::class, 'getallbulletinslist']);

    Route::post('cms-pages', [PagesController::class, 'getcmspagecontent']);

    Route::post('contact-subject', [PagesController::class, 'getcontactsubjectlist']);

    Route::post('send-enquiry', [PagesController::class, 'sendcontactenquiry']);

    //pages controller end

    //quiz dashboard controller start

    Route::post('getobjectivequestions', [QuizDashboardController::class, 'getsubtopicobjectivequizquestions']);

    Route::post('submitobjectivequizquestion', [QuizDashboardController::class, 'submitobjectivequizquestion']);

    Route::post('skipobjectivequizquestion', [QuizDashboardController::class, 'skipobjectivequizquestion']);

    Route::post('objectivequizquestionexplaination', [QuizDashboardController::class, 'getobjectivequizquestionexplaination']);

    Route::post('objectivequizquestiondetails', [QuizDashboardController::class, 'getobjectivequizquestiondetails']);

    Route::post('gettheoryquestions', [QuizDashboardController::class, 'getsubtopictheoryquizquestions']);

     Route::post('theoryquizquestionexplaination', [QuizDashboardController::class, 'gettheoryquizquestionexplaination']);

    Route::post('theoryquizquestiondetails', [QuizDashboardController::class, 'gettheoryquizquestiondetails']);

    //quiz dashboard controller end

    //practice dashboard controller start

    Route::get('practice-dashboard', [PracticeDashboardController::class, 'getpracticedashboardinfo']);

    Route::post('practice-dashboard-topics', [PracticeDashboardController::class, 'getcoursetopicsbyquiztype']);

    //practice dashboard controller end

    //practice quiz dashboard controller start

    Route::post('practice-objective-quiz', [PracticeQuizDashboardController::class, 'getpracticeobjectivequiz']);

    Route::post('practice-objective-quiz-explaination', [PracticeQuizDashboardController::class, 'getpracticeobjectivequizquestionexplaination']);

    Route::post('practice-objective-quiz-details', [PracticeQuizDashboardController::class, 'getpracticeobjectivequizquestiondetails']);

    Route::post('submit-practice-objective-quiz', [PracticeQuizDashboardController::class, 'submitpracticeobjectivequizquestion']);

    Route::post('practice-theory-quiz', [PracticeQuizDashboardController::class, 'getpracticetheoryquizquestions']);

    Route::post('practice-theory-quiz-explaination', [PracticeQuizDashboardController::class, 'getpracticetheoryquizquestionexplaination']);

    Route::post('practice-theory-quiz-details', [PracticeQuizDashboardController::class, 'getpracticetheoryquizquestiondetails']);

    //practice quiz dashboard controller end

    //quiz result controller start

        Route::post('quiz-result', [QuizResultController::class, 'getquizresultminisummary']);

        Route::post('quiz-result-summary', [QuizResultController::class, 'viewquizresultquestionsummary']);

        Route::get('manage-result', [QuizResultController::class, 'manageuserresult']);

        Route::post('view-result', [QuizResultController::class, 'viewuserresult']);

    // quiz result controller end

});



Route::fallback( function () {
    abort( 405 );
});
