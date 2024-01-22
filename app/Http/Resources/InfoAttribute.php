<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class InfoAttribute extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->resource;

        return [
            'teacher' => $this->when(!empty($resource['teacher']),
                !empty($resource['teacher']) ? $resource['teacher'] : null),
            'shipping_method' => $this->when(!empty($resource['shippingMethod']),
                !empty($resource['shippingMethod']) ? $resource['shippingMethod'] : null),
            'major' => $this->when(!empty($resource['major']), !empty($resource['major']) ? $resource['major'] : null),
            'services' => $this->when(!empty($resource['services']),
                !empty($resource['services']) ? $resource['services'] : null),
            'download_date' => $this->when(!empty($resource['downloadDate']),
                !empty($resource['downloadDate']) ? $resource['downloadDate'] : null),
            'educational_system' => $this->when(!empty($resource['educationalSystem']),
                !empty($resource['educationalSystem']) ? $resource['educationalSystem'] : null),
            'duration' => $this->getDuration($resource),
            'production_year' => $this->when(!empty($resource['productionYear']),
                !empty($resource['productionYear']) ? $resource['productionYear'] : null),
            'expiration_duration' => $this->when(!empty($resource['expirationDuration']),
                !empty($resource['expirationDuration']) ? $resource['expirationDuration'] : null),
            'grade' => $this->when(!empty($resource['grade']), !empty($resource['grade']) ? $resource['grade'] : null),
        ];
    }

    private function getDuration($resource)
    {
        if (Arr::has($resource, 'duration')) {
            return $resource['duration'];
        }

        if (Arr::has($resource, 'numberOfPages')) {
            return $resource['numberOfPages'];
        }

        return null;
    }
}
