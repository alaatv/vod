<?php


namespace App\Repositories;


use App\Models\Contractor;

class ContractorRepo extends AlaaRepo
{

    public static function getModelClass(): string
    {
        return Contractor::class;
    }

    public static function all()
    {
        return self::initiateQuery();
    }


}
