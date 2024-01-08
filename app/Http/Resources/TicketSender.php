<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class TicketSender extends AlaaJsonResource
{
    public function __construct(\App\Models\User $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'mobile' => $this->mobile,
            'national_code' => $this->nationalCode,
            'photo' => $this->photo,
            'role' => $this->getTicketRoleTitle(),
            'major' => $this->when(isset($this->major), function () {
                if (is_null($this->major_id)) {
                    return null;
                }

                return new Major($this->major);
            }),
        ];
    }
}
