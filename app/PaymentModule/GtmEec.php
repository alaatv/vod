<?php

namespace App\PaymentModule;

use App\Models\Order;

class GtmEec
{
    /**
     * @param  int  $orderId
     * @param  string  $device
     * @param        $paidPrice
     *
     * @return array
     */
    public function generateGtmEec(int $orderId, string $device, $paidPrice): array
    {
        $gtmEec = [];
        if (!isset($orderId)) {
            return $gtmEec;
        }
        $order = Order::find($orderId);
        $orderproducts = $order->normalOrderproducts;
        $orderproducts->loadMissing('product');

        $gtmEec = [
            'actionField' => [
                'id' => (string) $order->id,
                'affiliation' => $device,
                'revenue' => (string) number_format($paidPrice ?? 1, 2, '.', ''),
                'tax' => '0.00',
                'shipping' => '0.00',
                'coupon' => (string) optional($order->coupon)->code ?? '',
            ],
            'products' => [],
        ];

        foreach ($orderproducts as $orderproduct) {
            $sharedCost = $orderproduct->getSharedCostOfTransaction();
            if (!isset($sharedCost)) {
                continue;
            }

//            if($orderproduct->cost == 0 || $orderproduct->tmp_final_cost === 0 || $orderproduct->discountPercentage == 100)
//            {
//                continue ;
//            }

            $gtmEec['products'][] = [
                'id' => (string) $orderproduct->product->id,
                'name' => $orderproduct->product->name,
                'category' => (isset($orderproduct->product->category)) ? $orderproduct->product->category : '-',
                'variant' => '-',
                'brand' => 'آلاء',
                'quantity' => 1,
                'price' => (string) number_format($sharedCost, 2, '.', ''),
            ];
        }
        return $gtmEec;
    }
}
