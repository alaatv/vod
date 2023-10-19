<?php
/**
 * Created by PhpStorm.
 * User: Mohammad Shahrokhi
 * Date: 2021-01-03
 * Time: 13:00
 */

namespace App\Traits\User;


trait FCMTrait
{
    /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->firebasetokens->pluck('token')->last();
    }
}
