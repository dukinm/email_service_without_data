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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username',200)->comment('Имя клиента');
            $table->char('email',100)->unique('email')->comment('Email');
            $table->boolean('email_verified')->comment('Email был подтвержден');
            $table->timestamp('subscription_is_valid_until')->nullable();
            $table->timestamp('last_notification_of_the_expiring_subscription_was_sent_at')->nullable();
            $table->timestamps();
            $table->index(['email_verified', 'subscription_is_valid_until'],'email_verified_and_subscription_end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
