<?php

namespace App\Models;


class SMS extends BaseModel
{
    public const INDEX_PAGE_NAME = 'smsPage';
    protected $table = 'sms';
    protected $fillable = [
        'from_user_id',
        'from',
        'message',
        'sha1',
        'provider_id',
        'sent',
        'foreign_id',
        'foreign_type'
    ];
    protected $appends = [
        'recheck_sms_status_link',
        'sms_recipients_link',
        'resend_unsuccessful_bulk_sms_link',
        'detail',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::Class, 'from_user_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(SmsUser::Class, 'sms_id');
    }

    public function provider()
    {
        return $this->belongsTo(SmsProvider::class, 'provider_id');
    }

    public function getDetailAttribute()
    {
        return $this->details()->first();
    }

    public function details()
    {
        return $this->hasMany(SmsDetail::class, 'sms_id', 'id');
    }

    public function scopeSent($query)
    {
        return $query->where('sent', 1);
    }

    public function scopeReceived($query)
    {
        return $query->where('sent', 0);
    }

    public function getRecheckSmsStatusLinkAttribute()
    {
        return route('sms.update.status', $this->id);
    }

    public function getSmsRecipientsLinkAttribute()
    {
        return route('web.admin.sms.user', ['sms_id' => $this->id]);
    }

    public function getResendUnsuccessfulBulkSmsLinkAttribute()
    {
        return route('resend.unsuccessful.bulk.sms', $this->id);
    }

    public function getEditUserLinkAttribute()
    {
        if (!isset($this->from_user_id)) {
            return null;
        }
        return route('user.edit', $this->from_user_id);
    }
}
