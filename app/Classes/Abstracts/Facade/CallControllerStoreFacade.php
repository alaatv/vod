<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/22/2018
 * Time: 12:00 PM
 */

namespace App\Classes\Abstracts\Facade;

use App\Classes\Factory\ControllerFactory;
use App\Http\Controllers\Controller;

abstract class CallControllerStoreFacade
{
    /**
     * Calls store method of intended controller class
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function callStore(array $data)
    {
        $controllerObject = $this->getControllerObject();
        $orderproducts = $controllerObject->new($data);

        return $orderproducts;
    }

    /**
     * @return Controller
     */
    protected function getControllerObject(): Controller
    {
        return ControllerFactory::getControllerObject($this->getControllerName());
    }

    /**
     * @return string
     */
    abstract protected function getControllerName(): string;
}
