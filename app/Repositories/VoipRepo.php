<?php

namespace App\Repositories;

use App\Models\Voip;
use App\Models\Voip;

class VoipRepo
{
    public static function storeٰVoipContact($request, $userID, $operatorID)
    {
        $voipID = $userID ? self::storeCallDocumentByCallerID($userID,
            $operatorID) : self::storeCallDocumentByCallerPhone($request->caller_phone, $operatorID);
        return $voipID;
    }

    public static function storeCallDocumentByCallerID($user, $operator)
    {
        return Voip::create([
            'caller_id' => $user,
            'operator_id' => $operator,
        ])->id;
    }

    public static function storeCallDocumentByCallerPhone($callerPhone, $operator)
    {
        return Voip::create([
            'callerPhone' => $callerPhone,
            'operator_id' => $operator,
        ])->id;
    }
}
