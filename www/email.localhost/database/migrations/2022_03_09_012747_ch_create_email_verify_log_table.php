<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \PhpClickHouseLaravel\Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        static::write('
            CREATE TABLE email_verify_log (
                created_at DateTime,
                source String,
                email String,
                local String,
                domain String,
                type String,
                qc UInt8,
                domain_exists UInt8,
                mx_domain_exists UInt8,
                user_in_spam_list UInt8,
                can_send_email UInt8,
                time_to_send_in_s Int32
            )
            ENGINE = MergeTree()
            ORDER BY (created_at)
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        static::write('DROP TABLE email_verify_log');
    }
};
