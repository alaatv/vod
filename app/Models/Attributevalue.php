<?php

namespace App\Models;

use App\Collection\AttributevalueCollection;
use Illuminate\Database\Eloquent\Collection;


class Attributevalue extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'attribute_id',
        'name',
        'values',
        'description',
        'isDefault',
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     *
     * @return Collection
     */
    public function newCollection(array $models = [])
    {
        return new AttributevalueCollection($models);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function orderproducts()
    {
        return $this->belongsToMany(Orderproduct::class, 'attributevalue_orderproduct', 'value_id', 'orderproduct_id');
    }

    public function getValuesAttribute($value)
    {
        if (!isset($value)) {
            return $value;
        }

        return json_decode($value);
    }

    public function getInInfoAttribute()
    {
        return "{$this->id} - {$this->name}";
    }
}
