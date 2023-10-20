<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ActionResource;
use App\Http\Resources\AlaaJsonResource;
use App\Models\User;
use Illuminate\Http\Request;


/**
 * Class UserLightResource
 *
 * @mixin User
 */
class UserLightResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof User)) {
            return [];
        }

        $action[ActionResource::EDIT] = action('Web\UserController@edit', ['user' => $this->id]);
        return [
            'first_name' => $this->when(isset($this->firstName), $this->firstName),
            'last_name' => $this->when(isset($this->lastName), $this->lastName),
            'name_slug' => $this->when(isset($this->nameSlug), $this->nameSlug),
            'action' => new ActionResource($action),
            'mobile' => $this->when(isset($this->mobile), $this->mobile),
            'postalCode' => $this->postalCode,
            'shahr_id' => $this->shahr_id,
        ];
    }
}
