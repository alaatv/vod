<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-15
 * Time: 16:51
 */

namespace App\Traits\User;

use App\Collection\ProductCollection;
use App\Models\Content;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait AssetTrait
{
    use FavoredTrait;

    protected $is_content_released_cache;

    /**  Determines whether user has this content or not
     *
     */
    public function hasContent(Content $content): bool
    {
        return count(array_intersect($this->getUserProductsId(), $this->getContentProductsId($content))) > 0;
    }

    public function getUserProductsId(): array
    {
        return $this->products()
            ->pluck('id')
            ->toArray();
    }

    public function products(): ProductCollection
    {
        $key = 'userProducts:'.$this->cacheKey();

        return Cache::tags(['userAsset', 'userAsset_'.$this->id])
            ->remember($key, config('constants.CACHE_60'), function () {
                $result = $this->productsQuery()->get();

                return Product::hydrate($result->toArray());
            });
    }

    public function productsQuery()
    {
        $result = DB::table('products')
            ->join('orderproducts', function ($join) {
                $join->on('products.id', '=', 'orderproducts.product_id')
                    ->whereNull('orderproducts.deleted_at')
                    ->whereNull('orderproducts.expire_at');
            })
            ->join('orders', function ($join) {
                $join->on('orders.id', '=', 'orderproducts.order_id')
                    ->whereIn('orders.orderstatus_id', [
                        config('constants.ORDER_STATUS_CLOSED'),
                        config('constants.ORDER_STATUS_POSTED'),
                        config('constants.ORDER_STATUS_READY_TO_POST'),
                    ])
                    ->whereIn('orders.paymentstatus_id', [
                        config('constants.PAYMENT_STATUS_PAID'),
                        config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
                        config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'),
                        config('constants.PAYMENT_STATUS_INDEBTED'),
                    ])
                    ->whereNull('orders.deleted_at');
            })
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select([
                'products.*', 'orders.completed_at',
            ])
            ->where('users.id', '=', $this->getKey())
            ->whereNotIn('products.id',
                [Product::DONATE_PRODUCT_5_HEZAR, Product::CUSTOM_DONATE_PRODUCT, Product::ASIATECH_PRODUCT])
            ->whereNull('products.deleted_at')
            ->distinct();

        return $result;
    }

    private function getContentProductsId(Content $content): array
    {
        return $content->allProducts()
            ->pluck('id')
            ->toArray();
    }

    public function productWithParams($request)
    {
        $page = $request->has('page') ? $request->query('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 15;
        $category = $request->has('category') ? $request->get('category') : 'all';
        $sortByOrderCompleted_at = $request->has('sort_by_order_completed_at') ? $request->get('sort_by_order_completed_at') : 'asc';
        $key = 'userProducts:'.$this->cacheKey().':'.$page.':'.$limit.':'.$category.':'.$sortByOrderCompleted_at;
        if ($request->has('title')) {
            $eloquent_builder = new Builder($this->productsQuery());
            $eloquent_builder->setModel(new Product());
            $eloquent_builder
                ->where('products.name', 'LIKE', "%{$request->get('title')}%")
                ->orderBy('orders.completed_at', $sortByOrderCompleted_at);

            if ($category != 'all') {
                $eloquent_builder->where('products.category', $category);
            }

            return $eloquent_builder->paginate($limit)->withQueryString();
        }

        return Cache::tags(['userAsset', 'userAsset_'.$this->id])
            ->remember($key, config('constants.CACHE_60'),
                function () use ($limit, $sortByOrderCompleted_at, $category) {
                    $eloquent_builder = new Builder($this->productsQuery());
                    $eloquent_builder->setModel(new Product());
                    if ($category != 'all') {
                        $eloquent_builder->where('products.category', $category);
                    }

                    return $eloquent_builder->orderBy('orders.completed_at',
                        $sortByOrderCompleted_at)->paginate($limit)->withQueryString();
                });
    }

    public function userHasAnyOfTheseProducts(array $products): bool
    {
        return count(array_intersect($this->getUserProductsId2(), $products));
    }

    public function getUserProductsId2(): array
    {
        return $this->products2()
            ->pluck('id')
            ->toArray();
    }

    public function products2(): ProductCollection
    {//only if he has paid for it
        $key = 'userProducts2:'.$this->cacheKey();

        return Cache::tags(['userAsset', 'userAsset_'.$this->id])
            ->remember($key, config('constants.CACHE_60'), function () {
                $result = DB::table('products')
                    ->join('orderproducts', function ($join) {
                        $join->on('products.id', '=', 'orderproducts.product_id')
                            ->whereNull('orderproducts.deleted_at')
                            ->whereNull('orderproducts.expire_at');
                    })
                    ->join('orders', function ($join) {
                        $join->on('orders.id', '=', 'orderproducts.order_id')
                            ->whereIn('orders.orderstatus_id', [
                                config('constants.ORDER_STATUS_CLOSED'),
                                config('constants.ORDER_STATUS_POSTED'),
                                config('constants.ORDER_STATUS_READY_TO_POST'),
                            ])
                            ->whereIn('orders.paymentstatus_id', [
                                config('constants.PAYMENT_STATUS_PAID'),
                                config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'),
                            ])
                            ->whereNull('orders.deleted_at');
                    })
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select([
                        'products.*', 'orders.completed_at',
                    ])
                    ->where('users.id', '=', $this->getKey())
                    ->whereNotIn('products.id',
                        [Product::DONATE_PRODUCT_5_HEZAR, Product::CUSTOM_DONATE_PRODUCT, Product::ASIATECH_PRODUCT])
                    ->whereNull('products.deleted_at')
                    ->distinct()
                    ->get();

                return Product::hydrate($result->toArray());
            });
    }

    public function userHasAnyOfTheseProducts2(array $products): bool
    {
        // In method ro dar nahayate khastegi zadam ke kar rah biofte . foroosh azmoon ha ro bayad baz konam va farda beram mosaferat . bayad refactor konam
        return count(array_intersect($this->getUserProductsId(), $products));
    }

    public function canSeeContent(Content $content): bool
    {
        //ToDo : Does not work !!!
        //        return $this->can(config('constants.WATCH_ALAA_CONTENTS')) || $this->isContentReleased($content);
        return $this->isContentReleased($content);
        //        return $this->hasAnyRole() || $this->hasContent($content);
    }

    public function isContentReleased(Content $content)
    {
        /*if(!is_null($this->is_content_released_cache)){
            return $this->is_content_released_cache;
        }*/
        $cache_key = "isContentReleased:user-{$this->id}:content-{$content->id}";

        //        $this->is_content_released_cache =
        return Cache::tags([$cache_key, 'userAsset_'.$this->id])->remember($cache_key, config('constants.CACHE_600'),
            function () use ($content) {
                /// get products tha have $content
                $userOrders = $this->filterOrdersByProductsOfContent($content);
                // check user hase no such order
                if ($userOrders->isEmpty()) {
                    return false;
                }
                /// get user orderProducts that product_id is one of $productsOfContent
                $userOrderProducts = collect();
                $userOrders->each(fn ($order) => $userOrderProducts->push($order->orderProducts->whereIn('product_id',
                    $content->productsIdArray())->load('order')));
                $userOrderProducts = $userOrderProducts->flatten();
                if ($userOrderProducts->whereNull('expire_at')->isEmpty()) {
                    return false;
                }
                // check if orderProduct purchased
                if ($userOrders->where('isInInstalment', 0)->isNotEmpty()) {
                    return true;
                }

                if ($userOrders->where('isInInstalment', 0)->isNotEmpty() ||
                    $userOrders->where('isInInstalment', 1)->where('completed_at', '>=',
                        '2022-07-09 00:00:00')->isNotEmpty() ||
                    $userOrders->where('paymentstatus_id',
                        config('constants.PAYMENT_STATUS_PAID'))->where('orderstatus_id',
                            config('constants.ORDER_STATUS_CLOSED'))->isNotEmpty()) {
                    return true;
                }

                if ($userOrders->where('paymentstatus_id',
                    config('constants.PAYMENT_STATUS_PAID'))->where('orderstatus_id',
                        config('constants.ORDER_STATUS_CLOSED'))->isNotEmpty()) {
                    return true;
                }

                // check if $content released by installment
                /** @var Orderproduct $orderProduct */
                foreach ($userOrderProducts as $orderProduct) {
                    if ($orderProduct->hasPaidForContent($content)) {
                        return true;
                    }
                }

                return false;
            });
        //        return $this->is_content_released_cache;
    }

    public function canUserUseCoupon(Coupon $coupon, User $user): bool
    {
        $couponUsers = $coupon->users()->get();

        if ($couponUsers->isEmpty()) {
            return true;
        }

        return $couponUsers->where('id', $user->id)->isNotEmpty();
    }

    public function addProductsToAsset(Collection $products, User $user): ?Order
    {
        $order = OrderRepo::createBasicCompletedOrder($user->id, config('constants.PAYMENT_STATUS_PAID'));

        $orderPrice = 0;
        foreach ($products as $product) {
            $price = $product->price;
            $orderPrice += $price['base'];
            OrderproductRepo::createBasicOrderproduct($order->id, $product->id, $price['base'], $price['base'], 0,
                $product->discount);
        }

        $order->update([
            'cost' => $orderPrice,
            'costwithoutcoupon' => 0,
        ]);

        return $order;
    }

    private function searchProductInUserAssetsCollection(Product $product, ?User $user): bool
    {
        if (is_null($user)) {
            return false;
        }

        $key = 'searchProductInUserAssetsCollection:'.$product->cacheKey().'-'.$user->cacheKey();

        return Cache::tags(['searchInUserAsset', 'searchInUserAsset_'.$user->id, 'userAsset', 'userAsset_'.$user->id])
            ->remember($key, config('constants.CACHE_60'), function () use ($user, $product) {

                $userAssetsArray = $user->getUserProductsId();
                if (empty($userAssetsArray)) {
                    return false;
                }

                return in_array($product->id, $userAssetsArray);
            });
    }

    private function searchProductInUserAssetsCollection2(Product $product, ?User $user): bool
    {//Only if he has paid for it
        if (is_null($user)) {
            return false;
        }

        $key = 'searchProductInUserAssetsCollection2:'.$product->cacheKey().'-'.$user->cacheKey();

        return Cache::tags(['searchInUserAsset', 'searchInUserAsset_'.$user->id, 'userAsset', 'userAsset_'.$user->id])
            ->remember($key, config('constants.CACHE_60'), function () use ($user, $product) {

                $userAssetsArray = $user->getUserProductsId2();
                if (empty($userAssetsArray)) {
                    return false;
                }

                return in_array($product->id, $userAssetsArray);
            });
    }

    private function searchProductTreeInUserAssetsCollection(Product $product, ?User $user): array
    {
        $purchasedProductIdArray = [];

        if (is_null($user)) {
            return $purchasedProductIdArray;
        }

        $key = 'searchProductTreeInUserAssetsCollection:'.$product->cacheKey().'-'.$user->cacheKey();

        return Cache::tags(['searchInUserAsset', 'searchInUserAsset_'.$user->id, 'userAsset', 'userAsset_'.$user->id])
            ->remember($key, config('constants.CACHE_60'), function () use ($user, $product, $purchasedProductIdArray) {

                $userAssetsArray = $user->getUserProductsId();
                if (empty($userAssetsArray)) {
                    return $userAssetsArray;
                }

                $this->iterateProductAndChildrenInAsset($userAssetsArray, $product, $purchasedProductIdArray);

                return $purchasedProductIdArray;
            });
    }

    private function iterateProductAndChildrenInAsset(
        array $userAssetsArray,
        Product $product,
        array &$purchasedProductIdArray
    ): void {
        if (in_array($product->id, $userAssetsArray)) {
            $purchasedProductIdArray[] = $product->id;
            $purchasedProductIdArray = array_merge($purchasedProductIdArray,
                $product->getAllChildren()->pluck('id')->toArray());
        } else {
            $grandChildren = $product->getAllChildren();
            $hasBoughtEveryChild = $grandChildren->isEmpty() ? false : true;
            foreach ($grandChildren as $grandChild) {
                if (! in_array($grandChild->id, $userAssetsArray)) {
                    $hasBoughtEveryChild = false;
                    break;
                }
            }

            if ($hasBoughtEveryChild) {
                $purchasedProductIdArray[] = $product->id;
            }
        }

        $children = $product->children;
        if ($children->count() > 0) {
            foreach ($children as $key => $childProduct) {
                $this->iterateProductAndChildrenInAsset($userAssetsArray, $childProduct, $purchasedProductIdArray);
            }
        }
    }
}
