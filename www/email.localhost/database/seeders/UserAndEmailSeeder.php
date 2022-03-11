<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserAndEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $domains = ['gmail.com','mail.ru','ya.ru','my_company.com','list.ru','hotmail.com','live.com'];
        $roles = ['info','sale','support','no-reply','robot','admin'];
        $topLevelDomains = ['ru','com','su','tv','net','info','ws'];
        $subscriptionIsValidUntil = date("Y-m-d H:i:s",strtotime('+2 days'));
        $now = date("Y-m-d H:i:s");
        DB::table('users')->truncate();
        DB::table('emails')->truncate();
        for ($i = 0; $i<2000000; $i++){
            $name = Str::random(20);
            $emailVerified = true;
            // В 2% случаев делаем ролевой email
            if(rand(1,100)<=2){
                $email = $roles[array_rand($roles,1)].'@'.Str::random(rand(12,20)).'.'.$topLevelDomains[array_rand($topLevelDomains,1)];
            }
            else{
                $email = Str::random(rand(12,20)).'@'.$domains[array_rand($domains,1)];
            }
            // В 4% случаев вносим ошибку в адрес
            if(rand(1,100)<=4){
                $emailVerified = false;
                $error = rand(1,3);
                $email = match ($error) {
                    1 => str_replace('@', '@@', $email),
                    2 => str_replace('@', '', $email),
                    3 => str_replace('.', '', $email),
                };
            }
            // Еще в 20% случаев убираем галку подтвержденного email
            if(rand(1,100)<=20){
                $emailVerified = false;
            }
            try {
                DB::table('emails')->insert([
                    'source' => $email,
                    'can_send_email' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                DB::table('users')->insert([
                    'username' => $name,
                    'email' => $email,
                    'email_verified' => $emailVerified,
                    'subscription_is_valid_until' => $subscriptionIsValidUntil,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } catch (Exception $e) {
                echo $e->getMessage()."\r\n";
                continue;
            }
        }
    }
}
