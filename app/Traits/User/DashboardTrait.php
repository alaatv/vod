<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-02-15
 * Time: 15:46
 */

namespace App\Traits\User;


use App\Collection\BlockCollection;
use App\Repositories\SubscriptionRepo;
use stdClass;

trait DashboardTrait
{
    use AssetTrait;

    /**
     *
     * @return BlockCollection|null
     */
    public function getDashboardBlocks(): ?BlockCollection
    {
        $result = new BlockCollection();

        $blocks = [
            'products' => $this->makeBlockForUserProducts(),
            'favored' => $this->makeBlockForUserFavored(),
        ];
        foreach ($blocks as $block) {
            if (isset($block)) {
                $result->add($block);
            }
        }

        return $result;
    }

    /**
     * @return Block
     */
    private function makeBlockForUserProducts($withSubscriptions = false): ?Block
    {
        $products =
            $this->products()->whereNotIn('id', Product::getPackageProducts()->pluck('id')->toArray())->whereNotIn('id',
                Product::USER_PRODUCTS_PANEL_EXCLUDE_PRODUCTS)->unique();
        $products->addSorting();

        if ($withSubscriptions) {
            foreach (Product::TIMEPOINT_SUBSCRIPTON_PRODUCTS as $subscriptionProduct) {
                $subscription = SubscriptionRepo::validProductSubscriptionOfUser($this->id, [$subscriptionProduct]);
                if (!isset($subscription)) {
                    continue;
                }

                $expirationDateTime = $subscription->valid_until;
                $subscription = new stdClass();
                $subscription->expire_at = $expirationDateTime;
                break;
            }
        }


        if ($products->count() <= 0 && !isset($expirationDateTime)) {
            return null;
        }

        $block = Block::getDummyBlock(false, trans('profile.My Products'), ($products->count() > 0) ? $products : null);

        $block->subscriptions = (isset($subscription)) ? [$subscription] : null;

        return $block;
    }

    /**
     * @return Block
     */
    private function makeBlockForUserFavored(): ?Block
    {
        [
            $contentBlocks,
            $setBlocks,
            $productBlocks,
        ] = [
            $this->getTotalActiveFavoredContents(),
            $this->getActiveFavoredSets(),
            $this->getActiveFavoredProducts(),
        ];

        $favored = [
            'content' => $contentBlocks,
            'set' => $setBlocks,
            'product' => $productBlocks,
        ];

        if ($favored['product']->count() > 0 || $favored['set']->count() > 0 || $favored['content']->count() > 0) {
            return Block::getDummyBlock(false, trans('profile.Favored'), $favored['product'], $favored['set'],
                $favored['content']);
        }

        return null;
    }

    /**
     * child has worker task
     * @return BlockCollection|null
     */
    public function getDashboardBlocksForApp(): ?BlockCollection
    {
        $result = new BlockCollection();
        $blocks = [
            // child has worker task
            'products' => $this->makeBlockForUserProducts(true),
            'favored' => null,
        ];
        foreach ($blocks as $block) {
            if (isset($block)) {
                $result->add($block);
            }
        }

        return $result;
    }
}
