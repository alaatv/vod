<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/30/2018
 * Time: 4:59 PM
 */

namespace App\Classes\Abstracts\Pricing;

use App\Collection\ProductCollection;
use App\Models\Product;
use App\Models\User;
use App\Traits\User\AssetTrait;

abstract class ProductPriceCalculator
{
    use AssetTrait;

    /*
    |--------------------------------------------------------------------------
    | Properties Methods
    |--------------------------------------------------------------------------
    */

    protected $bonName;

    protected $rawCost;

    protected $discountValue;

    protected $discountPercentage;

    protected $instalmentallyDiscountValue;

    protected $instalmentallyDiscountPercentage;

    protected $bonDiscountPercentage;

    protected $totalBonNumber;

    protected $discountCashAmount;

    /**
     * CostCentre constructor.
     *
     * @param  User  $user
     * @param  Product  $product
     */
    public function __construct(Product $product, User $user = null)
    {
        $this->rawCost = $this->obtainPrice($product, $user);
        $this->discountValue = $this->getFinalDiscountValue($product);
        $this->discountPercentage = $this->obtainDiscount($product);
        $this->instalmentallyDiscountValue = $this->getFinalInstalmentallyDiscountValue($product);
        $this->instalmentallyDiscountPercentage = $this->obtainInstalmentallyDiscount($product);
        $this->discountCashAmount = $this->obtainDiscountAmount($product);

        $bonName = config('constants.BON1');
        $this->bonName = $bonName;
//        $this->totalBonNumber = (int)optional($user)->userHasBon($bonName);
        $this->totalBonNumber = 0;
        if (isset($user)) //Note: With out this if we query the database every time even when there is nothing to do with bon discount like calculating order's cost
        {
//            $this->bonDiscountPercentage = $product->obtainBonDiscount($bonName);
            $this->bonDiscountPercentage = 0;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Abstract Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Obtains product's price (rawCost)
     *
     * @param  Product  $product
     * @param  User|null  $user
     *
     * @return int|null
     */
    public function obtainPrice(Product $product, ?User $user = null): ?int
    {
        $cost = 0;

        if ($product->isFree()) {
            return $cost;
        }

        if ($product->isRoot()) {
            if ($product->isConfigurable()) {
                return $this->obtainConfigurableGrandPrice($product);
            }

            if ($product->isSelectable()) {
                return $this->obtainSelectableGrandPrice($product, $user);
            }

            return $product->basePrice;
        }

        $grandParent = $product->grandParent;
        if ($grandParent->isConfigurable()) {
            return $this->obtainChildOfConfigurablePrice($product, $grandParent);
        }

        if ($grandParent->isSelectable()) {
            return $this->obtainChildOfSelectablePrice($product);
        }
        if ($product->isChildOfGrandChild()) {
            return $this->obtainChildOfGrandChildPrice($product);
        }
        return $cost;
    }

    /*
    |--------------------------------------------------------------------------
    | Protected Methods
    |--------------------------------------------------------------------------
    */

    private function obtainConfigurableGrandPrice(Product $product)
    {
        /** @var ProductCollection $enableChildren */
        $enableChildren = $product->children->where('enable', 1); // It is not query efficient to use scopeEnable
        if ($enableChildren->count() == 1) {
            $cost = $this->obtainPrice($enableChildren->first());
        } else {
            $cost = $product->basePrice;
        }

        return $cost;
    }

    private function obtainSelectableGrandPrice(Product $product, ?User $user)
    {
        $allChildren = $product->getAllChildren()->where('pivot.isDefault', 1);

        $excludedChildren = $this->searchProductTreeInUserAssetsCollection($product, $user);
        if (!empty($excludedChildren)) {
            $allChildren = $allChildren->whereNotIn('pivot.child_id', $excludedChildren);
        }
        if ($allChildren->isNotEmpty()) {
            $cost = 0;
            foreach ($allChildren as $product) {
                /** @var Product $product */
                $cost += $this->obtainPrice($product);
            }
        } else {
            if ($product->basePrice != 0) {
                $cost = $product->basePrice;
            } else {
                $cost = null;
            }
        }

        return $cost;
    }

    private function obtainChildOfConfigurablePrice(Product $product, ?Product $grandParent)
    {
        if ($product->basePrice != 0) {
            return $product->basePrice;
        }

        return optional($grandParent)->basePrice;

        //ToDo :Commented for the sake of reducing queries . This snippet gives a second approach for calculating children's cost of a configurable product
        /*$attributevalues = $product->attributevalues->where("attributetype_id", config("constants.ATTRIBUTE_TYPE_MAIN"));
        foreach ($attributevalues as $attributevalue) {
            if (isset($attributevalue->pivot->extraCost))
                $cost += $attributevalue->pivot->extraCost;
        }*/
    }

    private function obtainChildOfSelectablePrice(Product $product)
    {
        if ($product->basePrice != 0) {
            return $product->basePrice;
        }
        $children = $product->children;
        $cost = 0;
        foreach ($children as $child) {
            $cost = $child->basePrice;
        }
        return $cost;
    }

    private function obtainChildOfGrandChildPrice(Product $product)
    {
        return $product->basePrice;
    }

    /*
    |--------------------------------------------------------------------------
    | Public Methods
    |--------------------------------------------------------------------------
    */

    public function getFinalDiscountValue(Product $product)
    {
        return $product->getFinalDiscountValue();
    }

    public function obtainDiscount(Product $product)
    {
        return $product->obtainDiscount();
    }

    public function getFinalInstalmentallyDiscountValue(Product $product)
    {
        return $product->getFinalInstalmentallyDiscountValue();
    }

    public function obtainInstalmentallyDiscount(Product $product)
    {
        return $product->obtainInstalmentallyDiscount();
    }

    public function obtainDiscountAmount(Product $product)
    {
        return $product->obtainDiscountAmount();
    }

    /**
     * Returns price info array
     *
     * @return
     */
    abstract public function getPrice();

    /**
     * @param  int  $rawCost
     *
     * @return ProductPriceCalculator
     */
    public function setRawCost($rawCost): ProductPriceCalculator
    {
        $this->rawCost = $rawCost;

        return $this;
    }

    /**
     * @param  int  $discountPercentage
     *
     * @return ProductPriceCalculator
     */
    public function setDiscountPercentage($discountPercentage): ProductPriceCalculator
    {
        $this->discountPercentage = $discountPercentage;

        return $this;
    }

    /**
     * @param  int  $bonDiscountPercentage
     *
     * @return ProductPriceCalculator
     */
    public function setBonDiscountPercentage($bonDiscountPercentage): ProductPriceCalculator
    {
        $this->bonDiscountPercentage = $bonDiscountPercentage;

        return $this;
    }

    /**
     * @param  int  $totalBonNumber
     *
     * @return ProductPriceCalculator
     */
    public function setTotalBonNumber(int $totalBonNumber): ProductPriceCalculator
    {
        $this->totalBonNumber = $totalBonNumber;

        return $this;
    }

    /**
     * @param  int  $discountCashAmount
     *
     * @return ProductPriceCalculator
     */
    public function setDiscountCashAmount($discountCashAmount): ProductPriceCalculator
    {
        $this->discountCashAmount = $discountCashAmount;

        return $this;
    }

    /**
     * Calculates the price
     *
     * @return mixed
     */
    protected function calculatePrice(): int
    {
        return $this->rawCost - $this->calculateTotalDiscountAmount();
    }

    /**
     * Calculates total discount cash amount
     *
     * @return mixed
     */
    protected function calculateTotalDiscountAmount(): int
    {
        return $this->getBonDiscount() + $this->getProductDiscount();
    }

    /**
     * Calculates bon discount
     *
     * @return mixed
     */
    protected function getBonDiscount(): int
    {
        return 0;
//        return $this->getBonTotalPercentage() * ($this->rawCost - $this->getProductDiscount());
    }

    /*
    |--------------------------------------------------------------------------
    | Private Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Obtains total discount product percentage based on product discount
     *
     * @return int
     */
    protected function getProductDiscount(): int
    {
        return max($this->discountPercentage * $this->rawCost, $this->discountCashAmount);
    }

    /**
     * Calculates the instalmentally price
     *
     * @return mixed
     */
    protected function calculateInstalmentallyPrice(): int
    {
        return $this->rawCost - $this->calculateTotalInstalmentallyDiscountAmount();
    }

    /**
     * Calculates total discount cash amount
     *
     * @return mixed
     */
    protected function calculateTotalInstalmentallyDiscountAmount(): int
    {
        return $this->getProductInstalmentallyDiscount();
    }

    /**
     * Obtains total discount product percentage based on product discount
     *
     * @return int
     */
    protected function getProductInstalmentallyDiscount(): int
    {
        return $this->instalmentallyDiscountPercentage * $this->rawCost;
    }

    /**
     * Obtains total bon percentage
     *
     * @return float|int
     */
    protected function getBonTotalPercentage()
    {
        return 0;
//        return min($this->bonDiscountPercentage * $this->totalBonNumber, 1);
    }
}
