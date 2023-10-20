<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class   SmsDetail extends Model
{
    protected $fillable = [
        'sms_id',
        'bulk_id',
        'pattern_data',
        'pattern_code',
        'sms_result_id',
        'admin_user_id',
        'resent_sms_id',
        'provider_number',
        'provider_message',
        'provider_sms_type',
        'provider_confirm_state',
        'provider_created_at',
        'provider_sent_at',
        'provider_recipients_count',
        'provider_valid_recipients_count',
        'provider_page',
        'provider_payback_cost',
        'provider_description',
        'provider_status',
        'provider_cost',
    ];

    public function result()
    {
        return $this->belongsTo(SmsResult::class, 'sms_result_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function sms()
    {
        return $this->belongsTo(SMS::Class);
    }
}
