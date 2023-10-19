<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhoneNumber extends BaseModel
{
    use DateTrait;
    use HasFactory;

    protected  $fillable = [
        'provider_id',
        'number',
    ];

    public function phoneNumberProvider()
    {
        return $this->belongsTo(PhoneNumberProvider::class, 'provider_id');
    }

    public function phoneBooks()
    {
        return $this->belongsToMany(PhoneBook::class, 'phone_book_number');
    }

    public function scopeFilter($query, $filters)
    {
        if( isset($filters['number']) ){
            $query->where('number', '=', $filters['number']);
        }

        if( isset($filters['phoneBookId']) ){
            $query->whereHas('phoneBooks',  function (Builder $query) use ($filters){
                $query->where('id',  $filters['phoneBookId']);
            });
        }

        if( isset($filters['phoneNumberProvider']) ){
            $query->whereHas('phoneNumberProvider',  function (Builder $query) use ($filters){
                $query->where('id',  $filters['phoneNumberProvider']);
            });
        }
    }
}
