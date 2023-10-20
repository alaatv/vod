<?php

namespace App\Listeners;

use App\Events\BonyadEhsanUserRegistered;
use App\Events\BonyadEhsanUserUpdate;
use App\Events\SendOrderNotificationsEvent;
use App\Models\Coupon;
use App\Models\Major;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BonyadEhsanEventSubscriber
{

    public function handleUserRegister($event)
    {
        $event->user->attachRole(config('constants.ROLE_BONYAD_EHSAN_USER'));
        auth()->user()->consultant->increaseRegistrationNumber();

        $this->giveUserProducts($event);
    }

    private function giveUserProducts($event)
    {
        $majorId = $event->user->major_id;

        [$productsThatUserMustHave, $productIsThatUserHas] = [
            $this->getProductsUserMustHave($majorId),
            $this->getUserBonyadProducts($event->user),
        ];
        $productsThatUserMustHave =
            $productsThatUserMustHave->filter(fn($product) => !in_array($product->id, $productIsThatUserHas));

        if ($productsThatUserMustHave->isNotEmpty()) {
            $this->addProductsToUser($event->user, $productsThatUserMustHave);
        }
    }

    private function getProductsUserMustHave(int $majorId)
    {
        switch ($majorId) {
            case Major::RIYAZI:
                return Product::query()->whereIn('id',
                    [Product::RAHE_ABRISHAM99_PACK_RIYAZI, Product::RAHE_ABRISHAM1401_PACK_OMOOMI, 804])->get();
            case Major::TAJROBI:
                return Product::query()->whereIn('id',
                    [Product::RAHE_ABRISHAM99_PACK_TAJROBI, Product::RAHE_ABRISHAM1401_PACK_OMOOMI, 819])->get();
            case Major::ENSANI :
                return Product::query()->whereIn('id',
                    [Product::RAHE_ABRISHAM1401_PACK_OMOOMI, Product::HAMAYESH_BUNDLES_ENSANI, 834])->get();
            default:
                return [];
        }
    }

    // helpers

    private function getUserBonyadProducts(User $user): array
    {
        return $user->getUserProductsId2();
    }

    private function addProductsToUser(User $user, Collection $products)
    {
        try {
            DB::beginTransaction();
            $order = OrderRepo::createBasicCompletedOrder(
                $user->id,
                config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'),
                couponId: Coupon::BONYAD_EHSAN_COUPON,
                couponDiscount: 100);

            $orderPrice = 0;
            foreach ($products as $product) {
                $orderPrice += $product->basePrice;
                OrderproductRepo::createBasicOrderproduct($order->id, $product->id, $product->basePrice,
                    $product->basePrice, 1);
            }

            $order->update([
                'cost' => $orderPrice,
                'costwithoutcoupon' => 0,
                'coupon_id' => Coupon::BONYAD_EHSAN_COUPON,
                'couponDiscount' => 100,
            ]);

            event(new SendOrderNotificationsEvent($order, $user));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('BonyadEhsanLogs')->error('file:'.$e->getFile().':'.$e->getLine());
        }
    }

    public function handleUserUpdate($event)
    {
        //To Do : log
        $this->giveUserProducts($event);
    }

    public function subscribe($events)
    {
        $events->listen(
            BonyadEhsanUserRegistered::class,
            [BonyadEhsanEventSubscriber::class, 'handleUserRegister']
        );

        $events->listen(
            BonyadEhsanUserUpdate::class,
            [BonyadEhsanEventSubscriber::class, 'handleUserUpdate']
        );
    }
}
