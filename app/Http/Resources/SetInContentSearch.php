<?php

namespace App\Http\Resources;

use App\Models\Contentset;
use Illuminate\Http\Request;


/**
 * Class Set
 *
 * @mixin Contentset
 * */
class SetInContentSearch extends AlaaJsonResource
{
    public function __construct(Contentset $model)
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
        if (!($this->resource instanceof Contentset)) {
            return [];
        }

        return [
            'id' => $this->id,
            'title' => $this->when(isset($this->name), $this->name),
            'short_title' => $this->when(isset($this->shortName), $this->shortName),
        ];
    }
}
