<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/test',function (){
   return App\Http\Controllers\SendingExpiringSubscriptionEmailFromTheQueueAndSaveSendStateController::getExpriringSubsriptionEmailFromRabbitMQThenSendItAndSaveStateToLog();
});
Route::get('/send-email',function (Request $request){
   return App\Http\Controllers\EmailSendController::send_from_request($request);
});
