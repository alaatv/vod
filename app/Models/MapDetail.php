<?php

namespace App\Models;

use App\Classes\Taggable;
use App\Classes\Uploader\Uploader;
use App\Traits\Map\TaggableMapTrait;

class MapDetail extends BaseModel implements Taggable
{
    use TaggableMapTrait;

    protected $table = 'mapDetails';

    protected $fillable = [
        'map_id',
        'type_id',
        'min_zoom',
        'max_zoom',
        'action',
        'data',
        'link',
        'entity_id',
        'entity_type',
        'tags',
        'enable'
    ];

    protected $with = [
        'latlngs',
    ];
    protected $casts = [
        'data' => 'array',
        'action' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        self::deleting(function (MapDetail $mapDetail) {
            $mapDetail->latlngs()->delete();
        });
    }

    public function latlngs()
    {
        return $this->hasMany(LatLng::class, 'map_detail_id');
    }

    public function Map()
    {
        return $this->belongsTo(Map::class);
    }

    public function mapDetail()
    {
        return $this->belongsTo(MapDetail::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }

    /**
     * Set the content's tag.
     *
     * @param  array|null  $value
     *
     * @return void
     */
    public function setTagsAttribute(array $value = null)
    {
        $tags = null;
        if (!empty($value)) {
            $tags = json_encode([
                'bucket' => 'mapDetail',
                'tags' => $value,
            ], JSON_UNESCAPED_UNICODE);
        }

        $this->attributes['tags'] = $tags;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getTagsAttribute($value)
    {
        $value = json_decode($value);
        return optional($value)->tags;
    }

    public function getDataAttribute($value)
    {
        if (is_null($value)) {
            return $value;
        }

        $value = json_decode($value);

        if (isset($value->icon)) {
            $value->icon->options->iconUrl = $this->icon;
        }
        unset($value->latlng);
        unset($value->latlngs);
        return $value;
    }

    public function getIconAttribute()
    {
        $data = json_decode($this->getRawOriginal('data'));

        if (empty($data)) {
            return null;
        }

        $icon = optional(optional(optional(optional($data)->icon))->options)->iconUrl;

        if (empty($icon)) {
            return null;
        }

        return Uploader::url(config('disks.MAP_DETAIL_ICON_MINIO'), '/'.$this->map_id.'/'.$icon, false);
    }

    public function isActive(): bool
    {
        return $this->enable ? true : false;
    }

    public function scopeActive($query)
    {
        return $query->where('enable', 1);
    }
}
