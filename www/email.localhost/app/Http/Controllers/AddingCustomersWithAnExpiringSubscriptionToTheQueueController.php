<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AddingCustomersWithAnExpiringSubscriptionToTheQueueController extends Controller
{
    public static function getCustomerWithAnExpiringSubscriptionAndAddItToTheQueueController(){
        $query=DB::table('users')->select('username','email')->where('email_verified','=',1)->where('subscription_is_valid_until','<',date("Y-m-d H:i:s",strtotime('+3 days')));
        $count = $query->count();
        if($count>0){
            Cache::forever('subscription_email_speed_in_min', round($count/60/10)); // считаем, что всю рассылку нам нужно совершить за 10 часов - это среднее рекомендуемое время для больших рассылок
            $connection = new AMQPStreamConnection(env('RABBIT_MQ_HOST'), env('RABBIT_MQ_POST'), env('RABBIT_MQ_USERNAME'), env('RABBIT_MQ_PASSWORD'));
            $channel = $connection->channel();
            $channel->queue_declare('EmailForSend', false, false, false, false);
            $query->orderBy('id')->chunk(100000, function ($users) use ($channel) {
                foreach ($users as $user){
                    $msg = new AMQPMessage(json_encode(['email'=>$user->email,'username'=>$user->username]));
                    $channel->basic_publish($msg, '', 'EmailForSend');
                }
            });
            $channel->close();
            $connection->close();
        }
        return 'Добавлено '.$count.' писем в очередь';
    }
}
