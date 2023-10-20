<?php

namespace App\Models;

use App\Classes\Uploader\Uploader;
use Illuminate\Support\Arr;
use stdClass;

class Websitesetting extends BaseModel
{
    private const DISK = 'minio_upload';
    protected $fillable = [
        'setting',
        'version',
        'faq',
        'user_id',
    ];

//    protected $casts = [
//        'setting' => 'array',
//    ];

    public static function getFaqPhoto($faq)
    {
        if (!isset($faq->photo) || empty($faq->photo)) {
            return null;
        }

        return Uploader::url(config('disks.FAQ_PHOTO_MINIO'), $faq->photo);
    }

    public static function createFAQ(array $input): stdClass
    {
        $faq = new stdClass();
        return self::fillFAQ($faq, $input);
    }

    public static function fillFAQ(stdClass $faq, array $input): stdClass
    {
        $faq->id = Arr::get($input, 'id', (isset($faq->id)) ? $faq->id : null);
        $faq->title = Arr::get($input, 'title');
        $faq->body = Arr::get($input, 'body');
        $faq->photo = Arr::get($input, 'photo');
        $faq->video = Arr::get($input, 'video');
        $faq->order = Arr::get($input, 'order', 0);

        return $faq;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param                $faqId
     *
     * @return array
     */
    public function findFAQ($faqId): array
    {
        $faqs = $this->faq;
        $faqKey = array_search($faqId, array_column($faqs, 'id'));
        return [$faqKey, $faqs[$faqKey]];
    }

    public function getLastFaqId(): int
    {
        $faqs = $this->faq;
        if (empty($faqs)) {
            return 0;
        }

        usort($faqs, function ($one, $two) {
            return ($two->id <=> $one->id);
        });

        return $faqs[0]->id;

    }

    public function getSettingAttribute($value)
    {
        return json_decode($value);
    }

    public function getSiteLogoUrlAttribute()
    {
//        $setting = json_decode(json_encode($this->setting));
//        $siteLogo =  $setting->site->siteLogo;

        $siteLogo = $this->setting->site->siteLogo;

        return Uploader::url(self::DISK, $siteLogo);
    }

    public function getFaqAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }

        $faqs = json_decode($value);
        usort($faqs, function ($one, $two) {
            return ($two->order <=> $one->order);
        });

        return $faqs;
    }

    public function setFaqAttribute($input)
    {
        if (is_null($input)) {
            $this->attributes['faq'] = null;
        } else {
            $this->attributes['faq'] = json_encode($input, JSON_UNESCAPED_UNICODE);
        }
    }
}
