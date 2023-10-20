<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TagGroup extends Model
{
    public const EDUCATIONAL_SYSTEM_ID = 1;
    public const GRADE_ID = 2;
    public const MAJOR_ID = 3;
    public const LESSON_ID = 4;
    public const TEACHER_ID = 5;
    public const TREE_ID = 6;

    protected $appends = [
        'dns_info',             // dns = Combine display_name, status
    ];

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function scopeEnable($query)
    {
        $query->where('enable', 1);
    }

    /**
     * dns = Combine display_name, status
     *
     * @return string
     */
    public function getDnsInfoAttribute()
    {
        return $this->display_name.($this->enable ? '' : ' (غیرفعال)');
    }
}
