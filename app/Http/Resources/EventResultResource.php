<?php

namespace App\Http\Resources;

use App\Http\Resources\Admin\UserLightResource;
use App\Models\Eventresult;


class EventResultResource extends AlaaJsonResource
{

    public function toArray($request)
    {

        if (!($this->resource instanceof Eventresult)) {
            return [];
        }

        $user = $this->user;
        return [
            'id' => $this->id,
            'event' => new EventResource($this->event),
            'rank' => $this->rank,
            'enable_report_publish' => $this->enableReportPublish,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'report_file_link' => $this->ReportFileLink,
            'user' => new UserLightResource($this->user),
            'major' => new Major($this->major),
            'region' => new RegionResource($this->region),
            'event_result_status' => new EventResultStatusResource($this->eventresultstatus),
            'participant_group_id' => $this->participant_group_id,
            'nomre_taraz_dey' => $this->nomre_taraz_dey,
            'nomre_taraz_tir' => $this->nomre_taraz_tir,
            'nomre_taraz_moadel' => $this->nomre_taraz_moadel,
            'nomre_taraz_kol' => $this->nomre_taraz_kol,
            'rank_in_region' => $this->rank_in_region,
            'rank_in_district' => $this->rank_in_district,
            //Because of preventing an update in front end , I am forced to put postalCode and shahr_id here
            'postalCode' => $user?->postalCode,
            'shahr_id' => $user?->shahr_id,
            'participationCode' => $this->participationCode,
        ];
    }
}
