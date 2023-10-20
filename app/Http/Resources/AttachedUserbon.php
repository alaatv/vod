<?php

namespace App\Http\Resources;

use App\Models\Userbon;
use Illuminate\Http\Request;


/**
 * Class Userbon
 *
 * @mixin Userbon
 * */
class AttachedUserbon extends AlaaJsonResource
{
    public function __construct(Userbon $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Userbon)) {
            return [];
        }

        return [
            'bon_id' => $this->bon_id,
            'user_id' => $this->user_id,
            'total_number' => $this->totalNumber,
            'used_number' => $this->usedNumber,
        ];
    }
}
