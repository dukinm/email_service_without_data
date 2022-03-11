<?php

namespace App\Http\Controllers;
use App\Models\Clickhouse\EmailSendLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class SendingExpiringSubscriptionEmailFromTheQueueAndSaveSendStateController extends Controller
{
    public static function getExpriringSubsriptionEmailFromRabbitMQThenSendItAndSaveStateToLog(){
        $speed = 2600;
        if( Cache::has('subscription_email_speed_in_min')){
            $speed = Cache::get('subscription_email_speed_in_min');
        }
        $connection = new AMQPStreamConnection(env('RABBIT_MQ_HOST'), env('RABBIT_MQ_POST'), env('RABBIT_MQ_USERNAME'), env('RABBIT_MQ_PASSWORD'));
        $channel = $connection->channel();

        $channel->queue_declare('EmailForSend', false, false, false, false);

        $encodedEmailsData = [];
        $callback = function ($msg) use(&$encodedEmailsData) {
            $encodedEmailsData[]=$msg->body;
        };

        $channel->basic_consume('EmailForSend', '', false, true, false, false, $callback);

        while (count($encodedEmailsData)<=$speed && $channel->is_open) {
            $channel->wait();
        }
        try {
            $curls     = [];
            $multiCURL = curl_multi_init();

            $logData = [];
            $emailGroup = array_chunk($encodedEmailsData, 200);
            foreach ($emailGroup as $encodedEmailsData){

                foreach ( $encodedEmailsData as $encodedEmailData ) {
                    $curls[ $encodedEmailData ] = curl_init();
                    $logData[ $encodedEmailData ] = json_decode($encodedEmailData,1);
                    curl_setopt_array( $curls[ $encodedEmailData], [
                        CURLOPT_URL => 'http://web:80/api/send-email?email=info@email.localhost&from=Subscription%20bot&to='.urlencode($logData[ $encodedEmailData ]['email']).'&subj=Subscription%20is%20expiring&body='.urlencode($logData[ $encodedEmailData ]['username']).',%20your%20subscription%20is%20expiring%0Asoon',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 40,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json'
                        ),
                    ] );
                    curl_multi_add_handle( $multiCURL, $curls[ $encodedEmailData ] );
                }

                do {
                    $status = curl_multi_exec( $multiCURL, $active );
                    if ( $active ) {
                        curl_multi_select( $multiCURL );
                    }
                } while ( $active && $status == CURLM_OK );

                $output = [];
                foreach ( $curls as $encodedEmailData => $curl ) {
                    curl_multi_remove_handle( $multiCURL, $curl );
                    $output[ $encodedEmailData ] = curl_multi_getcontent( $curl );
                }
                curl_multi_close( $multiCURL );
                foreach ($output as $encodedEmailData => $executionTime){
                    $logData[ $encodedEmailData ] ['time_to_send_in_s'] = intval($executionTime);
                    $logData[ $encodedEmailData ] ['created_at'] = date("Y-m-d H:i:s");

                }
            }
//            var_export(array_values($logData));
            EmailSendLog::insertAssoc(array_values($logData));
            return true;
        } catch ( \Exception $e ) {
            error_log( 'Произошла ошибка при отправке сообщений о заканчивающейся подписке "' . $e->getmessage() );
            return FALSE;
        }
    }
}
