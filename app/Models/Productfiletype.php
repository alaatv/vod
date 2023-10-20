<?php

namespace App\Models;

use App\Traits\DateTrait;
use Illuminate\Support\Arr;

class Productfiletype extends BaseModel
{
    use DateTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'displayName',
    ];

    /**
     * @return array
     */
    public static function makeSelectArray(): array
    {
        $productFileTypes = Productfiletype::pluck('displayName', 'id')
            ->toArray();
        $productFileTypes = Arr::add($productFileTypes, 0, 'انتخاب کنید');
        $productFileTypes = Arr::sortRecursive($productFileTypes);

        return $productFileTypes;
    }

    public function productfiles()
    {
        return $this->hasMany(Productfile::class);
    }

}
