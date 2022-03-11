<?php

namespace Tests\Feature;

use App\Http\Controllers\EmailValidationController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailValidationTest extends TestCase
{
    /**
     * Тестируем массив разных email на корректность валидации
     *
     * @return void
     */
    public function test_check_email()
    {
        $tests = [
            [
                'email'=>'mike@yaru',
                'result'=>[
                    'ok'=>true,
                    'data'=>[
                        'email'=>'mike@ya.ru',
                        'local'=>'mike',
                        'type'=>'PERSONAL',
                        'domain'=>'ya.ru',
                        'qc'=>4,
                        'can_send_email'=>true,
                    ],
                    'errors'=>[]
                ],
            ],
            [
                'email'=>'mike@yandex.ru',
                'result'=>[
                    'ok'=>true,
                    'data'=>[
                        'email'=>'mike@yandex.ru',
                        'local'=>'mike',
                        'type'=>'PERSONAL',
                        'domain'=>'yandex.ru',
                        'qc'=>0,
                        'can_send_email'=>true,
                    ],
                    'errors'=>[]
                ],
            ],
            [
                'email'=>'mike@example.ru',
                'result'=>[
                    'ok'=>true,
                    'data'=>[
                        'email'=>'mike@example.ru',
                        'local'=>'mike',
                        'type'=>'CORPORATE',
                        'domain'=>'example.ru',
                        'qc'=>0,
                        'can_send_email'=>true,
                    ],
                    'errors'=>[]
                ],
            ],
            [
                'email'=>'info@yandex.ru',
                'result'=>[
                    'ok'=>true,
                    'data'=>[
                        'email'=>'info@yandex.ru',
                        'local'=>'info',
                        'type'=>'ROLE',
                        'domain'=>'yandex.ru',
                        'qc'=>0,
                        'can_send_email'=>true,
                    ],
                    'errors'=>[]
                ],
            ],
            [
                'email'=>'temp-email@yandex.ru',
                'result'=>[
                    'ok'=>true,
                    'data'=>[
                        'email'=>'temp-email@yandex.ru',
                        'local'=>'temp-email',
                        'type'=>'DISPOSABLE',
                        'domain'=>'yandex.ru',
                        'qc'=>3,
                        'can_send_email'=>false,
                    ],
                    'errors'=>[]
                ],
            ],
            [
                'email'=>'mike@@yandex.ru',
                'result'=>[
                    'ok'=>false,
                    'data'=>[
                        'qc'=>1,
                    ],
                    'errors'=>['Email содержит более одной собаки']
                ],
            ],
            [
                'email'=>'mikeyandex.ru',
                'result'=>[
                    'ok'=>false,
                    'data'=>[
                        'qc'=>1,
                    ],
                    'errors'=>['Email не содержит собаки']
                ],
            ],

        ];
        foreach ($tests as $test){
            $result = EmailValidationController::check_email($test['email']);
            if($result['ok']===$test['result']['ok'] && empty(array_diff_assoc($test['result']['data'], $result['data'])) && empty(array_diff($test['result']['errors'], $test['result']['errors']))){
                $this->assertTrue(TRUE);
            }
            else{
                var_export($test);
                var_export($result);
                $this->assertTrue(FALSE);
            }
        }
    }
}
