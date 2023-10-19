<?php

/*
    These routes are loaded by the RouteServiceProvider within a group which is assigned the "api" middleware group.
*/

use App\Http\Controllers\AndroidLogController;
use App\Http\Controllers\Api\_3AController;
use App\Http\Controllers\Api\AbrishamDashboardPageV2Controller;
use App\Http\Controllers\Api\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Api\Admin\AttributeController;
use App\Http\Controllers\Api\Admin\AttributeSetController;
use App\Http\Controllers\Api\Admin\BlockController as AdminBlockController;
use App\Http\Controllers\Api\Admin\BlockProductsController;
use App\Http\Controllers\Api\Admin\BlockRelationsController;
use App\Http\Controllers\Api\Admin\BlockSetsController;
use App\Http\Controllers\Api\Admin\BlockSlideshowController;
use App\Http\Controllers\Api\Admin\BlockTypesController;
use App\Http\Controllers\Api\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Api\Admin\ContentInComeController;
use App\Http\Controllers\Api\Admin\CouponController;
use App\Http\Controllers\Api\Admin\EmployeeScheduleController as AdminEmployeeScheduleController;
use App\Http\Controllers\Api\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Api\Admin\SetController as AdminSetController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\SlideshowController as AdminSlideshowController;
use App\Http\Controllers\Api\Admin\TransactionController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\VoipController;
use App\Http\Controllers\Api\Admin\VoucherManagementController;
use App\Http\Controllers\Api\AppVersionController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\BlockController;
use App\Http\Controllers\Api\BonyadEhsan\Admin\NotificationController;
use App\Http\Controllers\Api\BonyadEhsan\Admin\OrderController as BonyadEhsanOrderController;
use App\Http\Controllers\Api\BonyadEhsan\Admin\UserController as BonyadEhsanUserController;
use App\Http\Controllers\Api\BookmarkPageV2Controller;
use App\Http\Controllers\Api\BotsController;
use App\Http\Controllers\Api\CacheController;
use App\Http\Controllers\Api\ChannelController as ApiChannelController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\ContentStatusController;
use App\Http\Controllers\Api\DashboardPageController;
use App\Http\Controllers\Api\DashboardPageV2Controller;
use App\Http\Controllers\Api\DonateController;
use App\Http\Controllers\Api\EmployeetimesheetController;
use App\Http\Controllers\Api\EntekhabReshteController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventResultController;
use App\Http\Controllers\Api\EwanoController;
use App\Http\Controllers\Api\ExamResultsController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\FaqPageController;
use App\Http\Controllers\Api\FavorableController;
use App\Http\Controllers\Api\FavorableListController;
use App\Http\Controllers\Api\FirebasetokenController;
use App\Http\Controllers\Api\FormBuilder;
use App\Http\Controllers\Api\ForrestController;
use App\Http\Controllers\Api\GatewayController;
use App\Http\Controllers\Api\GetPaymentRedirectEncryptedLink;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\IndexPageController;
use App\Http\Controllers\Api\LandingPageController;
use App\Http\Controllers\Api\LiveConductorController;
use App\Http\Controllers\Api\LiveDescriptionController;
use App\Http\Controllers\Api\MapDetailController;
use App\Http\Controllers\Api\MobileVerificationController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderproductController;
use App\Http\Controllers\Api\PaymentStatusController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductLandingController;
use App\Http\Controllers\Api\RahAbrishamController;
use App\Http\Controllers\Api\ReceiveSMSController;
use App\Http\Controllers\Api\ReferralCodesController;
use App\Http\Controllers\Api\RulesPageController;
use App\Http\Controllers\Api\SalesManController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SeoController;
use App\Http\Controllers\Api\SetController;
use App\Http\Controllers\Api\ShopPageController;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\StudyEventController;
use App\Http\Controllers\Api\StudyEventReportController;
use App\Http\Controllers\Api\StudyPlanController;
use App\Http\Controllers\Api\SubscriptoinController;
use App\Http\Controllers\Api\TagGroupController;
use App\Http\Controllers\Api\TempOasisAttendantController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketDepartmentController;
use App\Http\Controllers\Api\TicketMessageController;
use App\Http\Controllers\Api\TicketPriorityController;
use App\Http\Controllers\Api\TimepointController;
use App\Http\Controllers\Api\UploadCenterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\WatchHistoryController;
use App\Http\Controllers\Api\WebsiteSettingController;
use App\Http\Controllers\Api\ZarinpalTransactionController;
use App\Http\Controllers\Auth\ApiLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RolePermissionController;

/*
|--------------------------------------------------------------------------
| V1
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'v1'], function () {
    Route::get('lastVersion', [AppVersionController::class, 'show'])->name('api.v1.lastVersion');

    Route::get('debug', [HomeController::class, 'debug'])->name('api.v1.debug');
    Route::get('authTest', [HomeController::class, 'authTest'])->name('api.v1.authTest');

    Route::get('c/{c}', [ContentController::class, 'show'])->name('api.v1.content.show');
    Route::get('product/{product}', [ProductController::class, 'show'])->name('api.v1.product.show');
    Route::get('set/{set}', [SetController::class, 'show'])->name('api.v1.set.show');
    Route::get('set', [SetController::class, 'index'])->name('api.v1.set.index');
    Route::post('getPrice/{product}', [ProductController::class, 'refreshPrice'])->name('api.v1.refreshPrice');

    Route::any('fetchContents', [ContentController::class, 'fetchContents'])->name('api.v1.fetch.content');
    Route::get('shop', '\\'.ShopPageController::class)->name('api.v1.shop');
    Route::get('/home', '\\'.IndexPageController::class)->name('api.v1.home');

    Route::get('satra', [HomeController::class, 'satra']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('donate', [OrderController::class, 'donateOrder'])->name('api.v1.donate');
        Route::any('user/auth2/profile', [UserController::class, 'getAuth2Profile']);
        Route::resource('user', '\\'.UserController::class, ['as' => 'api'])->only(['update', 'show']);
        Route::post('orderproduct', [OrderproductController::class, 'store'])->name('api.v1.orderproduct.store');
        Route::delete('orderproduct/{orderproduct}',
            [OrderproductController::class, 'destroy'])->name('api.v1.orderproduct.destroy');
        Route::post('transaction',
            '\\'.ZarinpalTransactionController::class)->name('api.v1.zarinpal.transaction.store');
        Route::post('orderCoupon', [OrderController::class, 'submitCoupon'])->name('api.v1.coupon.submit');
        Route::delete('orderCoupon', [OrderController::class, 'removeCoupon'])->name('api.v1.coupon.remove');

        Route::group(['prefix' => 'user'], function () {
            Route::get('{user}/orders', [UserController::class, 'userOrders'])->name('api.v1.user.orders');
            Route::get('{user}/dashboard', '\\'.DashboardPageController::class)->name('api.v1.user.dashboard');
            Route::post('{user}/firebasetoken',
                [FirebasetokenController::class, 'store'])->name('api.v1.firebasetoken.store');
        });

        Route::group(['prefix' => 'checkout'], function () {
            Route::get('review', [OrderController::class, 'checkoutReview'])->name('api.v1.checkout.review');
            Route::get('payment', [OrderController::class, 'checkoutPayment'])->name('api.v1.checkout.payment');
        });

        Route::any('getPaymentRedirectEncryptedLink',
            '\\'.GetPaymentRedirectEncryptedLink::class)->name('api.v1.payment.getEncryptedLink');
    });
});

/*
|--------------------------------------------------------------------------
| V2
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'v2'], function () {

    Route::resource('faqs', '\\'.FaqController::class)->only(['index', 'show']);
    Route::resource('block', '\\'.BlockController::class, ['as' => 'api'])->only(['show', 'index']);
    Route::group(['prefix' => 'landing'], function () {
        Route::get('1', [ProductLandingController::class, 'landing1'])->name('api.v2.landing.1');
        Route::get('2', [ProductLandingController::class, 'landing2'])->name('api.v2.landing.2');
        Route::get('3', [ProductLandingController::class, 'landing3'])->name('api.v2.landing.3');
        Route::get('4', [ProductLandingController::class, 'landing4'])->name('api.v2.landing.4');
        Route::get('5', [ProductLandingController::class, 'landing5'])->name('api.v2.landing.5');
        Route::get('6', [ProductLandingController::class, 'landing6'])->name('api.v2.landing.6');
        Route::get('7', [ProductLandingController::class, 'landing7'])->name('api.v2.landing.7');
        Route::get('8', [ProductLandingController::class, 'landing8'])->name('api.v2.landing.8');
        Route::get('9', [ProductLandingController::class, 'landing9'])->name('api.v2.landing.9');
        Route::get('10', [ProductLandingController::class, 'landing10'])->name('api.v2.landing.10');
        Route::get('17', [ProductLandingController::class, 'landing17'])->name('api.v2.landing.17');
        Route::get('13Aban', [LandingPageController::class, 'roozeDaneshAmooz'])->name('api.v2.landing.13Aban');
        Route::get('13aban', [LandingPageController::class, 'roozeDaneshAmooz2'])->name('api.v2.landing.13aban');
    });

    Route::group(['prefix' => 'ewano'], function () {
        Route::get('/', [EwanoController::class, 'root'])->name('ewano.get');
    });

    Route::get('search', [SearchController::class, 'index'])->name('api.v2.search');
    Route::get('lastVersion', [AppVersionController::class, 'showV2']);
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [ApiLoginController::class, 'logout'])->name('api.logout');
    Route::get('authTest', [HomeController::class, 'authTestV2'])->name('api.v2.authTest');

    Route::get('c/{c}', [ContentController::class, 'showV2'])->name('api.v2.content.show');
    Route::get('c/{c}/products', [ContentController::class, 'products'])->name('api.v2.content.products');
    Route::get('product/lives', [ProductController::class, 'lives'])->name('api.v2.product.lives');
    Route::get('product/{product}', [ProductController::class, 'showV2'])->name('api.v2.product.show');
    Route::get('product/{product}/sample', [ProductController::class, 'sampleVideo'])->name('api.v2.product.sample');
    Route::get('product/{product}/faq', [ProductController::class, 'faq'])->name('api.v2.product.faq');
    Route::get('product/{product}/complimentary',
        [ProductController::class, 'complimentary'])->name('api.v2.product.complimentary');
    Route::get('product/{product}/exams', [ProductController::class, 'exams'])->name('api.v2.product.exams');
    Route::get('gift-products/{product}',
        [ProductController::class, 'giftProducts'])->name('api.v2.product.gift-products');
    Route::get('product-categories', [ProductController::class, 'productCategory'])->name('api.v2.product.category');
    Route::get('product', [ProductController::class, 'index'])->name('api.v2.product.index');
    Route::get('set/{set}', [SetController::class, 'showV2'])->name('api.v2.set.show');
    Route::get('set/{set}/contents', [SetController::class, 'contents'])->name('api.v2.set.contents');
    Route::get('set', [SetController::class, 'index'])->name('api.v2.set.index');
    Route::get('content-set/{set}', [SetController::class, 'showWithContents']);
    Route::resource('/forrest/tree', '\\'.ForrestController::class)->only(['index', 'store', 'update']);
    Route::get('/forrest/tree/{grid}', [ForrestController::class, 'show'])->name('api.v2.forrest.tree.show');
    Route::get('/forrest/tags', [ForrestController::class, 'tags'])->name('api.v2.forrest.tree.tags');
    Route::post('getPricgroupIndexe/{product}',
        [ProductController::class, 'refreshPriceV2'])->name('api.v2.refreshPrice');
//    Route::any('fetchContents', [ContentController::class, 'fetchContents'])->name('api.v2.fetch.content');
    Route::get('shop', '\\'.ShopPageController::class)->name('api.v2.shop');
    Route::get('home', '\\'.IndexPageController::class)->name('api.v2.home');
    Route::get('faq', '\\'.FaqPageController::class)->name('api.v2.faq');
    Route::get('contact', '\\'.ContactUsController::class)->name('api.v2.contact');
    Route::get('rule', '\\'.RulesPageController::class)->name('api.v2.rule');
    Route::put('c/updateDuration', [ContentController::class, 'updateDuration'])->name('api.v2.content.updateDuration');
    Route::resource('ticketDepartment', '\\'.TicketDepartmentController::class, ['as' => 'api'])->only(['index']);
    Route::resource('ticketPriority', '\\'.TicketPriorityController::class)->only(['index']);
    Route::resource('plan', '\\'.PlanController::class, ['as' => 'api'])->except(['create', 'edit']);
    Route::resource('newsletter', '\\'.NewsletterController::class, ['as' => 'api'])->only(['store']);
    Route::post('oasis/registerNewsLetter',
        '\\'.TempOasisAttendantController::class)->name('api.v2.oasis.registerNewsLetter');
    Route::get('donate', '\\'.DonateController::class)->name('api.v2.donate');

    Route::group(['prefix' => 'megaroute', 'as' => 'api.v2.'], function () {
        Route::get('getUserFormData', [UserController::class, 'getUserFormData'])->name('user.formData');
    });

    Route::group(['prefix' => 'sms'], function () {
        Route::get('mediana-get-credit', [SmsController::class, 'getCreditForMediana'])->middleware('auth:api');
        Route::get('/receive', '\\'.ReceiveSMSController::class)->name('api.v2.receive.sms');
        Route::post('/sendPattern/{user}', [SmsController::class, 'pattern']);
        Route::post('/sendBulk', [SmsController::class, 'sendBulk'])->middleware('auth:api');
        Route::get('/', [SmsController::class, 'index']);
    });
    Route::get('get-blocks', [BlockController::class, 'block'])->name('api.v2.getBlock');
    Route::group(['prefix' => 'product', 'as' => 'api.v2.'], function () {
        Route::get('{product}/sets', [ProductController::class, 'sets'])->name('api.v2.product.sets');
    });
    Route::group(['middleware' => 'auth:api'], function () {

        Route::group(['prefix' => 'admin', 'as' => 'api.v2.admin.'], function () {
            Route::get('/cache-clear', '\\'.CacheController::class);

            Route::resource('transaction', '\\'.TransactionController::class);

            Route::resource('attribute', '\\'.AttributeController::class);

            Route::resource('attribute-set', '\\'.AttributeSetController::class);

            Route::group(['prefix' => 'faq'], function () {
                Route::get('/', [AdminFaqController::class, 'index'])->name('faq.index');
                Route::post('/', [AdminFaqController::class, 'store'])->name('faq.store');
                Route::put('/{faq}', [AdminFaqController::class, 'update'])->name('faq.update');
                Route::delete('/{faq}', [AdminFaqController::class, 'delete'])->name('faq.delete');
            });

            Route::resource('block', '\\'.AdminBlockController::class)->except(['create', 'edit']);
            Route::get('block/{block}/products', [BlockRelationsController::class, 'products'])->name('block.products');
            Route::post('block/{block}/products/attach',
                [BlockRelationsController::class, 'attachProducts'])->name('block.attachProducts');
            Route::post('block/{block}/products/detach',
                [BlockRelationsController::class, 'detachProducts'])->name('block.detachProducts');
            Route::get('block/{block}/sets', [BlockRelationsController::class, 'sets'])->name('block.sets');
            Route::post('block/{block}/sets/attach',
                [BlockRelationsController::class, 'attachSets'])->name('block.attachSets');
            Route::post('block/{block}/sets/detach',
                [BlockRelationsController::class, 'detachSets'])->name('block.detachSets');
            Route::get('block/{block}/contents', [BlockRelationsController::class, 'contents'])->name('block.contents');
            Route::post('block/{block}/contents/attach',
                [BlockRelationsController::class, 'attachContents'])->name('block.attachContents');
            Route::post('block/{block}/contents/detach',
                [BlockRelationsController::class, 'detachContents'])->name('block.detachContents');
            Route::get('block/{block}/banners', [BlockRelationsController::class, 'banners'])->name('block.banners');
            Route::post('block/{block}/banners/attach',
                [BlockRelationsController::class, 'attachBanners'])->name('block.attachBanners');
            Route::post('block/{block}/banners/detach',
                [BlockRelationsController::class, 'detachBanners'])->name('block.detachBanners');
            Route::patch('/block/{block}/syncProducts',
                [AdminBlockController::class, 'syncProducts'])->name('syncProducts');
            Route::patch('/block/{block}/syncSets', [AdminBlockController::class, 'syncSets'])->name('syncSets');
            Route::patch('/block/{block}/syncBanners',
                [AdminBlockController::class, 'syncBanners'])->name('syncBanners');
            Route::patch('/block/{block}/syncContents',
                [AdminBlockController::class, 'syncContents'])->name('syncContents');
            Route::get('blockSlideShows', [BlockSlideshowController::class, 'index'])->name('blockSlideShow.index');
            Route::get('blockTypes', [BlockTypesController::class, 'index'])->name('blockTypes.index');
            Route::get('blockSets', [BlockSetsController::class, 'index'])->name('blockSets.index');
            Route::get('blockProducts', [BlockProductsController::class, 'index'])->name('blockProducts.index');
            Route::resource('set', '\\'.AdminSetController::class, ['as' => 'api']);
            Route::post('set/{set}/c/attach',
                [AdminSetController::class, 'attachContents'])->name('set.attachContents');
            Route::get('set/{set}/contents', [AdminSetController::class, 'contents'])->name('set.contents');
            Route::put('contents/bulk-update', [ContentController::class, 'bulkUpdate'])->name('content.bulk-update');
            Route::put('contents/bulk-edit-text',
                [ContentController::class, 'bulkEditText'])->name('content.bulk-edit-text');
            Route::put('contents/bulk-edit-tags',
                [ContentController::class, 'bulkEditTags'])->name('content.bulk-edit-tags');
            Route::resource('contents', '\\'.AdminContentController::class, ['as' => 'api'])->only([
                'index', 'show', 'update'
            ]);
            Route::get('c', [AdminContentController::class, 'index'])->name('c.index');
            Route::post('contents/destroy', [AdminContentController::class, 'destroy'])->name('content.bulk.destroy');
            Route::post('content/{content}/copy', [AdminContentController::class, 'copy'])->name('content.copy');
            Route::post('upload/presigned-request',
                [UploadCenterController::class, 'presignedRequest'])->name('upload.presigned-request');
            Route::get('upload', [UploadCenterController::class, 'upload'])->name('upload');
            Route::post('product/set-discount',
                [AdminProductController::class, 'setDiscount'])->name('product.set-discount');
            Route::put('product/bulk-update-statuses',
                [AdminProductController::class, 'bulkUpdateStatuses'])->name('product.bulk-update-statuses');
            Route::resource('product', '\\'.AdminProductController::class, ['as' => 'api'])->except(['create', 'edit']);

            Route::get('product/{product}/sets', [AdminProductController::class, 'sets'])->name('product.sets');
            Route::post('product/{product}/copy', [AdminProductController::class, 'copy'])->name('product.copy');
            Route::resource('user', '\\'.AdminUserController::class, ['as' => 'api'])->except(['create', 'edit']);
            Route::resource('permission', '\\'.AdminPermissionController::class, ['as' => 'api'])->except([
                'create', 'edit'
            ]);
            Route::resource('role', '\\'.AdminRoleController::class, ['as' => 'api'])->except(['create', 'edit']);
            Route::post('orderBatchTransfer',
                [AdminOrderController::class, 'orderBatchTransfer'])->name('order.batchTransfer');
            Route::get('unknownUsersCityIndex',
                [UserController::class, 'unknownUsersCityIndex'])->name('user.index.unknown.city');
            Route::post('employeeSchedule/batchUpdate',
                [AdminEmployeeScheduleController::class, 'batchUpdate'])->name('employeeSchedule.batchUpdate');
            Route::resource('order', '\\'.AdminOrderController::class)->except(['create', 'edit']);
            Route::resource('employeeSchedules', '\\'.AdminEmployeeScheduleController::class)->only(['index', 'store']);
            Route::resource('coupon', '\\'.CouponController::class)->except(['create', 'edit']);
            Route::resource('activityLog', '\\'.AdminActivityLogController::class)->only(['index']);
            Route::resource('slideshow', '\\'.AdminSlideshowController::class)->only(['index']);
            Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
                Route::get('findByCode', [CouponController::class, 'findByCode'])->name('findByCode');
                Route::post('generateMassiveRandomCoupon',
                    [CouponController::class, 'generateMassiveRandomCoupon'])->name('massive.random');
            });
            Route::get('abrisham/productChoice',
                [AdminOrderController::class, 'abrishamProductChoice'])->name('abrisham.productChoice');
            Route::group(['prefix' => 'bonyadEhsan', 'as' => 'bonyadEhsan'], function () {
                Route::get('order', [BonyadEhsanOrderController::class, 'index'])->name('order.index');
                Route::delete('order/{order}', [BonyadEhsanOrderController::class, 'destroy'])->name('order.remove');
                Route::post('user', [BonyadEhsanUserController::class, 'store'])->name('user.store');
                Route::get('user/myInfo',
                    [BonyadEhsanUserController::class, 'showLoginUser'])->name('user.show.myInfo');
                Route::get('user/{user}', [BonyadEhsanUserController::class, 'show'])->name('user.show');
                Route::put('user/{user}', [BonyadEhsanUserController::class, 'update'])->name('user.update');
                Route::post('moshaver',
                    [BonyadEhsanUserController::class, 'storeMoshaver'])->name('user.storeMoshaver');
                Route::post('network', [BonyadEhsanUserController::class, 'storeNetwork'])->name('user.storeNetwork');
                Route::post('subNetwork',
                    [BonyadEhsanUserController::class, 'storeSubNetwork'])->name('user.storeSubnetwork');
                Route::post('groupUser',
                    [BonyadEhsanUserController::class, 'storeGroupUser'])->name('user.storeGroupUser');
                Route::delete('delete/{user}', [BonyadEhsanUserController::class, 'delete'])->name('user.delete');
                Route::get('consultant/{consultant}',
                    [BonyadEhsanUserController::class, 'consultantInfo'])->name('user.consultant');
                Route::get('selectOption', [
                    \App\Http\Controllers\Api\BonyadEhsan\Admin\ProductController::class, 'selectOption'
                ])->name('bonyad.select.option');
                Route::post('studentLimit',
                    [BonyadEhsanUserController::class, 'studentLimit'])->name('user.studentLimit');
            });
            Route::get('/form-builder', '\\'.FormBuilder::class);
            Route::group(['prefix' => 'setting', 'as' => 'setting'], function () {
                Route::get('/', [SettingController::class, 'index'])->name('admin.setting.index');
                Route::post('/', [SettingController::class, 'store'])->name('admin.setting.store');
                Route::put('{setting:key}', [SettingController::class, 'update'])->name('admin.setting.update');
                Route::delete('{setting}', [SettingController::class, 'destroy'])->name('admin.setting.destroy');
            });
            Route::resource('setting', '\\'.SettingController::class)->only(['index', 'store', 'update']);
            Route::post('setting/file', [SettingController::class, 'file'])->name('file');
        });

        Route::group(['prefix' => 'ewano'], function () {
            Route::post('/order', [EwanoController::class, 'makeOrder'])->name('ewano.make.order');
            Route::post('/pay', [EwanoController::class, 'pay'])->name('ewano.pay');
        });

        Route::apiResource('favorable-list', '\\'.FavorableListController::class);
        Route::group(['prefix' => 'bonyadEhsan', 'as' => 'bonyadEhsan'], function () {
            Route::group(['prefix' => 'notification', 'as' => 'notification'], function () {
                Route::get('/', [NotificationController::class, 'index'])->name('bonyad.notification.index');
                Route::post('/{id}/read', [NotificationController::class, 'read'])->name('bonyad.notification.read');
                Route::post('/readAll',
                    [NotificationController::class, 'readAll'])->name('bonyad.notification.readAll');
            });
            Route::group(['prefix' => 'abrisham', 'as' => 'abrisham'], function () {
                Route::get('lessons', [
                    \App\Http\Controllers\Api\BonyadEhsan\ProductController::class, 'abrishamLessons'
                ])->name('bonyadLessons');
            });
        });

        Route::get('product/{product}/contents',
            [ProductController::class, 'contents'])->name('api.v2.product.contents');
        Route::get('product/{product}/content-comments',
            [ProductController::class, 'contentComments'])->name('api.v2.product.content.comments');
        Route::get('user/favored', [UserController::class, 'userFavored'])->name('api.v2.user.favored');
        Route::post('user/exam-save', [UserController::class, 'examSave'])->name('api.v2.user.examSave');
        Route::get('user/products', [ProductController::class, 'userProducts'])->name('api.v2.user.products');
        Route::resource('vouchers', '\\'.VoucherManagementController::class, ['as' => 'api.v2.admin.'])->only([
            'store', 'show', 'update', 'destroy'
        ]);
        Route::post('vouchers/createByCompany',
            [VoucherManagementController::class, 'createVoucherByCompany'])->name('api.v2.admin.createByCompany');
        Route::resource('studyPlan', '\\'.StudyPlanController::class)->only(['index', 'update', 'show']);
        Route::get('subscriptions/user',
            ['\\'.SubscriptoinController::class, 'userSubscriptions'])->name('user.subscriptions');
        Route::post('user/subscription/inquiry',
            ['\\'.SubscriptoinController::class, 'subscriptionInquiry'])->name('user.subscriptions.inquiry');
        Route::post('subscription/update/value',
            ['\\'.SubscriptoinController::class, 'updateValue'])->name('user.subscriptions.updateValue');
        Route::resource('subscription', '\\'.SubscriptoinController::class)->only(['store']);
        Route::resource('timepoint', '\\'.TimepointController::class)->except(['create', 'edit']);
        Route::resource('ticket', '\\'.TicketController::class)->except(['edit']);
        Route::resource('ticketMessage', '\\'.TicketMessageController::class)->except(['create', 'edit']);
        Route::resource('firebasetoken', '\\'.FirebasetokenController::class)->only('store');
        Route::resource('contentIncome', '\\'.ContentInComeController::class)->only(['index', 'show']);
        Route::resource('comment', '\\'.CommentController::class)->only('store', 'update', 'destroy', 'show');
        Route::resource('watched', '\\'.WatchHistoryController::class)->only('store');
        Route::post('watched-bulk', [WatchHistoryController::class, 'bulkInsert']);
        Route::resource('livedescription',
            '\\'.LiveDescriptionController::class)->where(['livedescription' => '[0-9]+'])->except(['create', 'edit']);
        Route::resource('entekhab-reshte', '\\'.EntekhabReshteController::class)->only('store');
        Route::post('unwatched', [WatchHistoryController::class, 'destroyByWatchableId']);
        Route::get('contentIncomeGroupIndex', [ContentInComeController::class, 'groupIndex']);
        Route::get('studyPlan/planDate/{plan_date}/event/{event}/showByDate',
            [StudyPlanController::class, 'showByDateAndEvent'])->name('api.v2.studyPlan.show.by.date');
        Route::put('studyPlan/planDate/{plan_date}/event/{event}/updateByDate',
            [StudyPlanController::class, 'updateByDateAndEvent'])->name('api.v2.studyPlan.update.by.date');

        Route::post('donate', [OrderController::class, 'donateOrderV2'])->name('api.v2.make.donate');
//        Route::any('user/auth2/profile', [UserController::class, 'getAuth2Profile']);
        Route::get('user/{user}', [UserController::class, 'showV2'])->name('api.v2.user.show');
        Route::put('user/{user}', [UserController::class, 'updateV2'])->name('api.v2.user.update');
        Route::post('orderproduct', [OrderproductController::class, 'storeV2'])->name('api.v2.orderproduct.store');
        Route::delete('orderproduct/{orderproduct}',
            [OrderproductController::class, 'destroyV2'])->name('api.v2.orderproduct.destroy');
        Route::post('orderproduct/restore',
            [OrderproductController::class, 'restore'])->name('api.v2.orderproduct.restore');
        Route::delete('remove-order-product/{product}',
            [OrderController::class, 'removeOrderProduct'])->name('api.v2.order.remove-order-product');
//        Route::post('transaction', '\\'.ZarinpalTransactionController::class)->name('api.v2.zarinpal.transaction.store');
        Route::post('orderCoupon', [OrderController::class, 'submitCouponV2'])->name('api.v2.coupon.submit');
        Route::delete('orderCoupon', [OrderController::class, 'removeCouponV2'])->name('api.v2.coupon.remove');
        Route::post('/order-referral-code',
            [OrderController::class, 'submitReferralCode'])->name('api.v2.order.submitGiftCard');
        Route::delete('/order-referral-code',
            [OrderController::class, 'removeReferralCode'])->name('api.v2.order.removeGiftCard');

        Route::group(['prefix' => 'order'], function () {
            Route::post('3a', [OrderController::class, 'create3aOrder'])->name('api.v2.order.3a');
            Route::post('freeSubscription',
                [OrderController::class, 'freeSubscription'])->name('api.v2.order.freeSubscription');
        });

        Route::prefix('referral-code')->name('api.v2.referral-code.')->group(function () {
            Route::get('/', [ReferralCodesController::class, 'index'])->name('index');
            Route::get('/orderproducts', [ReferralCodesController::class, 'indexOrderproducts'])->name('orderproducts');
            Route::get('/noneProfitableOrderproducts', [
                ReferralCodesController::class, 'indexNoneProfitableOrderproducts'
            ])->name('orderproducts.noneProfitable');
            Route::get('/{referralCode}', [ReferralCodesController::class, 'show'])->name('show');
            Route::post('/batch-store', [ReferralCodesController::class, 'batchStore'])->name('batch-store');
            Route::post('/{referralCode}/assign', [ReferralCodesController::class, 'assign'])->name('assign');
        });

        Route::group(['prefix' => 'user', 'as' => 'api.v2.user.'], function () {
            Route::get('', [UserController::class, 'index'])->name('index');
            Route::get('{user}/orders', [UserController::class, 'userOrdersV2'])->name('orders');
            Route::get('{user}/transactions', [UserController::class, 'userTransactionsV2'])->name('transactions');
            Route::get('{user}/installments', [UserController::class, 'userInstallmentsV2'])->name('installments');
            Route::get('{user}/dashboard', '\\'.DashboardPageV2Controller::class)->name('dashboard');
            Route::post('{user}/firebasetoken', [FirebasetokenController::class, 'storeByUser']);
            Route::post('getInfo', [UserController::class, 'getInfo'])->name('getInfo');
            Route::post('national-card-photo',
                [UserController::class, 'storeNationalCardPhoto'])->name('store.nationalPhoto');
            Route::get('national-card-photo/get',
                [UserController::class, 'getNationalCardPhoto'])->name('get.nationalPhoto');
            Route::get('/products/hasPurchased',
                [UserController::class, 'hasPurchased'])->name('api.v2.user.hasPurchased');
            Route::get('/isPermittedToPurchase/{product}',
                [UserController::class, 'isPermittedToPurchase'])->name('api.v2.user.isPermittedToPurchase');
            Route::get('/get/entekhab-reshte',
                [UserController::class, 'getEntekhabReshte'])->name('api.v2.user.getEntekhabReshte');
        });

        Route::prefix('sales-man')->name('api.v2.sales-man')->group(function () {
            Route::get('/', [SalesManController::class, 'index'])->name('index');
            Route::post('/contract', [SalesManController::class, 'submitContract'])->name('contract');
        });
        Route::group(['prefix' => 'mobile', 'as' => 'api'], function () {
            Route::post('verify', [MobileVerificationController::class, 'verify'])->name('mobile.verification.verify');
            Route::get('resend', [MobileVerificationController::class, 'resend'])->name('mobile.verification.resend');
        });

        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/', '\\'.DashboardPageV2Controller::class)->name('api.v2.asset');
            Route::get('/abrisham', '\\'.AbrishamDashboardPageV2Controller::class)->name('api.v2.asset.abrisham');
        });

        Route::get('bookmark', '\\'.BookmarkPageV2Controller::class)->name('api.v2.bookmark');

        Route::group(['prefix' => 'checkout'], function () {
            Route::post('addDonate', [OrderController::class, 'addDonate']);
            Route::delete('removeDonate', [OrderController::class, 'removeDonate']);
        });

        Route::any('getPaymentRedirectEncryptedLink',
            '\\'.GetPaymentRedirectEncryptedLink::class)->name('api.v2.payment.getEncryptedLink');
        Route::post('voucher/verify', [VoucherController::class, 'verify'])->name('api.v2.verify.voucher');
        Route::post('voucher/disable', [VoucherController::class, 'disable'])->name('api.v2.disable.voucher');
        Route::post('voucher/submit', [VoucherController::class, 'submit'])->name('api.v2.submit.voucher');
        Route::post('insertKMT', [BotsController::class, 'queueBatchInsertJob'])->name('api.bot.pk');

        Route::group(['prefix' => 'product'], function () {

            Route::get('{product}/favored', [
                FavorableController::class, 'getUsersThatFavoredThisFavorable'
            ])->name('api.v2.get.user.favorite.product');
            Route::post('{product}/favored',
                [FavorableController::class, 'markFavorableFavorite'])->name('api.v2.mark.favorite.product');
            Route::post('{product}/unfavored',
                [FavorableController::class, 'markUnFavorableFavorite'])->name('api.v2.mark.unfavorite.product');

            Route::post('create', [ProductController::class, 'storeV2'])->name('api.v2.product.store');
            Route::put('{product}', [ProductController::class, 'updateV2'])->name('api.v2.product.update');
            Route::get('{product}/toWatch',
                [ProductController::class, 'nextWatchContent'])->name('api.v2.product.nextWatchContent');
            Route::get('{product}/liveInfo', [ProductController::class, 'liveInfo'])->name('api.v2.product.liveInfo');
            Route::post('{product}/updateSetOrder',
                [ProductController::class, 'updateSetOrder'])->name('api.v2.product.updateSetOrder');
        });

        Route::group(['prefix' => 'c'], function () {
            Route::get('{c}/favored', [
                FavorableController::class, 'getUsersThatFavoredThisFavorable'
            ])->name('api.v2.get.user.favorite.content');
            Route::post('{c}/favored',
                [FavorableController::class, 'markFavorableFavorite'])->name('api.v2.mark.favorite.content');
            Route::post('{c}/unfavored',
                [FavorableController::class, 'markUnFavorableFavorite'])->name('api.v2.mark.unfavorite.content');

            Route::group(['prefix' => 'timepoint'], function () {
                Route::get('{timepoint}/favored', [
                    FavorableController::class, 'getUsersThatFavoredThisFavorable'
                ])->name('api.v2.get.user.favorite.content.timepoint');
                Route::post('{timepoint}/favored', [
                    FavorableController::class, 'markFavorableFavorite'
                ])->name('api.v2.mark.favorite.content.timepoint');
                Route::post('{timepoint}/unfavored', [
                    FavorableController::class, 'markUnFavorableFavorite'
                ])->name('api.v2.mark.unfavorite.content.timepoint');
            });
        });

        Route::group(['prefix' => 'set'], function () {
            Route::get('{set}/favored',
                [FavorableController::class, 'getUsersThatFavoredThisFavorable'])->name('api.v2.get.user.favorite.set');
            Route::post('{set}/favored',
                [FavorableController::class, 'markFavorableFavorite'])->name('api.v2.mark.favorite.set');
            Route::post('{set}/unfavored',
                [FavorableController::class, 'markUnFavorableFavorite'])->name('api.v2.mark.unfavorite.set');
        });

        Route::group(['prefix' => 'ticket'], function () {
            Route::post('{ticket}/sendTicketStatusNotice', [TicketController::class, 'sendTicketStatusChangeNotice']);
            Route::post('{ticket}/assign', [TicketController::class, 'assign']);
            Route::post('{ticket}/rate', [TicketController::class, 'rate']);
        });

        Route::group(['prefix' => 'ticketMessage'], function () {
            Route::post('{ticketMessage}/report', [TicketMessageController::class, 'report']);
        });

        Route::group(['prefix' => 'firebasetoken'], function () {
            Route::delete('{refreshToken}', [FirebasetokenController::class, 'destroyByRefreshToken']);
            Route::put('{refreshToken}', [FirebasetokenController::class, 'updateByRefreshToken']);
        });

        Route::post('insertExcel', [BotsController::class, 'queueExcelInsertion'])->name('api.v2.queueExcelInsertion');
        Route::post('sc', [BotsController::class, 'sendCodeToUnknownNumber'])->name('api.v2.sendCodeToUnknownNumber');
        Route::get('getTelescopeExpiration', [HomeController::class, 'getUserTelescopeExpiration']);
        Route::get('getUserFor3a', [_3AController::class, 'getUserFor3a']);
        Route::get('getUserRoleAndPermission', [_3AController::class, 'getUserFor3a']);
        Route::get('getUserData/{user}', [BotsController::class, 'getUserData']);

        Route::resource('tagGroup', '\\'.TagGroupController::class)->only(['index']);

        Route::group(['as' => 'api.v2.'], function () {
            Route::resource('map-details', '\\'.MapDetailController::class)->except(['create', 'edit']);
            Route::group(['prefix' => 'abrisham', 'as' => 'abrisham.'], function () {
                Route::get('lessons', [ProductController::class, 'abrishamLessons'])->name('lessons');
                Route::get('flatLessons', [ProductController::class, 'flatLessons'])->name('flatLessons');
                Route::get('whereIsKarvan', [StudyEventController::class, 'whereIsKarvan'])->name('whereIsKarvan');
                Route::get('majors', [ProductController::class, 'abrishamMajors'])->name('majors');

                Route::get('/selectPlan/create',
                    [RahAbrishamController::class, 'selectPlanCreate'])->name('selectPlan.create');
                Route::get('/myStudyPlan', [StudyEventController::class, 'showMyStudyEvent'])->name('myStudyPlan.get');
                Route::post('/myStudyPlan',
                    [StudyEventController::class, 'storeMyStudyEvent'])->name('myStudyPlan.store');
                Route::get('/findStudyPlan', [StudyEventController::class, 'findStudyPlan'])->name('findStudyPlan');
                Route::get('/systemReport',
                    [RahAbrishamController::class, 'indexSystemReport'])->name('systemReport.get');
            });
            Route::group(['prefix' => 'taftan', 'as' => 'taftan.'], function () {
                Route::get('lessons', [ProductController::class, 'taftanLessons'])->name('lessons');
                Route::get('majors', [ProductController::class, 'taftanMajors'])->name('majors');
            });
            Route::group(['prefix' => 'chatreNejat', 'as' => 'chatreNejat.'], function () {
                Route::get('lessons', [ProductController::class, 'chatreNejatLessons'])->name('lessons');
                Route::get('majors', [ProductController::class, 'chatrNejatMajors'])->name('majors');
            });
            Route::group(['prefix' => 'studyPlan', 'as' => 'studyPlan.'], function () {
                Route::get('{studyPlan}/plans', [StudyPlanController::class, 'plans'])->name('plans');
            });
            Route::group(['prefix' => 'studyEvent', 'as' => 'event.'], function () {
                Route::get('{studyevent}/studyPlans', [StudyEventController::class, 'studyPlans'])->name('studyPlans');
                Route::get('whereIsEvent', [StudyEventController::class, 'whereIsEvent'])->name('whereIsEvent');
            });
            Route::group(['prefix' => 'employeetimesheet', 'as' => 'employeetimesheet.'], function () {
                Route::post('confirmOverTime',
                    [EmployeetimesheetController::class, 'confirmEmployeeOverTime'])->name('confirmOverTime');
            });
        });

        Route::group(['prefix' => 'livedescription', 'as' => 'LiveDescriptionController.'], function () {
            Route::get('/getPined', [LiveDescriptionController::class, 'getPined'])
                ->name('getPined');

            Route::get('/{livedescription}/pin', [LiveDescriptionController::class, 'pin'])
                ->name('pin');

            Route::get('/{livedescription}/unpin', [LiveDescriptionController::class, 'unpin'])
                ->name('unpin');

            Route::get('/{livedescription}/seen', [LiveDescriptionController::class, 'increaseSeen'])
                ->name('increaseSeeliveDescriptionn');
        });

        Route::post('authorize', [RolePermissionController::class, 'getResponse']);
        Route::post('authorizeWithPermissionName', [RolePermissionController::class, 'authorizeWithPermissionName']);
        Route::post('checkUserAccess', [UserController::class, 'checkUserAccess']);
        Route::group(['prefix' => 'exam'], function () {
            Route::get('rank-chart', [ExamResultsController::class, 'rankChart'])->name('api.v2.rank.charts');
            Route::get('user-rank', [ExamResultsController::class, 'userRank'])->name('api.v2.user.rank');
            Route::get('averageRank', [ExamResultsController::class, 'averageRanking'])->name('api.v2.average.rank');
            Route::get('getUsersOfBonyad', [ExamResultsController::class, 'getUsers'])->name('api.v2.get.users');
            Route::get('check-export/{excelExport}',
                [ExamResultsController::class, 'checkExport'])->name('api.v2.check.export');
        });

        Route::resource('paymentstatuses', '\\'.PaymentStatusController::class)->only(['index']);
        Route::get('content-statuses', [ContentStatusController::class, 'index']);
        Route::post('bank-accounts', [BankAccountController::class, 'store'])->name('api.v2.bank-account.store');
        Route::get('bank-accounts', [BankAccountController::class, 'index'])->name('api.v2.bank-account.index');
        Route::post('wallet/withdraw', [WalletController::class, 'withdrawWallet'])->name('api.v2.wallet.withdraw');
        Route::get('wallet/withdraw-requests',
            [WalletController::class, 'withdrawRequests'])->name('api.v2.wallet.request');

        Route::resource('event-result', '\\'.EventResultController::class)->only(['index', 'create', 'store', 'show']);
        Route::get('event-result/event/{event}',
            [EventResultController::class, 'getInfoByEvent'])->name('api.v2.eventResult.getInfo.byEvent');
        Route::prefix('events')->name('event.')->group(function () {
            Route::get('/', [StudyEventController::class, 'index'])->name('index');
            Route::get('{studyEvent}/advisor', [StudyEventController::class, 'advisor'])->name('advisor');
            Route::get('{studyEvent}/products', [StudyEventController::class, 'products'])->name('products');
        });
        Route::resource('events', '\\'.EventController::class, ['as' => 'api'])->only(['show', 'store']);

        Route::get('konkur1403Countdown',
            [HomeController::class, 'getKonkur1403Countdown'])->name('api.v2.getKonkur1403Countdown');

        Route::prefix('website-setting')->name('website-setting.')->group(function () {
            Route::post('/user', [WebsiteSettingController::class, 'storeUserSetting'])->name('store-user-setting');
            Route::get('/user', [WebsiteSettingController::class, 'userSetting'])->name('user-setting');
        });

        Route::prefix('study-event-report')->name('study-event-report.')->group(function () {
            Route::get('/{studyEventReport}/mark-as-read',
                [StudyEventReportController::class, 'markAsRead'])->name('mark-as-read');
        });

        Route::prefix('/live-conductor')->name('live-conductor.')->group(function () {
            Route::put('/view', [LiveConductorController::class, 'view'])->name('view');
        });
        Route::get('conductor/{liveConductor}/live', [LiveConductorController::class, 'show'])->name('show');
    });


    Route::get('/seo', '\\'.SeoController::class);
    Route::prefix('setting')->name('setting.')->group(function () {
        Route::get('/{setting:key}', [SettingController::class, 'show'])->name('show');
        Route::post('/uesrStore', [SettingController::class, 'userStore'])->middleware('auth:api')->name('user-store');
    });

    Route::post('/savePenaltyCoupon', [CouponController::class, 'savePenaltyCoupon'])->name('save.penalty.coupon');

    Route::post('sc/pen',
        [BotsController::class, 'sendCodeToUnknownNumberPen'])->name('api.v2.sendCodeToUnknownNumberPen');

    Route::post('/exam/store',
        [ExamResultsController::class, 'store'])->name('api.v2.store.exam')->middleware('3aIpAccess');

    Route::group(['prefix' => 'checkout'], function () {
        Route::get('review', [OrderController::class, 'checkoutReviewV2'])->name('api.v2.checkout.review');
    });
    Route::get('/orderWithTransaction/{order}',
        [App\Http\Controllers\Api\OrderController::class, 'show'])->name('api.v2.orderWithTransaction');


    Route::get('/gateways', [GatewayController::class, 'index'])->name('api.v2.gateways');


    Route::group(['prefix' => 'androidLog'], function () {
        Route::get('failTrack', [AndroidLogController::class, 'failTrack']);
    });

    Route::group(['prefix' => 'mobile'], function () {
        Route::get('resendGuest',
            [MobileVerificationController::class, 'resendGuest'])->name('mobile.verification.resendGuest');
        Route::post('verifyMoshavereh',
            [MobileVerificationController::class, 'verifyMoshavereh'])->name('mobile.verification.verifyMoshavereh');
    });

    Route::group(['prefix' => 'product'], function () {
        Route::get('soalaa/all', [ProductController::class, 'soalaaProducts'])->name('api.v2.product.soalaaProducts');
    });

    Route::resource('ch', '\\'.ApiChannelController::class, ['as' => 'api'])->only(['show']);

});

Route::post('/voip_admin', [VoipController::class, 'sendUserToAdmin'])
    ->name('api.voip_websocket_adminPannel');
