<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/27/2018
 * Time: 11:37 AM
 */

namespace App\Traits\Cashier;

use Illuminate\Support\Collection;

trait CashierOrderproductUnit
{
    protected $rawOrderproductsToCalculateFromBase; //orderproducts that should be recalculated based on new conditions

    protected $rawOrderproductsToCalculateFromRecord; //orderproducts that should be calculated based recorded data

    protected $calculatedOrderproducts;

    /**
     * @return mixed
     */
    public function getRawOrderproductsToCalculateFromBase()
    {
        return $this->rawOrderproductsToCalculateFromBase;
    }

    /**
     * @param  mixed  $rawOrderproductsToCalculateFromBase
     *
     * @return mixed
     */
    public function setRawOrderproductsToCalculateFromBase($rawOrderproductsToCalculateFromBase)
    {
        $this->rawOrderproductsToCalculateFromBase = $rawOrderproductsToCalculateFromBase;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRawOrderproductsToCalculateFromRecord()
    {
        return $this->rawOrderproductsToCalculateFromRecord;
    }

    /**
     * @param  mixed  $rawOrderproductsToCalculateFromRecord
     *
     * @return mixed
     */
    public function setRawOrderproductsToCalculateFromRecord($rawOrderproductsToCalculateFromRecord)
    {
        $this->rawOrderproductsToCalculateFromRecord = $rawOrderproductsToCalculateFromRecord;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCalculatedOrderproducts(): ?Collection
    {
        return $this->calculatedOrderproducts;
    }

    /**
     * @param  Collection  $calculatedOrderproducts
     *
     * @return mixed
     */
    public function setCalculatedOrderproducts(Collection $calculatedOrderproducts)
    {
        $this->calculatedOrderproducts = $calculatedOrderproducts;

        return $this;
    }
}
