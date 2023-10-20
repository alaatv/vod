<?php

namespace App\Repositories;

use App\Models\VoipOperator;

class VoipOperatorRepo
{
    public static function getOperatorIdFromLocalPhone($operatorPhone)
    {
        return VoipOperator::where('local_phone_number', $operatorPhone)->first()?->operator_id;
    }
}
