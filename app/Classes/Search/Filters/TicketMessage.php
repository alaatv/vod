<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 10/25/2018
 * Time: 5:25 PM
 */

namespace App\Classes\Search\Filters;

use Illuminate\Database\Eloquent\Builder;
use function GuzzleHttp\json_decode;

class TicketMessage extends FilterAbstract
{
    protected $attribute = [
        'hasMessageFromDate' => 'created_at',
        'hasMessageToDate' => 'created_at',
        'users' => 'user_id',
    ];

    protected $relation = 'messages';

    public function apply(Builder $builder, $value, FilterCallback $callback): Builder
    {
        $filterData = json_decode($value);

        return $builder->whereHas($this->relation, function ($q) use ($filterData) {
            if (isset($filterData->hasMessageFromDate)) {
                $q->where($this->attribute['hasMessageFromDate'], '>=', $filterData->hasMessageFromDate);
            }

            if (isset($filterData->hasMessageToDate)) {
                $q->where($this->attribute['hasMessageToDate'], '<=', $filterData->hasMessageToDate);
            }

            if (isset($filterData->users) && is_array($filterData->users)) {
                $q->whereIn($this->attribute['users'], $filterData->users);
            }
        });
    }
}
