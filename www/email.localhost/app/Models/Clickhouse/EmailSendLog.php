<?php

namespace App\Models\Clickhouse;

use PhpClickHouseLaravel\BaseModel;

class EmailSendLog extends BaseModel
{
    // Not necessary. Can be obtained from class name MyTable => my_table
    protected $table = 'email_send_log';

}
