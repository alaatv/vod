<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;

class SmsUser extends Model
{
    use DateTrait;

    public const INDEX_PAGE_NAME = 'smsUserPage';
    protected $table = 'sms_user';
    protected $fillable = [
        'sms_id',
        'user_id',
        'mobile',
        'status',
    ];

    public function sms()
    {
        return $this->belongsTo(SMS::class, 'sms_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
