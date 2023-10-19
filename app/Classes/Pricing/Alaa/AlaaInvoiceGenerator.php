<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/22/2018
 * Time: 3:42 PM
 */

namespace App\Classes\Pricing\Alaa;

use App\Collection\OrderproductCollection;
use App\Models\Order;
use App\Models\Orderproduct;
use Exception;
use Illuminate\Support\Collection;

class AlaaInvoiceGenerator
{
    /**
     * @param  Order  $order
     *
     * @return array
     * @throws Exception
     */
    public function generateOrderInvoice(Order $order): array
    {
        $orderproductsInfo = $this->getOrderproductsInfo($order);
        /** @var OrderproductCollection $orderproducts */
        $orderproducts = $orderproductsInfo['purchasedOrderproducts'];

        $orderproducts->reCheckOrderproducs();

        $order = $order->fresh();

        $orderPriceArray = $order->obtainOrderCost(true);

        /** @var OrderproductCollection $calculatedOrderproducts */
        $calculatedOrderproducts = $orderPriceArray['calculatedOrderproducts'];
        $calculatedOrderproducts->updateCostValues($order->coupon);

        $orderproductsRawCost = $orderPriceArray['sumOfOrderproductsRawCost'];
        $totalCost = $orderPriceArray['totalCost'];
        $payableByWallet = $order->seller == config('constants.SOALAA_SELLER') ? null : $orderPriceArray['payableAmountByWallet'];

        $orderProductCount = $this->orderproductFormatter($calculatedOrderproducts);

        return $this->invoiceFormatter($calculatedOrderproducts, $orderProductCount, $orderproductsRawCost, $totalCost,
            $payableByWallet);
    }

    /**
     * @param  Order  $order
     *
     * @return array
     */
    private function getOrderproductsInfo(Order $order)
    {
        /** @var OrderproductCollection $allOrderproducts */
        $allOrderproducts = $order->orderproducts->sortByDesc('created_at');

        $purchasedOrderproducts =
            $allOrderproducts->whereType([
                config('constants.ORDER_PRODUCT_TYPE_DEFAULT'), config('constants.ORDER_PRODUCT_EXCHANGE')
            ]);
        $giftOrderproducts = $allOrderproducts->whereType([config('constants.ORDER_PRODUCT_GIFT')]);

        return [
            'purchasedOrderproducts' => $purchasedOrderproducts,
            'giftOrderproducts' => $giftOrderproducts,
        ];
    }

    /**
     * Formats orderproduct collection and return total number of orderproducts
     *
     * @param  OrderproductCollection  $orderproducts
     *
     * @return int
     */
    private function orderproductFormatter(OrderproductCollection &$orderproducts): int
    {
        $newPrices = $orderproducts->getNewPrices();

        $orderProductCount = 0;
        $orderproducts = new OrderproductCollection($orderproducts->filterNoneDonation()->groupBy('grandId')
            ->map(function ($orderproducts) use (&$orderProductCount) {
                $orderProductCount += $orderproducts->count();

                return [
                    'grand' => $orderproducts->first()->grand_product ?? null,
                    'orderproducts' => $orderproducts,
                ];
            })
            ->values()
            ->all());

        $orderproducts = $orderproducts->setNewPrices($newPrices);

        return $orderProductCount;
    }

    private function invoiceFormatter(
        $orderproducts,
        $orderproductCount,
        $orderproductsRawCost,
        $totalCost,
        $payableByWallet
    ) {
        $discount = $orderproductsRawCost - $totalCost;

        return [
            'items' => $orderproducts,
            'orderproductCount' => $orderproductCount,
            'price' => [
                'base' => $orderproductsRawCost,
                'discount' => $discount,
                'final' => $totalCost,
                'payableByWallet' => $payableByWallet,
            ],
        ];
    }

    public function generateInInstalmentInvoice(Order $order)
    {
        $orderproducts = $order->orderproducts;
        $orderCost = 0;
        /** @var Orderproduct $orderproduct */
        foreach ($orderproducts as $orderproduct) {
            $productPrice = $orderproduct->product?->basePrice;
            $orderproduct->cost = $productPrice;
            $orderproduct->updateWithoutTimestamp();
            $orderCost += $productPrice;
        }
        $order->costwithoutcoupon = $orderCost;
        $order->cost = 0;
        $order->updateWithoutTimestamp();
    }

    /**
     * @param  Collection  $fakeOrderproducts
     *
     * @return array
     */
    public function generateFakeOrderproductsInvoice(Collection $fakeOrderproducts)
    {
        /** @var OrderproductCollection $fakeOrderproducts */
        $groupPriceInfo = $fakeOrderproducts->calculateGroupPrice();

        $orderProductCount = $this->orderproductFormatter($fakeOrderproducts);

        return $this->invoiceFormatter($fakeOrderproducts, $orderProductCount, $groupPriceInfo['rawCost'],
            $groupPriceInfo['customerCost'], 0);
    }
}
