<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/2/2018
 * Time: 2:42 PM
 */

namespace App\Classes\Abstracts\Checkout;

use PHPUnit\Framework\Exception;

abstract class CheckoutInvoker
{
    protected $chainArray = [];

    protected $chainObjectArray = [];

    protected $chainClassesNameSpace;

    protected $cashier;

    /**
     * Runs the process checkout
     *
     * @return mixed
     */
    public function checkout()
    {
        $this->cashier = $this->initiateCashier();
        $this->chainArray = $this->fillChainArray();
        $this->initiateChain();

        $this->chainClassesNameSpace = $this->getChainClassesNameSpace();

        $chainStart = $this->determineChainStart();

        return $this->runCashier($chainStart);
    }

    /**
     * Initiates cashier
     *
     * @return mixed
     */
    abstract protected function initiateCashier(): Cashier;

    /**
     * @return array
     */
    abstract protected function fillChainArray(): array;

    /**
     * Initiates check out process
     *
     * @return void
     */
    protected function initiateChain()
    {
        foreach ($this->chainArray as $chainCellName) {
            $chainCellClassName = "\\".$this->getChainClassesNameSpace()."\\".studly_case($chainCellName);
            $chainCell = (new $chainCellClassName());
            array_push($this->chainObjectArray, $chainCell);
        }

        foreach ($this->chainObjectArray as $key => $chainObject) {
            if (isset($this->chainObjectArray[$key + 1])) {
                $chainObject->setSuccessor($this->chainObjectArray[$key + 1]);
            }
        }
    }

    /**
     * @return string
     */
    abstract public function getChainClassesNameSpace(): string;

    /**
     * @return CheckoutProcessor
     */
    protected function determineChainStart(): CheckoutProcessor
    {
        if (!isset($this->chainObjectArray[0])) {
            throw new Exception('No chain starter found!');
        }

        return $this->chainObjectArray[0];
    }

    /**
     * @param  CheckoutProcessor  $processor
     *
     * @return mixed
     */
    protected function runCashier(CheckoutProcessor $processor)
    {
        return $processor->process($this->cashier);
    }
}
