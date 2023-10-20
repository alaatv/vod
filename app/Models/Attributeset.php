<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class Attributeset extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'order',
    ];

    protected $touches = [
        'attributegroups',
    ];

    public function attributes()
    {
        $key = 'Attributeset:'.$this->cacheKey();

        return Cache::remember($key, config('constants.CACHE_60'), function () {
            $result = DB::table('attributesets')
                ->join('attributegroups', function ($join) {
                    $join->on('attributesets.id', '=', 'attributegroups.attributeset_id')
                        ->whereNull('attributegroups.deleted_at');
                })
                ->join('attribute_attributegroup', function ($join) {
                    $join->on('attribute_attributegroup.attributegroup_id', '=', 'attributegroups.id');
                })
                ->join('attributes', function ($join) {
                    $join->on('attributes.id', '=', 'attribute_attributegroup.attribute_id')
                        ->whereNull('attributes.deleted_at');
                })
                ->select([
                    'attributes.*',
                    'attribute_attributegroup.attributegroup_id as pivot_attributegroup_id',
                    'attribute_attributegroup.order as pivot_order',
                    'attribute_attributegroup.description as pivot_description',
                ])
                ->where('attributesets.id', '=', $this->id)
                ->whereNull('attributesets.deleted_at')
                ->orderBy('pivot_order')
                ->get();

            $result = Attribute::hydrate($result->toArray());

            $result->transform(function ($item, $key) {

                $p = [
                    'attributegroup_id' => $item->pivot_attributegroup_id,
                    'order' => $item->pivot_order,
                    'description' => $item->pivot_description,
                ];
                $p = $item->newPivot($item, $p, 'attribute_attributegroup', true);

                $item->relations = [

                    'pivot' => $p,
                ];
                unset($item->pivot_attributegroup_id, $item->pivot_order, $item->pivot_description);

                return $item;
            });

            return $result;
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
        return $this->hasMany(Product::class);
    }

    public function attributegroups()
    {
        return $this->hasMany(Attributegroup::class)
            ->orderBy('order');
    }
}
