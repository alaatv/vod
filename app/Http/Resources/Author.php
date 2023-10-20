<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class User
 *
 * @mixin \App\User
 * */
class Author extends AlaaJsonResource
{
    public function __construct(\App\User $model)
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
        if (!($this->resource instanceof \App\User)) {
            return [];
        }

        return [
            'id' => $this->id,
            'first_name' => $this->when(isset($this->firstName), $this->firstName),
            'last_name' => $this->when(isset($this->lastName), $this->lastName),
            'photo' => $this->when(isset($this->photo), $this->photo),
        ];
    }
}
