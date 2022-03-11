<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailValidationController extends Controller
{
    public static function check_email($email){
//        sleep(rand(1,60));

        $output = ['ok'=>true,'data'=>[],'errors'=>[]];

        if(rand(3,100)<=3){ // в 3% случаев имитируем ошибку сервиса
            $output['ok']=false;
            $output['errors'][]=['Закончились средства на балансе'];
        }
        else if(substr_count($email,'@')===0){
            $output['ok']=false;
            $output['errors'][]=['Email не содержит собаки'];
            $output['data']['qc']=1;
        }
        else if(substr_count($email,'@')>1){
            $output['ok']=false;
            $output['errors'][]=['Email содержит более одной собаки'];
            $output['data']['qc']=1;
        }
        else{
            // имитируем сервис стандартизации email от DaData
            $emailAtParts = explode('@',$email);
            $output['data']['local'] = $emailAtParts[0];
            $output['data']['domain'] = $emailAtParts[1];
            $personalDomains = ['gmail.com','mail.ru','ya.ru','yandex.ru','list.ru','hotmail.com','live.com'];
            $domainWithoutDots = ['gmailcom'=>'gmail.com','mailru'=>'mail.ru','yaru'=>'ya.ru','yandexru'=>'yandex.ru','listru'=>'list.ru','hotmail.com'=>'hotmailcom','livecom'=>'live.com'];
            $oneTimePart = 'temp';
            $roles = ['info','sale','support','no-reply','robot','admin'];
            $output['data']['qc']=0;
            if(substr_count($output['data']['local'],$oneTimePart)>0 || substr_count($output['data']['domain'],$oneTimePart)>0){
                $output['data']['type']='DISPOSABLE';
                $output['data']['qc']=3;
            }
            else if(in_array($output['data']['local'],$roles)){
                $output['data']['type']='ROLE';
            }
            else if(in_array($output['data']['domain'],$personalDomains)){
                $output['data']['type']='PERSONAL';
            }
            else if(isset($domainWithoutDots[$output['data']['domain']])){
                $output['data']['type']='PERSONAL';
                $output['data']['domain'] = $domainWithoutDots[$output['data']['domain']];
                $output['data']['qc']=4;
            }
            else{
                $output['data']['type']='CORPORATE';
            }
            if($output['data']['qc']===0 || $output['data']['qc']===4){
                $output['data']['can_send_email']=true;
            }
            else{
                $output['data']['can_send_email']=false;
            }
            $output['data']['email'] = $output['data']['local'].'@'.$output['data']['domain'];


        }
        return $output;

    }
}
