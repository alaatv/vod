<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;

class Contenttype extends BaseModel
{
    protected $fillable = [
        'name',
        'displayName',
        'description',
        'order',
        'enable',
    ];

    public static function List(): array
    {
        return [
            'video',
            'pamphlet',
            'article',
        ];
    }

    public static function video(): array
    {
        return [
            'video',
        ];
    }

    public static function getRootContentType()
    {
        return Cache::tags('contentType')
            ->remember('ContentType:getRootContentType', config('constants.CACHE_600'), function () {
                return Contenttype::whereDoesntHave('parents')
                    ->get();
            });
    }

    public function contents()
    {
        return $this->belongsToMany(Content::class, 'educationalcontent_contenttype', 'contenttype_id', 'content_id');
    }

    public function parents()
    {
        return $this->belongsToMany(Contenttype::class, 'contenttype_contenttype', 't2_id',
            't1_id')
            ->withPivot('relationtype_id')
            ->join('contenttypeinterraltions', 'relationtype_id',
                'contenttypeinterraltions.id')//            ->select('major1_id AS id', 'majorinterrelationtypes.name AS pivot_relationName' , 'majorinterrelationtypes.displayName AS pivot_relationDisplayName')
            ->where('relationtype_id', 1);
    }

    public function children()
    {
        return $this->belongsToMany(Contenttype::class, 'contenttype_contenttype', 't1_id',
            't2_id')
            ->withPivot('relationtype_id')
            ->join('contenttypeinterraltions', 'relationtype_id', 'contenttypeinterraltions.id')
            ->where('relationtype_id',
                1);
    }
}
