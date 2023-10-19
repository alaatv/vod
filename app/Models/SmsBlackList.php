<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsBlackList extends Model
{
    use HasFactory;

    public const DISABLE_SMS_WORDS = ['لغو 11', 'لغو', 'لغو11', 'لغو ۱۱', 'لغو۱۱'];
    public const DISABLE_SMS_CHARACTERS = ['11', '۱۱'];
    public $timestamps = false;
    protected $table = 'sms_blacklist';
    protected $keyType = 'string';
    protected $primaryKey = 'mobile';
    protected $fillable = [
        'mobile',
        'created_at',
    ];
}
