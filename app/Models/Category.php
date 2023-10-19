<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\Collection;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;

class Category extends Model
{
    use NodeTrait;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $casts = [
        'created_at'=> 'datetime',
        'updated_at'=> 'datetime',
    ];

    protected $fillable = [
        'name',
        'tags',
        'enable',
        'description',
    ];

    public function scopeActive($query)
    {
        return $query->where('enable', 1);
    }

    public function getWithDepth()
    {
        return Cache::tags('tree')
            ->remember('tree', config('constants.CACHE_600'), function () {
                return Category::withDepth()
                    ->active()
                    ->get();
            });
    }

    /**
     * Set the content's tag.
     *
     * @param  array  $value
     *
     * @return void
     */
    public function setTagsAttribute(array $value = null)
    {
        $tags = null;
        if (!empty($value)) {
            $tags = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        $this->attributes['tags'] = $tags;
    }

    public function referralCodeCommission()
    {
        return $this->morphMany(ReferralCodeCommission::class, 'entity');
    }
}
