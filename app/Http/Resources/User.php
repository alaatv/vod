<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class User
 *
 * @mixin \App\Models\User
 * */
class User extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Models\User)) {
            return [];
        }


        $profileCompletion = $this->completion();

        //   $this->loadMissing('major', 'grade', 'gender', 'wallets', 'userstatus');

        return [
            'id' => $this->id,
            'first_name' => $this->when(isset($this->firstName), $this->firstName),
            'last_name' => $this->when(isset($this->lastName), $this->lastName),
            'name_slug' => $this->when(isset($this->nameSlug), $this->nameSlug),
            'mobile' => $this->when(isset($this->mobile), $this->mobile),
            'mobile_verified_at' => $this->when(isset($this->mobile_verified_at), $this->mobile_verified_at),
            'national_code' => $this->when(isset($this->nationalCode), $this->nationalCode),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'kartemeli' => $this->when(isset($this->kartemeli), $this->kartemeli),
            'province' => $this->when(isset($this->province), $this->province),
            'city' => $this->when(isset($this->city), $this->city),
            'address' => $this->when(isset($this->address), $this->address),
            'postal_code' => $this->when(isset($this->postalCode), $this->postalCode),
            'school' => $this->when(isset($this->school), $this->school),
            'email' => $this->when(isset($this->email), $this->email),
            'bio' => $this->when(isset($this->bio), $this->bio),
            'info' => null,
            'major' => $this->when(isset($this->major), function () {
                return new Major($this->major);
            }),
            'grade' => $this->when(isset($this->grade), function () {
                return new Grade($this->grade);
            }),
            'gender' => $this->when(isset($this->gender), function () {
                return new  Gender($this->gender);
            }),
            'profile_completion' => !is_null($profileCompletion) ? (int) $profileCompletion : 0,
            'wallet_balance' => $this->getTotalWalletBalance(),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'edit_profile_url' => $this->when(isset($this->editProfileUrl), function () {
                return $this->editProfileUrl ?? null;
            }),
            'birthdate' => isset($this->birthdate) ? $this->birthdate : null,
            'has_purchased_anything' => $this->hasPurchasedAnything(),
            'shahr' => new ShahrLiteResource($this->shahr),
            'phone' => $this->phone,
        ];
    }
}
