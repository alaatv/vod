<?php

namespace App\Models;

use App\Repositories\SmsBlackListRepository;
use Illuminate\Database\Eloquent\Builder;

class Phone extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'phoneNumber',
        'priority',
        'contact_id',
        'phonetype_id',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function phonetype()
    {
        return $this->belongsTo(Phonetype::class);
    }

    public function scopeWhereNotInBlackList(Builder $builder)
    {
        return $builder->whereNotIn('phoneNumber',
            SmsBlackListRepository::getBlockedList()?->get()?->pluck('mobile')?->toArray());
    }
}
