<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ValidateEmailFromTheDatabaseController extends Controller
{
    public static function getEmailFromDatabaseValidateAndUpdateInfo(){
        $lastValidatedEmailId = 0;
//        Cache::forever('last_validated_email_id', 0);
//        Cache::forever('email_validation_service_need_start', true);
//        return '';
        if( Cache::has('last_validated_email_id')){
            $lastValidatedEmailId = Cache::get('last_validated_email_id');
        }




        if( Cache::has('email_validation_service_need_start') == true || (!Cache::has('email_validation_service_need_start'))){
            Cache::forever('email_validation_service_need_start', false);
            Email::where('id','>',$lastValidatedEmailId)->chunkById(100, function ($emails) {
                foreach ($emails as $email){
                    $emailValidated = EmailValidationController::check_email($email->source);
                    if(!empty($emailValidated['data'])){
                        $email->update($emailValidated['data']);
                    }
                    Cache::forever('last_validated_email_id', $email->id);

                }
            }, $column = 'id');
            Cache::forever('email_validation_service_need_start', true);
        }
    }
}
