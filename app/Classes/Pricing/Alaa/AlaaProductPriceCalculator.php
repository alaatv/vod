<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/30/2018
 * Time: 5:00 PM
 */

namespace App\Classes\Pricing\Alaa;


use App\Classes\Abstracts\Pricing\ProductPriceCalculator;

class AlaaProductPriceCalculator extends ProductPriceCalculator
{
    public function getPrice(): string
    {
        $priceInfo = [
            'price' => $this->calculatePrice(),
            'price_instalmentally' => $this->calculateInstalmentallyPrice(),
            'info' => [
                'productCost' => $this->rawCost,
                'discount' => [
                    'totalAmount' => $this->calculateTotalDiscountAmount(),
                    'info' => [
                        'bon' => [
                            'totalAmount' => $this->getBonDiscount(),
                            'info' => [
                                $this->bonName => [
                                    'number' => $this->totalBonNumber,
                                    'perUnitPercentage' => $this->bonDiscountPercentage,
                                    'totalPercentage' => $this->getBonTotalPercentage(),
                                ],
                            ],
                        ],
                        'product' => [
                            'totalAmount' => $this->getProductDiscount(),
                            'info' => [
                                'amount' => $this->discountCashAmount,
                                'percentageBase' => [
                                    'percentage' => $this->discountPercentage,
                                    'decimalValue' => $this->discountValue,
                                    'instalmentally_percentage' => $this->instalmentallyDiscountPercentage,
                                    'instalmentally_decimalValue' => $this->instalmentallyDiscountValue,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return json_encode($priceInfo);
    }
}
