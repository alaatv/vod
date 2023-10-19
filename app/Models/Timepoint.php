<?php

namespace App\Models;

use App\Classes\FavorableInterface;
use App\Traits\favorableTraits;

class Timepoint extends BaseModel implements FavorableInterface
{
    use favorableTraits;

    protected $fillable = [
        'insertor_id',
        'content_id',
        'title',
        'time',
        'photo',
    ];

    public function content()
    {
        return $this->belongsTo(Content::Class, 'content_id', 'id');
    }
}
