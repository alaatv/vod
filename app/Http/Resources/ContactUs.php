<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


class ContactUs extends AlaaJsonResource
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
            'branches' => $this->when(isset($this['branches']), function () {
                $mainBranch = $this['branches']->main;
                return isset($this['branches']) ? ContactCompanyBranchInfo::collection([(array) $mainBranch]) : null;
            }),
            'social_networks' => $this->when(isset($this['socialNetwork']), function () {
                $socialNetworks = $this['socialNetwork'];
                $socialNetworksArray = [];
                foreach ($socialNetworks as $key => $socialNetwork) {
                    $social = [];
                    if ($key == 'telegram') {
                        $social = array_merge($social, (array) $socialNetwork->channel);
                    } elseif ($key == 'instagram') {
                        $social = array_merge($social, (array) $socialNetwork->main);
                    }
                    $social['name'] = $key;
                    $socialNetworksArray[] = $social;
                }
                return isset($this['socialNetwork']) ? ContactCompanySocialNetworkInfo::collection($socialNetworksArray) : null;
            }),
        ];
    }
}
