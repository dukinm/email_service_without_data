<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->char('source',100)->unique('source')->comment('Исходный email, который был введен клиентом');
            $table->char('email',100)->nullable()->comment('Email, который был сформирован сервисом стандартизации');
            $table->char('local',100)->nullable()->comment('Локальная часть адреса');
            $table->char('domain',100)->nullable()->comment('Домен');
            $table->enum('type',['PERSONAL','CORPORATE','ROLE','DISPOSABLE'])->nullable()->comment('Тип адреса: PERSONAL — личный (@mail.ru, @yandex.ru), CORPORATE — корпоративный (@myshop.ru), ROLE — «ролевой» (info@, support@), DISPOSABLE — одноразовый (@temp-mail.ru)');
            $table->char('qc',1)->nullable()->comment('Код проверки, нужно проверить вручную при 1, 4');
            $table->boolean('can_send_email')->index()->comment('Email можно отправлять');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emails');
    }
};
