<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/22/2018
 * Time: 12:15 PM
 */

namespace App\Classes\Factory;

class ControllerFactory
{
    public static function getControllerObject($controllerName)
    {
        $classPath = "\App\\Http\\Controllers\\".$controllerName;

        return (new  $classPath());
    }
}
