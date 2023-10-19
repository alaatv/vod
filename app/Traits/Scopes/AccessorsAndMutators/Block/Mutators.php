<?php

namespace App\Traits\Scopes\AccessorsAndMutators\Block;

trait Mutators
{
    public function setOfferAttribute($value)
    {
        return $this->isOfferBlock = (boolean) $value;
    }
}
