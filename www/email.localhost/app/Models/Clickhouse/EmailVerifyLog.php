<?php

namespace App\Models\Clickhouse;

use PhpClickHouseLaravel\BaseModel;

class EmailVerifyLog extends BaseModel
{
    // Not necessary. Can be obtained from class name MyTable => my_table
    protected $table = 'email_verify_log';

}
