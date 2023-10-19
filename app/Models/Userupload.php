<?php

namespace App\Models;


class Userupload extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'file',
        'title',
        'comment',
        'staffComment',
        'isEnable',
        'useruploadstatus_id',
    ];

    public function useruploadstatus()
    {
        return $this->belongsTo(Useruploadstatus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
