<?php

namespace App\Observers;

use App\Models\ReferralRequest;

class ReferralRequestObserver
{
    /**
     * Handle the ReferralRequest "created" event.
     *
     * @param  ReferralRequest  $referralRequest
     * @return void
     */
    public function created(ReferralRequest $referralRequest)
    {
        $user = $referralRequest->owner;
        $user->wallets()->firstOrCreate([
            'wallettype_id' => config('constants.WALLET_TYPE_MAIN'),
        ]);
        $user->salesManProfile()->firstOrCreate();
    }

    /**
     * Handle the ReferralRequest "updated" event.
     *
     * @param  ReferralRequest  $referralRequest
     * @return void
     */
    public function updated(ReferralRequest $referralRequest)
    {
        //
    }

    /**
     * Handle the ReferralRequest "deleted" event.
     *
     * @param  ReferralRequest  $referralRequest
     * @return void
     */
    public function deleted(ReferralRequest $referralRequest)
    {
        //
    }

    /**
     * Handle the ReferralRequest "restored" event.
     *
     * @param  ReferralRequest  $referralRequest
     * @return void
     */
    public function restored(ReferralRequest $referralRequest)
    {
        //
    }

    /**
     * Handle the ReferralRequest "force deleted" event.
     *
     * @param  ReferralRequest  $referralRequest
     * @return void
     */
    public function forceDeleted(ReferralRequest $referralRequest)
    {
        //
    }
}
