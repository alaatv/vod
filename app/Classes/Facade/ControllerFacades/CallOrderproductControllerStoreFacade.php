<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/22/2018
 * Time: 11:59 AM
 */

namespace App\Classes\Facade\ControllerFacades;

use App\Classes\Abstracts\Facade\CallControllerStoreFacade;

class CallOrderproductControllerStoreFacade extends CallControllerStoreFacade
{
    public const CLASS_NAME = 'OrderproductController';

    protected function getControllerName(): string
    {
        return self::CLASS_NAME;
    }
}
