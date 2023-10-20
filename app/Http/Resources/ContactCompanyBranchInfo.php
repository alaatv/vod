<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use stdClass;

class ContactCompanyBranchInfo extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->resource;
        return [
            'name' => $this->when(Arr::has($resource, 'displayName'),
                Arr::has($resource, 'displayName') ? Arr::get($resource, 'displayName') : null),
            'address' => $this->when(Arr::has($resource, 'address'),
                Arr::has($resource, 'address') ? $this->getAddressString(Arr::get($resource, 'address')) : null),
//            'address'               => $this->when(Arr::has($resource,'address') , Arr::has($resource,'address')?new ContactBranchAddressInfo(Arr::get($resource , 'address')):null),
            'phones' => $this->when(Arr::has($resource, 'contacts'),
                Arr::has($resource, 'contacts') ? ContactBranchPhoneInfo::collection(Arr::get($resource,
                    'contacts')) : null),
//          'emergency_contacts'    => $this->when(Arr::has() , Arr::has()?:null),
//            'faxes'                 => $this->when(Arr::has($resource,'faxes') , Arr::has($resource,'faxes')?ContactBranchFaxInfo::collection(Arr::get($resource , 'faxes')):null),
            'emails' => $this->when(Arr::has($resource, 'emails'),
                Arr::has($resource, 'emails') ? ContactBranchEmailInfo::collection(Arr::get($resource,
                    'emails')) : null),
        ];
    }

    private function getAddressString(stdClass $addressObj): string
    {
        $address = '';
        $address .= isset($addressObj->city) ? $addressObj->city.' - ' : '';
        $address .= isset($addressObj->strret) ? $addressObj->strret.' - ' : '';
        $address .= isset($addressObj->avenue) ? $addressObj->avenue.' - ' : '';
        $address .= isset($addressObj->extra) ? $addressObj->extra.' - ' : '';
        $address .= isset($addressObj->postalCode) ? 'کد پستی : '.$addressObj->postalCode : '';
        return $address;
    }
}
