<?php


namespace App\Repositories;


class LogRepo
{

    public static function orderPropertiesArray($log)
    {
        return json_decode($log->properties);
    }

}
