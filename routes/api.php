<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ForgotController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\PagesController;

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
    Route::get('getuserinfo', [ProfileController::class, 'getuserinfo']);
    Route::post('changepassword', [ProfileController::class, 'changepassword']);
    Route::post('updateprofile', [ProfileController::class, 'updateprofile']);
    Route::post('updateprofileimage', [ProfileController::class, 'updateprofileimage']);

    Route::post('updateuserpushnotificationsettings', [ProfileController::class, 'updateuserpushnotificationsettings']);

    Route::post('updateuseremailnotificationsettings', [ProfileController::class, 'updateuseremailnotificationsettings']);

    Route::get('course-list', [DashboardController::class, 'getallcourseslist']);

    Route::post('course-topics', [DashboardController::class, 'getcoursetopicslist']);

    Route::post('course-sub-topics', [DashboardController::class, 'getcoursetopicsandsubtopicslist']);

    Route::post('topic-detail', [DashboardController::class, 'gettopicdetailpage']);

    Route::post('sub-topic-detail', [DashboardController::class, 'getsubtopicdetails']);

    Route::post('subscriptions', [PagesController::class, 'getallsubscriptionlist']);

    Route::post('exam-information', [PagesController::class, 'getallexaminformationlist']);

    Route::post('notifications', [PagesController::class, 'getallnotificationslist']);

    Route::post('bulletins', [PagesController::class, 'getallbulletinslist']);

    Route::post('cms-pages', [PagesController::class, 'getcmspagecontent']);

    Route::post('contact-subject', [PagesController::class, 'getcontactsubjectlist']);

});



Route::fallback( function () {
    abort( 405 );
});
