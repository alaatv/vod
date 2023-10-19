<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/2/2018
 * Time: 12:37 PM
 */

namespace App\Classes\Abstracts\Pricing;

use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Subscription;
use App\Repositories\SubscriptionRepo;

abstract class OrderproductPriceCalculator
{
    public const ORDERPRODUCT_CALCULATOR_MODE_CALCULATE_FROM_BASE = 'calculate_mode_from_base';

    public const ORDERPRODUCT_CALCULATOR_MODE_CALCULATE_FROM_RECORD = 'calculate_mode_from_record';

    protected $orderproduct;

    protected $mode;

    /**
     * OrderproductPriceCalculator constructor.
     *
     * @param $orderproduct
     */
    public function __construct($orderproduct)
    {
        $this->orderproduct = $orderproduct;
        $this->mode = self::getDefaultMode();
    }

    /**
     * Gets default mode
     *
     * @return mixed
     */
    public static function getDefaultMode()
    {
        return self::ORDERPRODUCT_CALCULATOR_MODE_CALCULATE_FROM_BASE;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param  string  $mode
     *
     * @return OrderproductPriceCalculator
     */
    public function setMode(string $mode): OrderproductPriceCalculator
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return array
     */
    protected function getOrderproductPrice(): array
    {
        return match ($this->mode) {
            self::ORDERPRODUCT_CALCULATOR_MODE_CALCULATE_FROM_BASE => $this->obtainOrderproductPrice($this->orderproduct),
            self::ORDERPRODUCT_CALCULATOR_MODE_CALCULATE_FROM_RECORD => $this->obtainOrderproductPrice($this->orderproduct,
                false),
            default => [],
        };
    }

    /**
     * Calculates intended Orderproduct price
     *
     * @param  Orderproduct  $orderproduct
     * @param  bool  $calculate
     *
     * @return array
     */
    protected function obtainOrderproductPrice(Orderproduct $orderproduct, $calculate = true): array
    {
        /** @var Order $order */
        $order = $orderproduct->order;
        $user = optional($order)->user;
        if ($calculate) {
            $product = $orderproduct->product;
            $priceArray = $product->calculatePayablePrice();
            $price = $priceArray['cost'];
            $productDiscountPercentage =
                ($order?->isInInstalment) ? $priceArray['productInstalmentallyDiscount'] : $priceArray['productDiscount'];
            $productDiscountValue =
                ($order?->isInInstalment) ? $priceArray['productInstalmentallyDiscountValue'] : $priceArray['productDiscountValue'];
            $productDiscountAmount = $priceArray['productDiscountAmount'];

            /** @var Subscription $userDiscountSubscription */
            $userDiscountSubscription =
                isset($user) ? SubscriptionRepo::validProductSubscriptionOfUser($user->id,
                    [Product::SUBSCRIPTION_12_MONTH]) : null;
            if (isset($userDiscountSubscription)) {
                $currentUsage =
                    optional(optional($userDiscountSubscription->values)->discount)->usage_limit;
                $subscriptionOrderproductIdArray =
                    optional(optional($userDiscountSubscription->values)->discount)->orderproduct_id;
                if (isset($subscriptionOrderproductIdArray) && !empty($subscriptionOrderproductIdArray)) {
                    if (in_array($orderproduct->id, $subscriptionOrderproductIdArray)) {
                        $productDiscountAmount += (optional(optional($userDiscountSubscription)->values)->discount)->discount_amount;
                    }
                } elseif ($orderproduct->isRaheAbrisham() && $currentUsage > 0) {
                    $productDiscountAmount += (optional(optional($userDiscountSubscription)->values)->discount)->discount_amount;
                    $userDiscountSubscription->setUsageLimit(max($currentUsage - 1, 0));
                    $userDiscountSubscription->setOrderproductId($orderproduct->id);
                    $userDiscountSubscription->updateWithoutTimestamp();
                }
            }

        } else {
            $price = $orderproduct->cost;
            $productDiscountValue = $orderproduct->getRawOriginal('discountPercentage');
            $productDiscountPercentage = $orderproduct->discountPercentage;
            $productDiscountAmount = $orderproduct->discountAmount;
        }


        $orderProductExtraPrice = $orderproduct->getExtraCost();
        $totalBonDiscountPercentage = $orderproduct->getTotalBonDiscountPercentage();
        $totalBonDiscountValue = $orderproduct->getTotalBonDiscountDecimalValue();

        $price = (int) $price;

        $customerPrice =
            (int) (($price * (1 - $productDiscountPercentage)) * (1 - $totalBonDiscountPercentage) - $productDiscountAmount);

        $discount = $price - $customerPrice;
        $totalPrice = $orderproduct->quantity * $customerPrice;
        $orderproduct->tmp_final_cost = $totalPrice;
        $orderproduct->updateWithoutTimestamp();

        return [
            ///////////////Details///////////////////////
            'cost' => $price,
            'extraCost' => $orderProductExtraPrice,
            'productDiscount' => (int) $productDiscountValue,
            'productDiscountPercentage' => $productDiscountPercentage,
            'bonDiscount' => $totalBonDiscountValue,
            'bonDiscountPercentage' => $totalBonDiscountPercentage,
            'productDiscountAmount' => (int) $productDiscountAmount,
            ////////////////////Total///////////////////////
            'customerCost' => $customerPrice,
            'discount' => $discount,
            'totalCost' => $totalPrice,
        ];
    }
}
