<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 10/11/2018
 * Time: 2:33 PM
 */

namespace App\Classes\Abstracts\Checkout;

use App\Traits\Cashier\CashierCouponUnit;
use App\Traits\Cashier\CashierOrderDiscountUnit;
use App\Traits\Cashier\CashierOrderproductSumUnit;
use App\Traits\Cashier\CashierOrderproductUnit;
use App\Traits\Cashier\CashierOrderUnit;
use App\Traits\Cashier\CashierReferralCodeUnit;
use App\Traits\Cashier\CashierWalletUnit;

abstract class Cashier
{
    use CashierOrderUnit;
    use CashierCouponUnit;
    use CashierOrderDiscountUnit;
    use CashierOrderproductUnit;
    use CashierOrderproductSumUnit;
    use CashierWalletUnit;
    use CashierReferralCodeUnit;

    /**
     * Presents Cashier's price data
     *
     * @return mixed
     */
    abstract public function getPrice();
}
