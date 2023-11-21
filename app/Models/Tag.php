<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public const INDEX_PAGE_NAME = 'tagPage';

    protected $fillable = [
        'name',
        'value',
        'tag_group_id',
        'enable',
        'description',
    ];
    protected $with = [
        'group'
    ];

    public function group()
    {
        return $this->belongsTo(TagGroup::class, 'tag_group_id');
    }

    public function scopeEnable($query)
    {
        $query->where('enable', 1);
    }
}
