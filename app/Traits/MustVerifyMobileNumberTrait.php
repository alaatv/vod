<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-10-22
 * Time: 17:21
 */

namespace App\Traits;

use App\Notifications\MobileVerified;
use App\Notifications\VerifyMobile;
use Illuminate\Support\Facades\Cache;

trait MustVerifyMobileNumberTrait
{
    /**
     * Determine if the user has verified their mobile number.
     *
     * @return bool
     */
    public function hasVerifiedMobile()
    {
        return !is_null($this->mobile_verified_at);
    }

    /**
     * Mark the given user's mobile as verified.
     *
     * @return bool
     */
    public function markMobileAsVerified()
    {
        return $this->forceFill([
            'mobile_verified_at' => $this->freshTimestamp(),
        ])
            ->save();
    }

    /**
     * Send the mobile verification notification.
     *
     * @return void
     */
    public function sendMobileVerificationNotification()
    {
        if ($this->setMobileVerificationCode()) {
            $this->notify(new VerifyMobile());
            Cache::tags('user_'.$this->id)->flush();
        }
    }

    /**
     * generate a verification code for given user
     *
     * @return bool
     */
    public function setMobileVerificationCode(): bool
    {
        $verificationCode = rand(10000, 99999);

        return $this->forceFill([
            'mobile_verified_code' => $verificationCode,
        ])
            ->save();
    }

    /**
     * Send the mobile verified notification.
     *
     * @return void
     */
    public function sendMobileVerifiedNotification()
    {
        $this->notify(new MobileVerified());
    }

    /**
     * get user's verification code
     *
     * @return string
     */
    public function getMobileVerificationCode()
    {
        return $this->mobile_verified_code;
    }
}
