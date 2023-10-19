<?php


namespace App\Traits\GiveGift;




use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderproductRepo;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

trait GiveGiftsHelper
{
    // todo: if gift be an array we should check that all of them gave to user
    public function giveGiftProducts($order, array $giftIds)
    {
        $orderProductIds = $order->orderproducts->pluck('product_id')->toArray();

        $giftGivenFlag = false;
        foreach ($giftIds as $giftId) {
            if (in_array($giftId, $orderProductIds)) {
                continue;
            }

            $gift = Product::find($giftId);
            if (!isset($gift)) {
                continue;
            }

            $price = $gift->price;

            try {
                OrderproductRepo::createGiftOrderproduct($order->id, $gift->id, $price['base']);
                $giftGivenFlag = true;
            } catch (Exception $exception) {
                Log::error("GiveGiftPlan error: gift:$gift->id, Not gave to {$order->user->id}. {$exception->getMessage()}");
            }

        }
        return $giftGivenFlag;
    }

    public function giveProducts($order, $productIds, $product_ids)
    {
        $productFlag = false;
        foreach ($productIds as $productId) {
            if (in_array($productId, $product_ids)) {
                continue;
            }

            $product = Product::find($productId);
            if (!isset($product)) {
                continue;
            }
            try {
                OrderproductRepo::createBasicOrderproduct($order->id, $productId, 0, 0);
                $productFlag = true;
            } catch (Exception $exception) {
                Log::error("GiveGiftPlan error: gift:$product->id, Not gave to {$order->user->id}. {$exception->getMessage()}");
            }
        }
        return $productFlag;
    }

    public function actionIsValid($action)
    {
        $klass = new ReflectionClass(self::class);
        return $klass->hasMethod($action);
    }

    public function giveHiddenSubProduct(Order $order, array $productMap)
    {
        $orderProducts = $order->orderproducts;
        $myOrderproducts = $order->orderproducts->whereIn('product_id', array_keys($productMap));
        if ($myOrderproducts->isEmpty()) {
            return null;
        }

        foreach ($myOrderproducts as $orderproduct) {
            $complimentaryproducts = Product::findMany($productMap[$orderproduct->product_id]);

            foreach ($complimentaryproducts as $complimentaryproduct) {
                if ($orderProducts->where('product_id', $complimentaryproduct->id)->isNotEmpty()) {
                    continue;
                }
                $price = Arr::get($complimentaryproduct->price, 'base', 0);
                OrderproductRepo::createHiddenOrderproduct(
                    $order->id,
                    $complimentaryproduct->id,
                    $price,
                    $price,
                    0,
                    0,
                    0,
                    $orderproduct?->instalmentQty,
                    $orderproduct?->paidPercent,
                );
            }
        }
    }
}
