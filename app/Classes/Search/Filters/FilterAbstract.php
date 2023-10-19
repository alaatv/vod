<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-04
 * Time: 15:35
 */

namespace App\Classes\Search\Filters;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use LogicException;

class FilterAbstract implements Filter
{
    protected $attribute;

    protected array $attributes;
    protected $relation;

    public function __construct()
    {
        if (!isset($this->attribute) && !isset($this->attributes)) {
            throw new LogicException(get_class($this).' must have $attribute or $attributes');
        }
    }

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        if (!isset($value)) {
            return $builder;
        }

        $value = $this->getSearchValue($value);

        return $builder->where($this->attribute, 'LIKE', '%'.$value.'%');
    }

    // TODO: I think the following method isn't needed, because only one method is used in it. Please check.
    protected function getSearchValue($value)
    {
        return is_string($value) ? trim($value) : $value;
    }

    /**
     * @return array|Translator|null|string
     */
    protected function getValueShouldBeSetMessage()
    {
        return trans('filter.value should be set', ['filter' => get_class($this)]);
    }

    /**
     * @return array|Translator|null|string
     */
    protected function getValueShouldBeArrayMessage()
    {
        return trans('filter.value should be array', ['filter' => get_class($this)]);
    }
}
