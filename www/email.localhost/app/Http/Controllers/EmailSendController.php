<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailSendController extends Controller
{
    public static function send_from_request(Request $request): string
    {
        $email = $request->get('email');
        $from = $request->get('from');
        $to = $request->get('to');
        $subj = $request->get('subj');
        $body = $request->get('body');
        return static::send($email, $from,  $to, $subj, $body);
    }
    public static function send(string $email, string $from, string $to, string $subj, string $body): int
    {
        $executionTime = rand(1,10);
        sleep($executionTime);
        return $executionTime;
    }
}
