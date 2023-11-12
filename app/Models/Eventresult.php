<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use Illuminate\Support\Arr;

class Eventresult extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'eventresultstatus_id',
        'rank',
        'region_id',
        'major_id',
        'participationCode',
        'participationCodeHash',
        'enableReportPublish',
        'comment',
        'participant_group_id',
        'nomre_taraz_dey',
        'nomre_taraz_tir',
        'nomre_taraz_moadel',
        'nomre_taraz_kol',
        'rank_in_region',
        'rank_in_district',
        'reportFile',
    ];

    protected $appends = [
        'reportFileLink'
    ];

    public static function updateOrCreateEventResult($data)
    {
        $event = Event::findOrFail(Arr::get($data, 'event_id'));
        if ($event->isDuplicatabale()) {
            return self::create($data);
        }

        return self::updateOrCreate(
            [
                'user_id' => Arr::get($data, 'user_id'),
                'event_id' => Arr::get($data, 'event_id'),
            ],
            $data
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function eventresultstatus()
    {
        return $this->belongsTo(Eventresultstatus::Class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function getReportFileLinkAttribute()
    {
        if (!isset($this->reportFile)) {
            return null;
        }

        return Uploader::url(config('disks.EVENT_RESULT_MINIO_TEMP'), $this->reportFile);
//        return Uploader::privateUrl(config('disks.EVENT_RESULT_MINIO'), 600, $this, $this->reportFile);
//        return Uploader::privateUrl(config('disks.EVENT_RESULT_MINIO'), 600, $this, $this->reportFile);
    }
}