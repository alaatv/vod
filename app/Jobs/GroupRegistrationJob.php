<?php

namespace App\Jobs;

use App\Models\Product;
use App\Traits\SearchiaCommonTrait;
use App\Traits\UserCommon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GroupRegistrationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SearchiaCommonTrait;
    use SerializesModels;
    use UserCommon;

    private $user;
    private $rows;
    private $products;
    private $giftProducts;
    private $discount;
    private $paymentStatusId;
    private $orderStatusId;

    public function __construct($user, $rows, $products, $giftProducts, $discount, $paymentStatusId, $orderStatusId)
    {
        $this->user = $user;
        $this->rows = $rows;
        $this->products = $products;
        $this->giftProducts = $giftProducts;
        $this->discount = $discount;
        $this->paymentStatusId = $paymentStatusId;
        $this->orderStatusId = $orderStatusId;
    }

    public function handle()
    {
        $user = $this->user;
        $rows = $this->rows;
        $products = $this->products;
        $giftProducts = $this->giftProducts;
        $discount = $this->discount;
        $paymentStatusId = $this->paymentStatusId;
        $orderStatusId = $this->orderStatusId;

        foreach ($rows as $index => $row) {

            if ($index == 0) {
                continue;
            }

            $row = $this->usersOrderImportMapItems($row);

            [$user, $userInsertionIsFailed, $userInsertionReason, $userHasBeenUpdated] = $this->usersImport($row);

            $row->push($userInsertionIsFailed ? 'ناموفق' : 'موفق');
            $row->push(implode('، ', $userInsertionReason));

            if ($userInsertionIsFailed && !$userHasBeenUpdated) {
                continue;
            }

            // Discard products that have already been registered for the user.
            $remainProductIds = $this->notExistsProducts($user, $products);
            $remainGiftProductIds = $this->notExistsProducts($user, $giftProducts);

            if (empty($remainProductIds) && empty($remainGiftProductIds)) {
                Log::error('All of products add to user previously!');
                continue;
            }

            $remainProducts = Product::find($remainProductIds);
            $remainGiftProducts = Product::find($remainGiftProductIds);

            $order = $this->create3AOrderForUser($user, $paymentStatusId, $discount, $orderStatusId);

            if (!isset($order)) {
                Log::error('Order not found');
                continue;
            }

            if (!empty($remainProducts)) {
                $orderProductsCost = $this->add3AProductToUser($order, $user, $remainProducts);
            }

            if (!empty($remainGiftProducts)) {
                $orderGiftProductsCost = $this->addGiftToUser($order, $user, $remainGiftProducts);
            }

            if (!isset($orderProductsCost) && !isset($orderGiftProductsCost)) {
                $order->delete();
                continue;
            }
        }
        $rows[0]->push('وضعیت افزودن');
        $rows[0]->push('دلیل افزوده نشدن');

        dispatch(new ExportGroupRegistrationExcel($user, $rows));
    }
}
