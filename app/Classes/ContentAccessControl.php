<?php


namespace App\Classes;


use App\Models\Content;
use App\Models\User;

class ContentAccessControl
{

    private $content;

    /**
     * ContentAccessControl constructor.
     *
     * @param $content
     */
    public function __construct(Content $content)
    {
        $this->content = $content;
    }

    public function canUserAccessContent(?User $user)
    {
        $accessCode = $this->getAccessCode($user);

        if ($accessCode == 1) {
            return [true, null];
        }

        $access = false;
        if ($accessCode == 0) {
            $order = optional($user)->filterOrdersByProductsOfContent($this->content)
                ?->where('paymentstatus_id', config('constants.PAYMENT_STATUS_INDEBTED'))
                ?->first();

            if ($order) {
                return [$access, route('web.user.orders').'?order_id='.$order->id];
            }
        }

        $productId = optional($this->content->allProducts()->first())->id;
        if ($productId) {
            return [$access, route('product.show', $productId)];
        }

        return [$access, route('web.home')];
    }

    public function getAccessCode(?User $user)
    {
        /**
         *   Outputs:
         *   0 => can't see content
         *   2 => it's not determine whether can see content or not
         *   1 => can see content
         */
        if ($this->content->isFree) {
            return 1;
        }

        if (is_null($user)) {
            return 2;
        }

        return $user->canSeeContent($this->content) ? 1 : 0;
    }
}
