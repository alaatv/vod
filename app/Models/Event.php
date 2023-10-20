<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;

class Event extends BaseModel
{
    public const ARASH_PUBLISH_EVENT_ID = 5;

    public const KONKUR_96 = 1;
    public const SABTENAME_SHARIF_97 = 2;
    public const KONKUR_97 = 3;
    public const KONKUR_98 = 4;
    public const ARAS_HPUBLISH = 5;
    public const SABTENAME_SHARIF_99 = 6;
    public const KONKUR_99 = 7;
    public const MOSHAVERE_WINTER_1400 = 10;
    public const EMTEHAN_NAHAYI_1401 = 11;
    public const EMTEHAN_NAHAYI_1402 = 15;

    public const ABRISHAM_2 = 20;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
        'startTime',
        'endTime',
        'enable',
        'duplicatable',
    ];

    public function studyEvent()
    {
        return $this->hasMany(Studyevent::class);
    }

    public function surveys()
    {
        return $this->belongsToMany(Survey::class)
            ->withPivot('order', 'enable', 'description');
    }

    public function usersurveyanswers()
    {
        return $this->hasMany(Usersurveyanswer::class);
    }

    public function eventresults()
    {
        return $this->hasMany(Eventresult::class);
    }

    public function newsLetters()
    {
        return $this->hasMany(Newsletter::class);
    }

    public function scopeName($query, $value)
    {
        return $query->where('name', $value);
    }

    public function scopeEnable($query)
    {
        return $query->where('enable', 1);
    }

    /**
     * Get the referral codes for the event.
     */
    public function referralCodes()
    {
        return $this->hasMany(ReferralCode::class);
    }

    public function isDuplicatabale()
    {
        $this->duplicatable;
        return $this->duplicatable;
    }

    public function getImageAttribute($value)
    {
        return empty($value) ? null : Uploader::url(config('disks.EVENT_IMAGE'), $value);
    }
}
