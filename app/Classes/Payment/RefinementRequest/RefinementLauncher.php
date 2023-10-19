<?php
/**
 * Created by PhpStorm.
 * User: Alaaa
 * Date: 1/8/2019
 * Time: 1:20 PM
 */

namespace App\Classes\Payment\RefinementRequest;

use App\Classes\Payment\RefinementRequest\Strategies\{ChargingWalletRefinement,
    OpenInInstalmentOrderRefinement,
    OpenOrderRefinement,
    OrderIdRefinement,
    TransactionRefinement};
use Illuminate\Support\Arr;

class RefinementLauncher
{
    /**
     * @var Refinement $refinement
     */
    private $refinement;

    public function __construct($refinement)
    {
        $this->refinement = $this->gteRefinementRequestStrategy($refinement);
    }

    /**
     * @param  array  $inputData
     *
     * @return Refinement
     */
    private function gteRefinementRequestStrategy(array $inputData): Refinement
    {
        $seller = $inputData['seller'] ?? config('constants.ALAA_SELLER');
        if (Arr::has($inputData, 'transaction_id')) { // closed order
            return new TransactionRefinement();
        } else {
            if (Arr::has($inputData, 'order_id')) { // closed order
                return new OrderIdRefinement();
            } else {
                if (Arr::has($inputData, 'walletId') && Arr::has($inputData,
                        'walletChargingAmount')) { // Charging Wallet
                    return new ChargingWalletRefinement();
                } else {
                    if (Arr::has($inputData, 'inInstalment')) {
                        return new OpenInInstalmentOrderRefinement($seller);
                    }
                }
            }
        }
        // Open order
        return new OpenOrderRefinement($seller);
    }

    /**
     * @param  array  $inputData
     *
     * @return array: [statusCode, message, user, order, cost, donateCost, transaction]
     */
    public function getData(array $inputData)
    {
        return $this->refinement->setData($inputData)
            ->validateData()
            ->loadData()
            ->getData();
    }

}
