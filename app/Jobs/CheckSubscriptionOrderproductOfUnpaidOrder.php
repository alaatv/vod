<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\SubscriptionRepo;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckSubscriptionOrderproductOfUnpaidOrder implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     *
     * @var User
     */
    private $user;

    /**
     *
     * @var Order
     */
    private $order;

    /**
     * CheckSubscriptionOrderproductOfUnpaidOrder constructor.
     *
     * @param  User  $user
     * @param  Order  $order
     */
    public function __construct(User $user, Order $order)
    {
        $this->user = $user;
        $this->order = $order;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $userDiscountSubscription = SubscriptionRepo::validProductSubscriptionOfUser($this->user->id,
            [Product::SUBSCRIPTION_12_MONTH]);
        if (!isset($userDiscountSubscription)) {

            return null;
        }

        $subscriptionDiscountObj = optional(optional($userDiscountSubscription->values)->discount);

        if (is_null($subscriptionDiscountObj)) {
            return null;
        }

        $subscriptionOrderproductIdArray = $subscriptionDiscountObj->orderproduct_id;
        $currentUsage = $subscriptionDiscountObj->usage_limit;
        if (!(isset($subscriptionOrderproductIdArray) && !empty($subscriptionOrderproductIdArray))) {
            return null;
        }

        foreach ($this->order->orderproducts as $orderproduct) {
            if (!in_array($orderproduct->id, $subscriptionOrderproductIdArray)) {
                continue;
            }

            $userDiscountSubscription->setUsageLimit(min($currentUsage + 1, 1));
            $userDiscountSubscription->unsetOrderproductId();
            $userDiscountSubscription->updateWithoutTimestamp();

            $orderproduct->discountAmount = 0;
            $orderproduct->updateWithoutTimestamp();

            $this->order->refreshCostWithoutReobtain();

        }


        return null;
    }
}
