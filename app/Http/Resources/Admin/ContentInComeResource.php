<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\Author;
use App\Models\ContentIncome;
use Illuminate\Http\Request;


class ContentInComeResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'content_id' => $this->content_id,
            'gate_way' => $this->tmp_gateway,
            'cost' => $this->getCost(),
            'user' => $this->getUser(),
        ];
    }

    private function getCost()
    {
        $shareCostKey = ContentIncome::getAuthorizedShareCostIndex();

        return $this->$shareCostKey;
    }

    private function getUser()
    {
        $user = optional(optional($this->orderproduct)->order)->user;
        if (is_null($user)) {
            return null;
        }
        return new Author($user);
    }
}
