<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/30/2018
 * Time: 5:00 PM
 */

namespace App\Classes\Pricing\Alaa;


use App\Classes\Abstracts\Pricing\ProductPriceCalculator;
use App\Models\Product;

class AlaaProductPriceCalculatorInstalmentPurchase extends ProductPriceCalculator
{
    public function getPrice(): string
    {
        $priceInfo = [
            'price' => $this->calculatePrice(),
            'info' => [
                'productCost' => $this->rawCost,
                'discount' => [
                    'totalAmount' => $this->calculateTotalDiscountAmount(),
                    'info' => [
                        'product' => [
                            'totalAmount' => $this->getProductDiscount(),
                            'info' => [
                                'amount' => $this->discountCashAmount,
                                'percentageBase' => [
                                    'percentage' => $this->discountPercentage,
                                    'decimalValue' => $this->discountValue,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return json_encode($priceInfo);
    }

    /**
     * Calculates total discount cash amount
     *
     * @return mixed
     */
    protected function calculateTotalDiscountAmount(): int
    {
//        return $this->getBonDiscount() + $this->getProductDiscount();
        return $this->getProductDiscount();
    }

    /**
     * Obtains total discount product percentage based on product discount
     *
     * @return int
     */
    protected function getProductDiscount(): int
    {
        return $this->discountPercentage;
    }

    public function getFinalDiscountValue(Product $product)
    {
        return $product->getFinalDiscountValueForInstalmentPurchase();
    }

    public function obtainDiscount(Product $product)
    {
        return $product->obtainDiscountForInstalmentPurchase();
    }

    public function obtainDiscountAmount(Product $product)
    {
        return 0;
    }

    /**
     * Calculates bon discount
     *
     * @return mixed
     */
    protected function getBonDiscount(): int
    {
        return 0;
    }
}
