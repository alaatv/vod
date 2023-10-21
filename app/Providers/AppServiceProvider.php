<?php

namespace App\Providers;

use App\{Adapter\AlaaSftpAdapter,
    Classes\AlaaRedisStore,
    Classes\AuthorizationService\AuthorizationServiceInterface,
    Classes\AuthorizationService\SeaAuthorizationService,
    Classes\Search\ContentSearch,
    Classes\Search\ContentsetSearch,
    Classes\Search\ProductSearch,
    Classes\Search\SearchStrategy\AlaaSearch,
    Http\Resources\ReferralCodeInfoWithPrice,
    Models\Block,
    Models\Comment,
    Models\Content,
    Models\Contentset,
    Models\Coupon,
    Models\Employeetimesheet,
    Models\LiveDescription,
    Models\MapDetail,
    Models\Order,
    Models\Orderfile,
    Models\Ordermanagercomment,
    Models\Orderpostinginfo,
    Models\Orderproduct,
    Models\Plan,
    Models\Product,
    Models\ReferralRequest,
    Models\Slideshow,
    Models\Source,
    Models\Ticket,
    Models\TicketMessage,
    Models\Transaction,
    Models\User,
    Models\WatchHistory,
    Observers\BlockObserver,
    Observers\CommentObserver,
    Observers\ContentObserver,
    Observers\CouponObserver,
    Observers\EmployeetimesheetObserver,
    Observers\LiveDescriptionObserver,
    Observers\MapDetailObserver,
    Observers\OrderFileObserver,
    Observers\OrderManagerCommentObserver,
    Observers\OrderObserver,
    Observers\OrderPostingInfoObserver,
    Observers\OrderproductObserver,
    Observers\PlanObserver,
    Observers\ProductObserver,
    Observers\ReferralRequestObserver,
    Observers\SetObserver,
    Observers\SlideshowObserver,
    Observers\SourceObserver,
    Observers\TicketMessageObserver,
    Observers\TicketObserver,
    Observers\TransactionObserver,
    Observers\UserObserver,
    Observers\WatchHistoryObserver,
    Repositories\AuthorizationRepository\_3aAuthorizationRepo,
    Repositories\AuthorizationRepository\AuthorizationRepoInterface,
    Repositories\Loging\ActivityLogRepo,
    Repositories\SmsDetailsRepository,
    Traits\RegionCommon,
    Traits\UserCommon};
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Auth, Cache, Schema, Storage, Validator};
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    use UserCommon;
    use RegionCommon;

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(AuthorizationServiceInterface::class, SeaAuthorizationService::class);
        $this->app->bind(AuthorizationRepoInterface::class, _3aAuthorizationRepo::class);
        $this->app->singleton(ActivityLogRepo::class, ActivityLogRepo::class);

        $this->app->bind(AlaaSearch::class, function () {
            $contentSearch = new ContentSearch();
            $setSearch = new ContentsetSearch();
            $productSearch = new ProductSearch();
            return new AlaaSearch($contentSearch, $setSearch, $productSearch);
        });

        $this->app->bind(SmsDetailsRepository::class, function ($app) {
            return new SmsDetailsRepository();
        });
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        Horizon::auth(function ($request) {
            return (Auth::check() && Auth::user()
                    ->hasRole('admin'));
        });
        Schema::defaultStringLength(191);

        Storage::extend('sftp', function ($app, $config) {
            $adapter = new AlaaSftpAdapter($config);
            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });

        //pick columns from collection
        if (!Collection::hasMacro('pick')) {
            Collection::macro('pick', function ($columns) {
                $is_assoc = Arr::isAssoc($columns);
                return $this->map(function ($item) use ($columns, $is_assoc) {
                    $data = [];
                    foreach ($columns as $name => $as) {
                        $data[$as] = $item[$is_assoc ? $name : $as] ?? null;
                    }

                    return $data;
                });
            });
        }


        if (!Collection::hasMacro('pushAt')) {
            Collection::macro('pushAt', function ($key, $item) {
                return $this->put($key, collect($this->get($key))->push($item));
            });
        }
        if (Collection::hasMacro('paginate')) {
            return;
        }
        Collection::macro('paginate',
            function ($perPage = 15, $pageName = 'page', $page = null, $options = []) {
                $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                $total = $this->count();
                $items = $total > 0 ? $this->forPage($page, $perPage) : collect();
                $currentPage = $page;
                $options['path'] = Paginator::resolveCurrentPath();
                $options['pageName'] = $pageName;
                return Container::getInstance()->makeWith(LengthAwarePaginator::class,
                    compact('items', 'total', 'perPage', 'currentPage', 'options'));
            });


    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        added to keep bootstrap default instead of tailwind (https://laravel.com/docs/8.x/upgrade#pagination-defaults)
        Paginator::useBootstrap();
        Blade::withoutComponentTags();
        Content::observe(ContentObserver::class);
        Employeetimesheet::observe(EmployeetimesheetObserver::class);
        Product::observe(ProductObserver::class);
        Contentset::observe(SetObserver::class);
        Orderproduct::observe(OrderproductObserver::class);
        Order::observe(OrderObserver::class);
        Transaction::observe(TransactionObserver::class);
        User::observe(UserObserver::class);
        Slideshow::observe(SlideshowObserver::class);
        Block::observe(BlockObserver::class);
        Coupon::observe(CouponObserver::class);
        Source::observe(SourceObserver::class);
        Ticket::observe(TicketObserver::class);
        TicketMessage::observe(TicketMessageObserver::class);
        MapDetail::observe(MapDetailObserver::class);
        Ordermanagercomment::observe(OrderManagerCommentObserver::class);
        Orderfile::observe(OrderFileObserver::class);
        Orderpostinginfo::observe(OrderPostingInfoObserver::class);
        Comment::observe(CommentObserver::class);
        WatchHistory::observe(WatchHistoryObserver::class);
        $this->defineValidationRules();
        LiveDescription::observe(LiveDescriptionObserver::class);
        Plan::observe(PlanObserver::class);
        ReferralRequest::observe(ReferralRequestObserver::class);
        Cache::extend('alaa-redis', function ($app) {
            return Cache::repository(new AlaaRedisStore(
                $app['redis'],
                $app['config']['cache.prefix'],
                $app['config']['cache.stores.redis.connection']
            ));
        });
        ReferralCodeInfoWithPrice::withoutWrapping();
    }

    private function defineValidationRules(): void
    {
        /**
         *  National code validation for registration form
         */
        Validator::extend('validate', function ($attribute, $value, $parameters, $validator) {
            if (strcmp($parameters[0], 'nationalCode') === 0) {
                return $this->validateNationalCode($value);
            }

            return true;
        });

        Validator::extend('activeProduct', function ($attribute, $value, $parameters, $validator) {
            return Product::findOrFail($value)->active;
        });

        Validator::extend('region_match', function ($attribute, $value, $parameters, $validator) {
            return $this->regionMatch(request($parameters[0]), $value);
        });
    }
}
