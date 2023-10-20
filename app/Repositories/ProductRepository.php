<?php


namespace App\Repositories;


use App\Collection\ProductCollection;
use App\Models\Product;
use App\Traits\ProductCommon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRepository extends AlaaRepo
{
    use ProductCommon;

    public static function getModelClass(): string
    {
        return Product::class;
    }

    /**
     * @param  array  $setIds
     *
     * @return Product|Builder
     */
    public static function getProductsByUserId(array $setIds): Builder
    {
        return self::initiateQuery()->whereHas('sets', function ($q) use ($setIds) {
            $q->whereIn('contentset_id', $setIds)
                ->whereNotNull('grand_id');
        });
    }

    /**
     * @param  string  $teacherName
     *
     * @return Builder
     */
    public static function getProductByTag(string $teacherName): Builder
    {
        return self::initiateQuery()->where('tags', 'like', '%'.$teacherName.'%');
    }

    public static function getUnPurchasableProducts()
    {
        return [Product::DONATE_PRODUCT_5_HEZAR, Product::CUSTOM_DONATE_PRODUCT, Product::ASIATECH_PRODUCT];
    }

    public static function getDonateProducts()
    {
        return [Product::DONATE_PRODUCT_5_HEZAR, Product::CUSTOM_DONATE_PRODUCT];
    }

    public static function getProductsById(array $products): Builder
    {
        return self::initiateQuery()->whereIn('id', $products);
    }

    public static function excludeProductsById(array $products): Builder
    {
        return self::initiateQuery()->whereNotIn('id', $products);
    }

    public static function all(): Builder
    {
        return self::initiateQuery();
    }

    public static function isYaldaProductActive()
    {
        return Cache::tags([
            'products', 'yalda_product', 'product_'.Product::YALDA_SUBSCRIPTION
        ])->remember('product_'.Product::YALDA_SUBSCRIPTION, config('constants.CACHE_60'), function () {
            return Product::find(Product::YALDA_SUBSCRIPTION)?->exists();
        });
    }

    public static function getShowDiscount($productId)
    {
        $key = 'Product:'.$productId;

        /** @var Product $product */
        $product = Cache::tags([
            'product',
            'product_'.$productId,
        ])->remember($key, config('constants.CACHE_5'), function () use ($productId) {
            return Product::where('id', $productId)->first();
        });
        return $product->showDiscount;
    }

    /**
     * @param $fileName
     *
     * @return ProductCollection
     */
    public static function getProductsThatHaveValidProductFileByFileNameRecursively(string $fileName)
    {
        $key = "product_$fileName";
        return Cache::tags(['product_valid_file', $key])->remember($key, config('constants.CACHE_600'),
            function () use ($fileName) {
                $products = Product::whereIn('id',
                    self::getArrayOfProductsIdThatHaveValidProductfileByFileName($fileName))
                    ->OrwhereIn('id',
                        self::getArrayOfProductsIdThatTheirParentHaveValidProductFileByFileName($fileName))
                    ->OrwhereIn('id',
                        self::getArrayOfProductsIdThatTheirComplimentaryHaveValidProductFileByFileName($fileName))
                    ->OrwhereIn('id', self::getArrayOfProductsIdThatTheirGiftHaveValidProductFileByFileName($fileName))
                    ->OrwhereIn('id',
                        self::getArrayOfProductsIdThatTheirParentComplimentaryHaveValidProductFileByFileName($fileName))
                    ->get();

                return self::getTotalProducts($products);
            });
    }

    /**
     * @param $fileName
     *
     * @return Collection
     */
    public static function getArrayOfProductsIdThatHaveValidProductFileByFileName($fileName): Collection
    {
        return Product::whereHas('validProductfiles', function ($query) use ($fileName) {
            $query->where('file', $fileName);
        })
            ->get()
            ->pluck('id');
    }

    /**
     * @param $fileName
     *
     * @return Collection
     */
    public static function getArrayOfProductsIdThatTheirParentHaveValidProductFileByFileName($fileName): Collection
    {
        return Product::whereHas('parents', function ($q) use ($fileName) {
            $q->whereHas('validProductfiles', function ($q) use ($fileName) {
                $q->where('file', $fileName);
            });
        })
            ->get()
            ->pluck('id');
    }

    /**
     * @param $fileName
     *
     * @return Collection
     */
    public static function getArrayOfProductsIdThatTheirComplimentaryHaveValidProductFileByFileName($fileName
    ): Collection {
        return Product::whereHas('complimentaryproducts', function ($q) use ($fileName) {
            $q->whereHas('validProductfiles', function ($q) use ($fileName) {
                $q->where('file', $fileName);
            });
        })
            ->get()
            ->pluck('id');
    }

    /**
     * @param $fileName
     *
     * @return Collection
     */
    public static function getArrayOfProductsIdThatTheirGiftHaveValidProductFileByFileName($fileName): Collection
    {
        return Product::whereHas('gifts', function ($q) use ($fileName) {
            $q->whereHas('validProductfiles', function ($q) use ($fileName) {
                $q->where('file', $fileName);
            });
        })
            ->get()
            ->pluck('id');
    }

    /**
     * @param $fileName
     *
     * @return Collection
     */
    public static function getArrayOfProductsIdThatTheirParentComplimentaryHaveValidProductFileByFileName($fileName
    ): Collection {
        return Product::whereHas('parents', function ($q) use ($fileName) {
            $q->whereHas('complimentaryproducts', function ($q) use ($fileName) {
                $q->whereHas('validProductfiles', function ($q) use ($fileName) {
                    $q->where('file', $fileName);
                });
            });
        })
            ->get()
            ->pluck('id');
    }

    /**
     * @param  Collection  $products
     *
     * @return ProductCollection
     */
    private static function getTotalProducts(Collection $products)
    {
        $totalProducts = new ProductCollection();
        /** @var Product $product */
        foreach ($products as $product) {
            $productChain = $product->getProductChain();

            $totalProducts = $totalProducts->merge($productChain);
        }
        $totalProducts = $totalProducts->merge($products);

        return $totalProducts;
    }

    public static function _3aExamsProductsIds()
    {
        return Product::query()->whereHas('exams');
    }

    public static function interrelationProducts()
    {
        return Product::whereHas('productProduct', function ($q) {
            $q->whereIn('relationtype_id', [
                config('constants.PRODUCT_INTERRELATION_GIFT'),
                config('constants.PRODUCT_INTERRELATION_ITEM')
            ]);
        })->with('productProduct', function ($q) {
            $q->whereIn('relationtype_id', [
                config('constants.PRODUCT_INTERRELATION_GIFT'),
                config('constants.PRODUCT_INTERRELATION_ITEM')
            ]);
        })->get();
    }
}
