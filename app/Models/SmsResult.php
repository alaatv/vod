<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class SmsResult extends Model
{
    public const DONE_ID = 1;
    public const SEND_FAIL_ID = 2;
    public const RESPONSE_ERROR_ID = 3;
}
