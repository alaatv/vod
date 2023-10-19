<?php
/**
 * Created by PhpStorm.
 * User: Alaaa
 * Date: 1/8/2019
 * Time: 1:29 PM
 */

namespace App\Classes\Payment\RefinementRequest\Strategies;

use App\Classes\Payment\RefinementRequest\Refinement;
use Illuminate\Http\Response;

class ChargingWalletRefinement extends Refinement
{
    /**
     * @return Refinement
     */
    public function loadData(): Refinement
    {
        if ($this->statusCode != Response::HTTP_OK) {
            return $this;
        }

        $this->description .= 'شارژ کیف پول -';
        $this->cost = $this->walletChargingAmount;
        if ($this->cost > 0) {
            $result = $this->getNewTransaction(false);
            $this->statusCode = $result['statusCode'];
            $this->message = $result['message'];
            $this->transaction = $result['transaction'];
        } else {
            if ($this->cost == 0) {
                $this->statusCode = Response::HTTP_OK;
                $this->message = 'Zero cost';
                $this->transaction = null;
            } else {
                $this->statusCode = Response::HTTP_BAD_REQUEST;
                $this->message = 'Cost cant be minus';
            }
        }
        return $this;
    }
}
