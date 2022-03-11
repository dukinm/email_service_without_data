<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Artisan::command('CustomersWithAnExpiringSubscription:AddToTheQueue', function () {
    $this->info(App\Http\Controllers\AddingCustomersWithAnExpiringSubscriptionToTheQueueController::getCustomerWithAnExpiringSubscriptionAndAddItToTheQueueController());
});
Artisan::command('ExpiringSubscriptionEmail:Send', function () {
    $this->info(App\Http\Controllers\SendingExpiringSubscriptionEmailFromTheQueueAndSaveSendStateController::getExpriringSubsriptionEmailFromRabbitMQThenSendItAndSaveStateToLog());
});
Artisan::command('ValidateEmail:Run', function () {
    $this->info(App\Http\Controllers\ValidateEmailFromTheDatabaseController::getEmailFromDatabaseValidateAndUpdateInfo());
});
