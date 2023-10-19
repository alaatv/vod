<?php

namespace App\Models;

use App\Collection\BlockCollection;
use App\Collection\ContentCollection;
use App\Collection\ProductCollection;
use App\Collection\SetCollection;
use App\Repositories\Loging\ActivityLogRepo;
use App\Repositories\SlideshowRepo;
use App\Traits\logger;
use App\Traits\Scopes\AccessorsAndMutators\Block\Accessors;
use App\Traits\Scopes\AccessorsAndMutators\Block\Mutators;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Block extends BaseModel
{
    use HasFactory;
    use logger;
    use Accessors;
    use Mutators;

    protected const LOG_ATTRIBUTES = ['title', 'enable', 'type', 'customUrl'];

    public const SET_SHOW_TOP_OF_LIST_MOBILE_SHOW_BLOCK_ID = 217;
    public const SET_SHOW_TOP_OF_LIST_DESKTOP_SHOW_BLOCK_ID = 218;
    public const CONTENT_SHOW_PAGE_LEFT_SIDE_1_BLOCK_ID = 219;
    public const CONTENT_SHOW_PAGE_RIGHT_SIDE_0_BLOCK_ID = 220;

    public const TYPES = [
        [
            'value' => '1',
            'name' => 'صفحه اصلی',
        ],
        [
            'value' => '2',
            'name' => 'فروشگاه',
        ],
        [
            'value' => '3',
            'name' => 'صفحه محصول',
        ],
    ];

    public const INDEX_PAGE_NAME = 'blockPage';

    protected const ACTION_LOOKUP_TABLE = [
        '1' => 'Web\ContentController@index',
        '2' => 'Web\ProductController@index',
        '3' => null,
    ];

    protected const ACTION_LOOKUP_TABLE_V2 = [
        '1' => 'Api\SearchController@index',
        '2' => 'Web\ProductController@index',
        '3' => null,
    ];

    protected $isOfferBlock = false;

    protected $cascadeDeletes = [
        'channels',
        'sets',
        'products',
        'contents',
        'banners',
    ];

    protected $fillable = [
        'title',
        'tags',
        'class',
        'order',
        'enable',
        'type',
        'customUrl',
    ];
    protected $with = [
        'sets',
        'products',
        'contents',
        'banners',
    ];
    protected $appends = [
        'url',
        'offer',
        'edit_link',
        'remove_link',
    ];

    protected $hidden = [
        'enable',
        'tags',
        'created_at',
        'class',
        'deleted_at',
        //        'type',
    ];
    protected $casts = [
        'tags' => 'array',
    ];
    /**
     * @var array|mixed
     */
    private $active_banners_cache;

    public static function getShopBlocksForWeb(): ?BlockCollection
    {
        return Cache::tags(['block', 'shop'])
            ->remember('block:getShopBlocksForWeb', config('constants.CACHE_600'), function () {
//                $offerBlock = self::getOfferBlock();
                return self::shopWithSlide()
                    ->enable()
//                    ->where('id', '<>', 115)
                    ->orderBy('order')
                    ->get()
                    ->loadMissing([
                        'contents',
                        'sets',
                        'products',
                        'banners',
                    ]);

//                return $blocks->prepend($offerBlock);
            });
    }

    /**
     * For API V1
     *
     * @return BlockCollection|null
     */
    public static function getShopBlocksForAppV1(): ?BlockCollection
    {
        return Cache::tags(['block', 'shop'])
            ->remember('block:getShopBlocksForAppV1', config('constants.CACHE_600'), function () {
                $offerBlock = self::getOfferBlock();
                $blocks = self::shopWithoutSlide()
                    ->enable()
//                    ->where('id', '<>', 113)
                    ->orderBy('order')
                    ->get()
                    ->loadMissing([
                        'contents',
                        'sets',
                        'products',
                        'banners',
                    ]);

                return $blocks->prepend($offerBlock);
            });
    }

    /**
     * @return Block
     */
    protected static function getOfferBlock(): Block
    {
        return self::getDummyBlock(true, 'الماس و جزوات', Product::getProductsHaveBestOffer());
    }

    public static function getDummyBlock(
        bool $offer,
        string $title,
        ProductCollection $products = null,
        SetCollection $sets = null,
        ContentCollection $contents = null,
        Collection $banners = null
    ) {
        //TODO:// add Cache Layer!
        $block = new Block();
        $block->id = 0;
        $block->offer = $offer;
//        $block->type  = 3;
        $block->order = 0;
        $block->title = $title;

        return $block->addProducts($products)
            ->addContents($contents)
            ->addSets($sets)
            ->addBanners($banners);
    }

    protected function addBanners($banners)
    {
        if ($banners != null) {
            foreach ($banners as $banner) {
                $this->banners->add($banner);
            }
        }

        return $this;
    }

    protected function addSets($sets)
    {
        if ($sets != null) {
            foreach ($sets as $set) {
                $this->sets->add($set);
            }
        }

        return $this;
    }

    protected function addContents($contents)
    {
        if ($contents != null) {
            foreach ($contents as $content) {
                $this->contents->add($content);
            }
        }

        return $this;
    }

    protected function addProducts($products)
    {
        if ($products != null) {
            foreach ($products as $product) {
                $this->products->add($product);
            }
        }

        return $this;
    }

    /**
     * For API V2
     *
     * @return mixed
     */
    public static function getShopBlocksForAppV2()
    {
        return Cache::tags(['block', 'shop'])
            ->remember('block:getShopBlocksForAppV2', config('constants.CACHE_600'), function () {
                return self::shopWithSlide()
                    ->enable()
//                    ->where('id', '<>', 113)
                    ->orderBy('order')
                    ->paginate(20);
            });
    }

    public static function getMainBlocksForAppV2(): ?LengthAwarePaginator
    {
        return Cache::tags(['block', 'home'])
            ->remember('block:getMainBlocksForAppV2', config('constants.CACHE_600'), function () {
                return self::mainWithSlide()
                    ->enable()
                    ->orderBy('order')
                    ->paginate(20);
            });
    }

    public static function getMainBlocksForWeb(): ?BlockCollection
    {
        $blocks = Cache::tags(['block', 'home'])
            ->remember('block:getMainBlocksForWeb', config('constants.CACHE_600'), function () {
                $blocks = self::mainWithoutSlide()
                    ->enable()
                    ->where('id', '<>', 124)
                    ->orderBy('order')
                    ->get()
                    ->loadMissing([
                        'contents',
                        'sets',
                        'products',
                        'banners',
                    ]);
                return $blocks->map(function (Block $block) {
                    return $block->attacheCachedMethodResult();
                });
            });
        return $blocks;
    }

    public function attacheCachedMethodResult()
    {
        $result = parent::attacheCachedMethodResult();
        $result->sets->each(function (Contentset $s) {
            $s->attacheCachedMethodResult();
        });
        $result->products->each(function (Product $p) {
            $p->attacheCachedMethodResult();
        });
        $result->contents->each(function (Content $c) {
            $c->attacheCachedMethodResult();
        });
        $result->banners->each(function (Slideshow $slideshow) {
            $slideshow->attacheCachedMethodResult();
        });
        return $result;
    }

    public static function getMainBlocksForAppV1(): ?BlockCollection
    {
        return Cache::tags(['block', 'home'])
            ->remember('block:getMainBlocksForAppV1', config('constants.CACHE_600'), function () {
                $blocks = self::mainWithSlide()
                    ->enable()
                    ->where('id', '<>', 124)
                    ->orderBy('order')
                    ->get()
                    ->loadMissing([
                        'contents',
                        'sets',
                        'products',
                        'banners',
                    ]);
                return $blocks->map(function (Block $block) {
                    return $block->attacheCachedMethodResult();
                });
            });
    }

    public static function getContentBlocks(): ?BlockCollection
    {
        $blocks = Cache::tags(['block', 'content'])
            ->remember('block:getContentBlocks', config('constants.CACHE_600'), function () {
                /** @var BlockCollection $blocks */
                $blocks = Block::whereIn('id', [163])
                    ->get()
                    ->loadMissing([
                        'contents',
                        'sets',
                        'products',
                        'banners',
                    ]);
                return $blocks->map(function (Block $block) {
                    return $block->attacheCachedMethodResult();
                });
            });
        return $blocks;
    }

    public static function calculateExpireTimeForCachingSlides($block, $activeSlideshows)
    {
        $littleValidUntil = $activeSlideshows->pluck('validUntil')->min();
        $baseTime = $littleValidUntil;
        $now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now('Asia/Tehran'));
        $littleValidSince = SlideshowRepo::futureSlide($block, $now)->pluck('validSince')->min();

        if (is_null($baseTime) && is_null($littleValidSince)) {
            return config('constants.CACHE_600');
        }

        if (is_null($baseTime)) {
            return $now->diffInSeconds($littleValidSince);
        }

        if (is_null($littleValidSince)) {
            return $now->diffInSeconds($baseTime);
        }

        return $now->diffInSeconds(min($baseTime, $littleValidSince));

    }

    /**
     * Scope a query to only blocks for shop.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeShopWithoutSlide($query)
    {
        return $query->where('type', 2);
    }

    /**
     * Scope a query to only blocks for shop.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeShopWithSlide($query)
    {
        // TODO: I saw a number of scope models that used the orWhere phrase incorrectly. Please check.
//        return $query->where('type', 5)->orWhere('type', 2);
        return $query->where(function ($q) {
            return $q->where('type', 5)
                ->orWhere('type', 2);
        });
    }

    /**
     * Scope a query to only blocks for HomePage.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeMainWithoutSlide($query)
    {
        return $query->where('type', 1);
    }

    /**
     * Scope a query to only blocks for HomePage.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeMainWithSlide($query)
    {
        return $query->where('type', 4)->orWhere('type', 1);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     *
     * @return BlockCollection
     */
    public function newCollection(array $models = [])
    {
        return new BlockCollection($models);
    }

    /**
     * Scope a query to only include enable Blocks.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeEnable($query)
    {
        return $query->where('enable', 1);
    }

    /**
     * Scope a query to only include enable Blocks.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeDisable($query)
    {
        return $query->where('enable', 0);
    }

    /**
     * Scope a query to only include active Contents.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->enable();
    }

    public function notRedirectedContents()
    {
        return $this->contents()->active()->notRedirected();
    }

    public function contents()
    {
        return $this->allContents()->active()->notRedirected();
    }

    public function allContents()
    {
        return $this->morphedByMany(Content::class, 'blockable')
            ->withTimestamps()
            ->withPivot(['order'])
            ->orderBy('blockables.order');
    }

    public function setsForAdmin()
    {
        return $this->allSets()->notRedirected();
    }

    public function allSets()
    {
        return $this->morphedByMany(Contentset::class, 'blockable')
            ->withTimestamps()
            ->withPivot(['order'])
            ->orderBy('blockables.order');
    }

    public function blockType()
    {
        return $this->belongsTo(BlockType::class, 'type');
    }

    public function getActiveContent(): ContentCollection
    {
        return Cache::tags([
            'block', 'block_'.$this->id, 'block_'.$this->id.'_activeContents'
        ])->remember('block:activeContent:'.$this->cacheKey(), config('constants.CACHE_60'), function () {
            return $this->contents()
                ->get()->sortBy('pivot.order');
        });
    }

    public function getActiveSets(): SetCollection
    {
        return Cache::tags([
            'block', 'block_'.$this->id, 'block_'.$this->id.'_activeSets'
        ])->remember('block:activeSets:'.$this->cacheKey(), config('constants.CACHE_60'), function () {
            return $this->sets()
                ->active()
                ->get()->sortBy('pivot.order');
        });
    }

    public function sets()
    {
        return $this->allSets()->active()->notRedirected();
    }

    public function channels()
    {
        return $this->belongsToMany(Channel::class);
    }

    public function updateBlockableOrder(
        $blockableOrders = [],
        array $relations = ['products', 'sets', 'contents', 'banners']
    ) {

        if (empty($blockableOrders)) {
            return null;
        }
        foreach ($relations as $relationship) {
            $this->blockable($relationship)->withPivot('order');

            foreach ($blockableOrders as $key => $order) {
                $this->blockable($relationship)->updateExistingPivot($key, ['order' => $order]);
            }
            resolve(ActivityLogRepo::class)->logChanges($this, newOrder: $blockableOrders,
                lastOrder: $this->{$relationship}, relation: $relationship);
        }
    }

    public function blockable($blockableType)
    {
        return call_user_func([$this, $blockableType]);
    }

    public function attachProducts(array $productsId, bool $shouldLog = false)
    {
        if ($shouldLog) {
            $current_items = $this->products->pluck('id')->toArray();
            resolve(ActivityLogRepo::class)->logChanges($this, $productsId, $current_items, 'products');
        }
        $this->products()->sync($productsId);
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'blockable')
            ->withTimestamps()
            ->enable()
            ->active()
            ->withPivot(['order'])
            ->orderBy('blockables.order');
    }

    public function attachSets(array $setsId, bool $shouldLog = false): void
    {
        if ($shouldLog) {
            $current_items = $this->sets->pluck('id')->toArray();
            resolve(ActivityLogRepo::class)->logChanges($this, $setsId, $current_items, 'sets');
        }
        $this->sets()->sync($setsId);
    }

    public function attachContents(array $contentsId, bool $shouldLog = false): void
    {
        if ($shouldLog) {
            $current_items = $this->contents->pluck('id')->toArray();
            resolve(ActivityLogRepo::class)->logChanges($this, $contentsId, $current_items, 'contents');
        }
        $this->contents()->sync($contentsId);
    }

    public function attachBanners(array $bannerIds, bool $shouldLog = false): void
    {
        if ($shouldLog) {
            $current_items = $this->banners->pluck('id')->toArray();
            resolve(ActivityLogRepo::class)->logChanges($this, $bannerIds, $current_items, 'banners');
        }
        $this->banners()->sync($bannerIds);
    }

    public function banners()
    {
        return $this->morphedByMany(Slideshow::class, 'blockable')
            ->withTimestamps()
            ->withPivot(['order'])
            ->orderBy('blockables.order');
    }

    public function blockables()
    {
        return $this->hasMany(Blockable::class)->where('deleted_at', null)->orderBy('order');
    }

    private function makeUrl($action, $input = null)
    {
        if (!$input) {
            return str_replace('https://192.168.11.4:8080', 'https://alaatv.com', urldecode(action($action)));
        }
        return str_replace('https://192.168.11.4:8080', 'https://alaatv.com',
            urldecode(action($action, ['tags' => $input])));
    }
}
