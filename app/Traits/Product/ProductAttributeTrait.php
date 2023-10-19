<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-03-08
 * Time: 19:53
 */

namespace App\Traits\Product;




use App\Classes\Uploader\Uploader;
use App\Models\Attribute;
use App\Models\Attributetype;
use App\Models\Attributevalue;
use App\Repositories\AttributeRepo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait ProductAttributeTrait
{
    /**
     * @param  array  $attributesId
     *
     * @return mixed
     */
    public function getAttributesValueByIds(array $attributesId)
    {
        return $this->attributevalues->whereIn('id', $attributesId);
    }

    public function attributevalues($attributeType = null)
    {
        if (!isset($attributeType)) {
            return $this->belongsToMany(Attributevalue::class)->withPivot('extraCost', 'description');
        }

        $attributeType = Attributetype::where('name', $attributeType)->first();
        $attributesArray = [];
        foreach ($this->attributeset->attributes()->where('attributetype_id', $attributeType->id) as $attribute) {
            array_push($attributesArray, $attribute->id);
        }

        return $this->belongsToMany(Attributevalue::class)
            ->whereIn('attribute_id', $attributesArray)
            ->withPivot('extraCost', 'description');
    }

    public function attributevaluesByType(int $attributeType)
    {
        return $this->attributevalues->whereIn('attribute_id',
            AttributeRepo::getAttributesByType($attributeType)->pluck('id')->toArray());
    }

    public function attributevalueTree($attributeType = null)
    {
        $key = 'product:attributevalueTree:'.$attributeType.$this->cacheKey();

        return Cache::tags(['product'])
            ->remember($key, config('constants.CACHE_60'), function () use ($attributeType) {
                if ($attributeType) {
                    $attributeType = Attributetype::all()
                        ->where('name', $attributeType)
                        ->first();
                    $attributesArray = [];
                    foreach ($this->attributeset->attributes()
                                 ->where('attributetype_id', $attributeType->id) as $attribute) {
                        array_push($attributesArray, $attribute->id);
                    }
                }
                $parentsCollection = $this->getAllParents();
                $parentsCollection->push($this);
                $attributes = collect();
                foreach ($parentsCollection as $parent) {
                    if (isset($attributesArray)) {
                        $attributevalues = $parent->attributevalues->whereIn('attribute_id', $attributesArray);
                    } else {
                        $attributevalues = $parent->attributevalues;
                    }
                    foreach ($attributevalues as $attributevalue) {
                        if ($attributes->has($attributevalue->id)) {
                            continue;
                        }
                        $attributes->put($attributevalue->id, [
                            'attributevalue' => $attributevalue,
                            'attribute' => $attributevalue->attribute,
                        ]);
                    }
                }

                return $attributes;
            });
    }

    /**
     * @return Collection|null
     */
    public function getAttributesAttribute(): ?Collection
    {
        return $this->getAllAttributes();
    }

    /**
     * Gets product's all attributes
     *
     * @return Collection|null
     */
    protected function getAllAttributes(): ?Collection
    {
        $product = $this;
        $key = 'product:getAllAttributes:'.$product->id;

        return Cache::tags(['product', 'attribute', 'product_'.$this->id, 'product_'.$this->id.'_attributes'])
            ->remember($key, config('constants.CACHE_600'), function () use ($product) {

                $selectCollection = collect();
                $groupedCheckboxCollection = collect();
                $extraSelectCollection = collect();
                $extraCheckboxCollection = collect();
                $simpleInfoAttributes = collect();
                $checkboxInfoAttributes = collect();
                $attributeset = $product->attributeset;
                $attributes = optional($attributeset)->attributes();
                $productType = $product->producttype_id;
                if (!$product->relationLoaded('attributevalues')) {
                    $product->load('attributevalues');
                }

                if (!isset($attributes)) {
                    return null;
                }

                $attributes->load('attributetype', 'attributecontrol');

                foreach ($attributes as $attribute) {
                    $attributeType = $attribute->attributetype;
                    $controlName = $attribute->attributecontrol->name;
                    $attributevalues = $product->attributevalues->where('attribute_id', $attribute->id)
                        ->sortBy('pivot.order');

                    if ($attributevalues->isEmpty()) {
                        continue;
                    }
                    switch ($controlName) {
                        case 'select':
                            if ($attributeType->name == 'extra') {
                                $select = [];
                                $this->makeSelectAttributes($attributevalues, $select, 'extra');
                                if (!empty($select)) {
                                    $at = [];
                                    foreach ($select as $s) {
                                        $at[] = array_merge($s, [
                                            'displayName' => $attribute->displayName,
                                            'attributeDescription' => $attribute->displayName,
                                        ]);
                                    }
                                    $extraSelectCollection->put($attribute->id, $at);
                                }
                            } else {
                                if ($attributeType->name == 'main' && $productType == config('constants.PRODUCT_TYPE_CONFIGURABLE')) {
                                    if ($attributevalues->count() == 1) {
                                        $this->makeSimpleInfoAttributes($attributevalues, $attribute,
                                            $attributeType, $simpleInfoAttributes);
                                    } else {
                                        $select = [];
                                        $this->makeSelectAttributes($attributevalues, $select);
                                        if (!empty($select)) {
                                            $selectCollection->put($attribute->pivot->description, $select);
                                        }
                                    }
                                } else { // 1
                                    $this->makeSimpleInfoAttributes($attributevalues, $attribute, $attributeType,
                                        $simpleInfoAttributes);
                                }
                            }
                            break;
                        case 'groupedCheckbox':
                            if ($attributeType->name == 'extra') {
                                $groupedCheckbox = collect();
                                foreach ($attributevalues as $attributevalue) {
                                    $attributevalueIndex = $attributevalue->name;
                                    if (isset($attributevalue->pivot->description) && strlen($attributevalue->pivot->description) > 0) {
                                        $attributevalueIndex .= '( '.$attributevalue->pivot->description.' )';
                                    }

                                    if (isset($attributevalue->pivot->extraCost)) {
                                        if ($attributevalue->pivot->extraCost > 0) {
                                            $attributevalueIndex .= '(+'.number_format($attributevalue->pivot->extraCost).' تومان)';
                                        }
                                        if ($attributevalue->pivot->extraCost < 0) {
                                            $attributevalueIndex .= '(-'.number_format($attributevalue->pivot->extraCost).' تومان)';
                                        }
                                    }

                                    $groupedCheckbox->put($attributevalue->id, [
                                        'index' => $attributevalueIndex,
                                        'displayName' => $attribute->displayName,
                                        'name' => $attributevalueIndex,
                                        'value' => $attributevalue->id,
                                        'type' => $attributeType->name,
                                        'productType' => $product->producttype->name,
                                    ]);
                                }
                                if (!empty($groupedCheckbox)) {
                                    $extraCheckboxCollection->put($attribute->displayName, $groupedCheckbox);
                                }
                            } else {
                                if ($product->producttype->id == config('constants.PRODUCT_TYPE_CONFIGURABLE')) {
                                    if ($attributeType->name == 'information') {
                                        $this->makeSimpleInfoAttributes($attributevalues, $attribute,
                                            $attributeType, $checkboxInfoAttributes);
                                    } else {
                                        $groupedCheckbox = [];
                                        foreach ($attributevalues as $attributevalue) {
                                            $attributevalueIndex = $attributevalue->name;
                                            if (isset($attributevalue->pivot->description) && strlen($attributevalue->pivot->description) > 0) {
                                                $attributevalueIndex .= '( '.$attributevalue->pivot->description.' )';
                                            }

                                            $attributevalueExtraCost = '';
                                            $attributevalueExtraCostWithDiscount = '';
                                            if (!isset($attributevalue->pivot->extraCost)) {
                                                $groupedCheckbox = Arr::add($groupedCheckbox, $attributevalue->id, [
                                                    'displayName' => null,
                                                    'index' => $attributevalueIndex,
                                                    'name' => $attributevalueIndex,
                                                    'extraCost' => $attributevalueExtraCost,
                                                    'extraCostWithDiscount' => $attributevalueExtraCostWithDiscount,
                                                    'value' => $attributevalue->id,
                                                    'type' => $attributeType->name,
                                                    'productType' => $product->producttype->name,
                                                ]);
                                                continue;
                                            }
                                            if ($attributevalue->pivot->extraCost > 0) {
                                                $attributevalueExtraCost =
                                                    '+'.number_format($attributevalue->pivot->extraCost).' تومان';
                                                if ($product->discount > 0) {
                                                    $attributevalueExtraCostWithDiscount =
                                                        number_format('+'.$attributevalue->pivot->extraCost * (1 - ($product->discount / 100))).' تومان';
                                                } else {
                                                    $attributevalueExtraCostWithDiscount = 0;
                                                }
                                            } else {
                                                if ($attributevalue->pivot->extraCost < 0) {
                                                    $attributevalueExtraCost =
                                                        '-'.number_format($attributevalue->pivot->extraCost).' تومان';
                                                    if ($product->discount > 0) {
                                                        $attributevalueExtraCostWithDiscount =
                                                            number_format('-'.$attributevalue->pivot->extraCost * (1 - ($product->discount / 100))).' تومان';
                                                    } else {
                                                        $attributevalueExtraCostWithDiscount = 0;
                                                    }
                                                }
                                            }

                                            $groupedCheckbox = Arr::add($groupedCheckbox, $attributevalue->id, [
                                                'displayName' => null,
                                                'index' => $attributevalueIndex,
                                                'name' => $attributevalueIndex,
                                                'extraCost' => $attributevalueExtraCost,
                                                'extraCostWithDiscount' => $attributevalueExtraCostWithDiscount,
                                                'value' => $attributevalue->id,
                                                'type' => $attributeType->name,
                                                'productType' => $product->producttype->name,
                                            ]);
                                        }
                                        if (!empty($groupedCheckbox)) {
                                            $groupedCheckboxCollection->put($attribute->pivot->description,
                                                $groupedCheckbox);
                                        }
                                    }
                                } else {
                                    $this->makeSimpleInfoAttributes($attributevalues, $attribute, $attributeType,
                                        $checkboxInfoAttributes);
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }

                $productAttributes = [
                    //main Attribute
                    'dropDown' => $selectCollection,
                    'checkBox' => $groupedCheckboxCollection,

                    //info Attribute
                    'simple' => $simpleInfoAttributes,
                    'check_box_' => $checkboxInfoAttributes,

                    //extra Attribute
                    'check_box' => $extraCheckboxCollection,
                    'drop_down' => $extraSelectCollection,

                ];

//            return $productAttributes;
                $attributesResult = collect();
                foreach ($productAttributes as $key => $values) {
//                dump([$key,$values]);
                    foreach ($values as $attributes) {
//                    dump($attributes);
                        $data = collect();
                        foreach ($attributes as $item) {
                            $type = Arr::get($item, 'type');
                            $title = Arr::get($item, 'displayName');
                            $data->push([
                                'name' => Arr::get($item, 'name'),
                                'id' => Arr::get($item, 'value'),
                            ]);
                        }
                        if (count($attributes) <= 0) {
                            continue;
                        }
                        $attributesResult->pushAt($type, json_decode(json_encode([
                            'type' => $type,
                            'title' => $title,
                            'control' => camel_case($key),
                            'data' => $data,
                        ])));

                    }
                }

                return $attributesResult->count() > 0 ? $attributesResult : null;
            });
    }

    private function makeSelectAttributes(&$attributevalues, &$result, $type = 'main')
    {
        foreach ($attributevalues as $attributevalue) {
            $attributevalueIndex = $attributevalue->name;
            if (isset($attributevalue->pivot->description) && strlen($attributevalue->pivot->description) > 0) {
                $attributevalueIndex .= '( '.$attributevalue->pivot->description.' )';
            }

            if (!isset($attributevalue->pivot->extraCost)) {

                $result = array_merge($result, [
                    $attributevalue->id => [
                        'value' => $attributevalue->id,
                        'name' => $attributevalueIndex,
                        'index' => $attributevalueIndex,
                        'type' => $type,
                    ],
                ]);
//            $result = Arr::add($result, $attributevalue->id, $attributevalueIndex);
                continue;
            }
            if ($attributevalue->pivot->extraCost > 0) {
                $attributevalueIndex .= '(+'.number_format($attributevalue->pivot->extraCost).' تومان)';
            } else {
                if ($attributevalue->pivot->extraCost < 0) {
                    $attributevalueIndex .= '(-'.number_format($attributevalue->pivot->extraCost).' تومان)';
                }
            }


            $result = array_merge($result, [
                $attributevalue->id => [
                    'value' => $attributevalue->id,
                    'name' => $attributevalueIndex,
                    'index' => $attributevalueIndex,
                    'type' => $type,
                ],
            ]);
//            $result = Arr::add($result, $attributevalue->id, $attributevalueIndex);
        }
    }

    private function makeSimpleInfoAttributes(
        &$attributevalues,
        &$attribute,
        &$attributeType,
        Collection &$simpleInfoAttributes
    ) {
        $infoAttributeArray = [];
        foreach ($attributevalues as $attributevalue) {

            array_push($infoAttributeArray, [
                'displayName' => $attribute->displayName,
                'name' => $attributevalue->name,
                'index' => $attributevalue->name,
                'value' => $attributevalue->id,
                'type' => $attributeType->name,
            ]);
        }
        if (!empty($infoAttributeArray)) {
            $simpleInfoAttributes->put($attribute->displayName, $infoAttributeArray);
        }
    }

    public function getInfoAttributesAttribute()
    {
        $product = $this;
        $key = 'product:getInfoAttributes:'.$product->id;

        return Cache::tags([
            'product', 'attribute', 'infoAttributes', 'product_'.$this->id, 'product_'.$this->id.'_attributes',
            'product_'.$this->id.'_infoAttributes'
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($product) {
                $attributes = [
                    'productionYear' => [],
                    'sal' => [],
                    'grade' => [],
                    'major' => [],
                    'educationalSystem' => [],
                    'shippingMethod' => [],
                    'downloadDate' => [],
                    'teacher' => [],
                    'services' => [],
                    'educationalYear' => [],
                    'author' => [],
                    'examType' => [],
                    'duration' => [],
                    'expirationDuration' => [],
                ];
                /** @var Attributevalue $attributevalue */
                foreach ($product->attributevalues as $attributevalue) {
                    /** @var Attribute $attribute */
                    $attribute = $attributevalue->attribute;
                    if ($attribute->attributetype_id == config('constants.ATTRIBUTE_TYPE_EXTRA')) {
                        continue;
                    }
                    $attributeName = $attribute->name;
                    if ($attributeName == 'educationalYear') {
                        $attributeName = 'productionYear';
                    } elseif ($attributeName == 'author') {
                        $attributeName = 'teacher';
                    } elseif ($attributeName == 'examType') {
                        $attributeName = 'shippingMethod';
                    }
                    $attributes[$attributeName][] = $attributevalue->name;

                }
                return $attributes;
            });
    }

    public function getDurationAttributeAttribute()
    {
        $product = $this;
        $key = 'product:durationAttribute:'.$product->id;

        return Cache::tags([
            'product', 'attribute', 'infoAttributes', 'product_'.$this->id, 'product_'.$this->id.'_attributes',
            'product_'.$this->id.'_infoAttributes'
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($product) {
                $attributes = [
                    'productionYear' => null,
                    'sal' => null,
                    'grade' => null,
                    'major' => null,
                    'educationalSystem' => null,
                    'shippingMethod' => null,
                    'teacher' => null,
                    'services' => null,
                    'duration' => null,
                    'downloadDate' => null,
                ];
                /** @var Attributevalue $attributevalue */
                $attributevalues = $product->attributevalues->filterDurationAttribute();
                foreach ($attributevalues as $attributevalue) {
                    $attributes['duration'][] = $attributevalue->name;
                }
                return $attributes;
            });
    }

    public function getExtraAttributesAttribute()
    {
        $product = $this;
        $key = 'product:getExtraAttributes:'.$product->id;

        return Cache::tags([
            'product', 'attribute', 'extraAttributes', 'product_'.$this->id, 'product_'.$this->id.'_attributes',
            'product_'.$this->id.'_extra!ttributes'
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($product) {
                return $this->filterAttribute($product, config('constants.ATTRIBUTE_TYPE_EXTRA'));
            });
    }

    public function filterAttribute(Product $product, $attributetype_id)
    {
        $attributes = [];
        /** @var Attributevalue $attributevalue */
        foreach ($product->attributevalues as $attributevalue) {
            /** @var Attribute $attribute */
            $attribute = $attributevalue->attribute;
            if ($attribute->attributetype_id == $attributetype_id) {
                $attributes[$attribute->name][] = $attributevalue->name;
            }
        }
        return $attributes;
    }

    public function getSubscriptionAttributesAttribute()
    {
        $product = $this;
        $key = 'product:getSubscriptionAttributes:'.$product->id;

        return Cache::tags([
            'product', 'attribute', 'subscriptionAttributes', 'product_'.$this->id, 'product_'.$this->id.'_attributes',
            'product_'.$this->id.'_subscription!ttributes'
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($product) {
                return $this->filterAttribute($product, config('constants.ATTRIBUTE_TYPE_SUBSCRIPTION'));
            });
    }

    public function getCatalogAttribute()
    {
        if (!isset($this->file) || empty($this->file)) {
            return null;
        }

        return Uploader::url(config('disks.PRODUCT_CATALOG_MINIO'), $this->file);
    }
}
