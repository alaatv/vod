<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/26/2018
 * Time: 5:12 PM
 */

namespace App\Collection;

use App\Models\Attributevalue;
use Illuminate\Database\Eloquent\Collection;

class AttributevalueCollection extends Collection
{
    public function filterDurationAttribute()
    {
        return $this->where('attribute.name', 'duration');
    }

    public function pullSubscriptionAttributevalue(int $attributeId): ?Attributevalue
    {
        $subscriptionDurationAttribute = $this->where('attribute_id', $attributeId)->first();

        if (!isset($subscriptionDurationAttribute)) {
            return null;
        }

        $key = $this->search(function ($i) use ($subscriptionDurationAttribute) {
            return $i->id === $subscriptionDurationAttribute->id;
        });

        $this->forget($key);

        return $subscriptionDurationAttribute;
    }
}
