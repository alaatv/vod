<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Bon extends BaseModel
{
    public const ALAA_BON = 1;
    public const ALAA_POINT_BON = 2;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
        'order',
        'isEnable',
    ];

    protected $hidden = [
        'pivot',
        'deleted_at',
        'isEnable',
        'bontype_id',
        'order',
        'created_at',
        'updated_at',
    ];

    public static function getAlaaBonDisplayName()
    {
        return Cache::tags('bon')
            ->remember('getAlaaBon', config('constants.CACHE_600'), function () {
                $myBone = Bon::where('name', config('constants.BON1'))
                    ->get();
                $bonName = null;
                if ($myBone->isNotEmpty()) {
                    $bonName = $myBone->first()->displayName;
                }

                return $bonName;
            });
    }

    public function cacheKey()
    {
        $key = $this->getKey();
        $time = isset($this->updated_at) ? $this->updated_at->timestamp : $this->created_at->timestamp;

        return sprintf('%s-%s', //$this->getTable(),
            $key, $time);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('discount', 'bonPlus');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('number');
    }

    public function userbons()
    {
        return $this->hasMany(Userbon::class);
    }

    public function bontype()
    {
        return $this->belongsTo(Bontype::class);
    }

    public function scopeEnable($query)
    {
        return $query->where('isEnable', '=', 1);
    }

    /**
     * @param  Builder  $query
     * @param  mixed  $name
     *
     * @return Builder
     */
    public function scopeOfName($query, $name)
    {
        return $query->where('name', $name);
    }
}
