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
use App\Http\Controllers\Auth\ApiLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| V2
|--------------------------------------------------------------------------
*/
Route::prefix('v2')->group(function () {

    // FAQs
    Route::resource('faqs', FaqController::class)->only(['index', 'show']);
    Route::get('faq', '\\'.FaqPageController::class)->name('api.v2.faq');

    // Block routes
    Route::prefix('block')->name('api.')->group(function () {
        Route::resource('block', BlockController::class)->only(['show', 'index']);
        Route::get('{block}/products', [BlockRelationsController::class, 'products'])->name('block.products');
        Route::post('{block}/products/attach',
            [BlockRelationsController::class, 'attachProducts'])->name('block.attachProducts');
        Route::post('{block}/products/detach',
            [BlockRelationsController::class, 'detachProducts'])->name('block.detachProducts');
        Route::get('{block}/sets', [BlockRelationsController::class, 'sets'])->name('block.sets');
        Route::post('{block}/sets/attach', [BlockRelationsController::class, 'attachSets'])->name('block.attachSets');
        Route::post('{block}/sets/detach', [BlockRelationsController::class, 'detachSets'])->name('block.detachSets');
        Route::get('{block}/contents', [BlockRelationsController::class, 'contents'])->name('block.contents');
        Route::post('{block}/contents/attach',
            [BlockRelationsController::class, 'attachContents'])->name('block.attachContents');
        Route::post('{block}/contents/detach',
            [BlockRelationsController::class, 'detachContents'])->name('block.detachContents');
        Route::get('{block}/banners', [BlockRelationsController::class, 'banners'])->name('block.banners');
        Route::post('{block}/banners/attach',
            [BlockRelationsController::class, 'attachBanners'])->name('block.attachBanners');
        Route::post('{block}/banners/detach',
            [BlockRelationsController::class, 'detachBanners'])->name('block.detachBanners');
    });
    Route::get('get-blocks', [BlockController::class, 'block'])
        ->name('blocks.get');
    Route::get('blockSlideShows', [BlockSlideshowController::class, 'index'])->name('blockSlideShow.index');
    Route::get('blockTypes', [BlockTypesController::class, 'index'])->name('blockTypes.index');
    Route::get('blockSets', [BlockSetsController::class, 'index'])->name('blockSets.index');
    Route::get('blockProducts', [BlockProductsController::class, 'index'])->name('blockProducts.index');

    // Debug routes
    Route::get('debug', [HomeController::class, 'debug'])->name('api.v2.debug');
    Route::get('satra', [HomeController::class, 'satra']);

    // Landing routes
    Route::prefix('landing')->group(function () {
        for ($i = 1; $i <= 10; $i++) {
            Route::get($i, [ProductLandingController::class, 'landing'.$i])->name('api.v2.landing.'.$i);
        }
        Route::get('17', [ProductLandingController::class, 'landing17'])->name('api.v2.landing.17');
        Route::get('13Aban', [LandingPageController::class, 'roozeDaneshAmooz'])->name('api.v2.landing.13Aban');
        Route::get('13aban', [LandingPageController::class, 'roozeDaneshAmooz2'])->name('api.v2.landing.13aban');
    });

// Ewano routes
    Route::group(['prefix' => 'ewano'], function () {
        Route::get('/', [EwanoController::class, 'root'])->name('ewano.get');
        Route::post('/order', [EwanoController::class, 'makeOrder'])->name('ewano.make.order');
        Route::post('/pay', [EwanoController::class, 'pay'])->name('ewano.pay');
    });

// Search route
    Route::get('search', [SearchController::class, 'index'])->name('api.v2.search');

// App version route
    Route::get('lastVersion', [AppVersionController::class, 'showV2']);

// Authentication routes
    Route::post('login', [LoginController::class, 'login']); // Login route
    Route::post('logout', [ApiLoginController::class, 'logout'])->name('api.logout');
    Route::get('authTest', [HomeController::class, 'authTestV2'])->name('api.v2.authTest');

// Content routes
    Route::get('c/{c}', [ContentController::class, 'showV2'])->name('api.v2.content.show');
    Route::get('c/{c}/products', [ContentController::class, 'products'])->name('api.v2.content.products');
    Route::put('c/updateDuration', [ContentController::class, 'updateDuration'])->name('api.v2.content.updateDuration');
    Route::put('contents/bulk-update', [ContentController::class, 'bulkUpdate'])->name('content.bulk-update');
    Route::put('contents/bulk-edit-text', [ContentController::class, 'bulkEditText'])->name('content.bulk-edit-text');
    Route::put('contents/bulk-edit-tags', [ContentController::class, 'bulkEditTags'])->name('content.bulk-edit-tags');
    Route::get('content-statuses', [ContentStatusController::class, 'index']);

// Product routes
    Route::prefix('product')->name('api.v2.product.')->group(function () {
        Route::get('lives', [ProductController::class, 'lives'])->name('lives');
        Route::get('{product}', [ProductController::class, 'showV2'])->name('show');
        Route::get('{product}/sample', [ProductController::class, 'sampleVideo'])->name('sample');
        Route::get('{product}/faq', [ProductController::class, 'faq'])->name('faq');
        Route::get('{product}/complimentary', [ProductController::class, 'complimentary'])->name('complimentary');
        Route::get('{product}/exams', [ProductController::class, 'exams'])->name('exams');
        Route::get('gift-products/{product}',
            [ProductController::class, 'giftProducts'])->name('api.v2.product.gift-products');
        Route::get('{product}/sets', [ProductController::class, 'sets'])->name('sets');
        Route::get('{product}/contents', [ProductController::class, 'contents'])->name('api.v2.product.contents');
        Route::get('{product}/content-comments',
            [ProductController::class, 'contentComments'])->name('api.v2.product.content.comments');
        Route::get('{product}/favored', [FavorableController::class, 'getUsersThatFavoredThisFavorable'])
            ->name('api.v2.get.user.favorite.product');
        Route::post('{product}/favored', [FavorableController::class, 'markFavorableFavorite'])
            ->name('api.v2.mark.favorite.product');
        Route::post('{product}/unfavored',
            [FavorableController::class, 'markUnFavorableFavorite'])->name('api.v2.mark.unfavorite.product');
        Route::post('create', [ProductController::class, 'storeV2'])->name('api.v2.product.store');
        Route::put('{product}', [ProductController::class, 'updateV2'])->name('api.v2.product.update');
        Route::get('{product}/toWatch', [ProductController::class, 'nextWatchContent'])
            ->name('api.v2.product.nextWatchContent');
        Route::get('{product}/liveInfo', [ProductController::class, 'liveInfo'])
            ->name('api.v2.product.liveInfo');
        Route::post('{product}/updateSetOrder', [ProductController::class, 'updateSetOrder'])
            ->name('api.v2.product.updateSetOrder');
        Route::get('soalaa/all', [ProductController::class, 'soalaaProducts'])->name('api.v2.product.soalaaProducts');
    });
    Route::get('product-categories', [ProductController::class, 'productCategory'])->name('api.v2.product.category');
    Route::get('product', [ProductController::class, 'index'])->name('api.v2.product.index');
    Route::post('getPricgroupIndexe/{product}',
        [ProductController::class, 'refreshPriceV2'])->name('api.v2.refreshPrice');

    // Routes related to 'abrisham'
    Route::group(['prefix' => 'abrisham', 'as' => 'abrisham.'], function () {
        Route::get('lessons', [ProductController::class, 'abrishamLessons'])->name('lessons');
        Route::get('flatLessons', [ProductController::class, 'flatLessons'])->name('flatLessons');
        Route::get('whereIsKarvan', [StudyEventController::class, 'whereIsKarvan'])->name('whereIsKarvan');
        Route::get('majors', [ProductController::class, 'abrishamMajors'])->name('majors');
        Route::get('/selectPlan/create',
            [RahAbrishamController::class, 'selectPlanCreate'])->name('selectPlan.create');
        Route::get('/myStudyPlan', [StudyEventController::class, 'showMyStudyEvent'])->name('myStudyPlan.get');
        Route::post('/myStudyPlan', [StudyEventController::class, 'storeMyStudyEvent'])->name('myStudyPlan.store');
        Route::get('/findStudyPlan', [StudyEventController::class, 'findStudyPlan'])->name('findStudyPlan');
        Route::get('/systemReport', [RahAbrishamController::class, 'indexSystemReport'])->name('systemReport.get');
        Route::get('lessons', [ProductController::class, 'abrishamLessons'])->name('bonyadLessons');
    });

    // Routes related to 'taftan'
    Route::group(['prefix' => 'taftan', 'as' => 'taftan.'], function () {
        Route::get('lessons', [ProductController::class, 'taftanLessons'])->name('lessons');
        Route::get('majors', [ProductController::class, 'taftanMajors'])->name('majors');
    });

    // Routes related to 'chatre Nejat'
    Route::group(['prefix' => 'chatreNejat', 'as' => 'chatreNejat.'], function () {
        Route::get('lessons', [ProductController::class, 'chatreNejatLessons'])->name('lessons');
        Route::get('majors', [ProductController::class, 'chatrNejatMajors'])->name('majors');
    });

// Set routes
    Route::prefix('set')->name('api.v2.set.')->group(function () {
        Route::get('', [SetController::class, 'index'])->name('index');
        Route::get('{set}', [SetController::class, 'showV2'])->name('show');
        Route::get('{set}/contents', [SetController::class, 'contents'])->name('contents');
        Route::get('{set}/favored', [FavorableController::class, 'getUsersThatFavoredThisFavorable'])
            ->name('api.v2.get.user.favorite.set');
        Route::post('{set}/favored', [FavorableController::class, 'markFavorableFavorite'])
            ->name('api.v2.mark.favorite.set');
        Route::post('{set}/unfavored', [FavorableController::class, 'markUnFavorableFavorite'])
            ->name('api.v2.mark.unfavorite.set');
    });
    Route::get('content-set/{set}', [SetController::class, 'showWithContents']);

// Forrest routes
    Route::resource('/forrest/tree', '\\'.ForrestController::class)->only(['index', 'store', 'update']);
    Route::get('/forrest/tree/{grid}',
        [ForrestController::class, 'show'])->name('api.v2.forrest.tree.show');
    Route::get('/forrest/tags', [ForrestController::class, 'tags'])->name('api.v2.forrest.tree.tags');


// Additional Home routes
    Route::get('shop', '\\'.ShopPageController::class)->name('api.v2.shop');
    Route::get('home', '\\'.IndexPageController::class)->name('api.v2.home');
    Route::get('contact', '\\'.ContactUsController::class)->name('api.v2.contact');
    Route::get('rule', '\\'.RulesPageController::class)->name('api.v2.rule');

// Newsletter Routes
    Route::resource('newsletter', '\\'.NewsletterController::class, ['as' => 'api'])->only(['store']);


// Comment Routes
    Route::resource('comment', '\\'.CommentController::class)->only(['store', 'update', 'destroy', 'show']);

// Watched Routes
    Route::resource('watched', '\\'.WatchHistoryController::class)->only('store');
    Route::post('watched-bulk', [WatchHistoryController::class, 'bulkInsert']);

// Donate route
    Route::get('donate', '\\'.DonateController::class)->name('api.v2.donate');
    Route::post('donate', [OrderController::class, 'donateOrderV2'])->name('api.v2.make.donate');

// Mega route group
    Route::group(['prefix' => 'megaroute', 'as' => 'api.v2.'], function () {
        Route::get('getUserFormData',
            [UserController::class, 'getUserFormData'])->name('user.formData');
    });

// SMS Routes
    Route::group(['prefix' => 'sms'], function () {
        // Get Credit for Mediana
        Route::get('mediana-get-credit',
            [SmsController::class, 'getCreditForMediana'])->middleware('auth:api')->name('sms.mediana-get-credit');
        Route::get('/receive', '\\'.ReceiveSMSController::class)->name('sms.receive');
        Route::post('/sendPattern/{user}', [SmsController::class, 'pattern'])->name('sms.sendPattern');
        Route::post('/sendBulk', [SmsController::class, 'sendBulk'])->middleware('auth:api')->name('sms.sendBulk');
        Route::get('/', [SmsController::class, 'index'])->name('sms.index');

    });

//=================================== Admin Routes ============================================
    Route::group(['middleware' => 'auth:api'], function () {

        // Admin-Clear Cache
        Route::get('/admin/cache-clear', '\\'.CacheController::class)
            ->name('admin.cache-clear');

        // Admin-Manage Transactions
        Route::resource('admin/transaction', '\\'.TransactionController::class);

        // Admin-Manage Attributes
        Route::resource('admin/attribute', '\\'.AttributeController::class);

        // Admin-Manage Attribute Sets
        Route::resource('admin/attribute-set', '\\'.AttributeSetController::class);

        // Admin-FAQs
        Route::group(['prefix' => 'admin/faq', 'as' => 'admin.faq.'], function () {
            Route::get('/', [AdminFaqController::class, 'index'])
                ->name('index');
            Route::post('/', [AdminFaqController::class, 'store'])
                ->name('store');
            Route::put('/{faq}', [AdminFaqController::class, 'update'])
                ->name('update');
            Route::delete('/{faq}', [AdminFaqController::class, 'delete'])
                ->name('delete');
        });
    });

// Admin-Block Routes
    Route::resource('block', '\\'.AdminBlockController::class)->except(['create', 'edit']);
    Route::patch('/block/{block}/syncProducts', [AdminBlockController::class, 'syncProducts'])->name('syncProducts');
    Route::patch('/block/{block}/syncSets', [AdminBlockController::class, 'syncSets'])->name('syncSets');
    Route::patch('/block/{block}/syncBanners', [AdminBlockController::class, 'syncBanners'])->name('syncBanners');
    Route::patch('/block/{block}/syncContents', [AdminBlockController::class, 'syncContents'])->name('syncContents');

// Admin-Content Routes
    Route::resource('contents', '\\'.AdminContentController::class, ['as' => 'api'])->only(['index', 'show', 'update']);
    Route::get('c', [AdminContentController::class, 'index'])->name('c.index');
    Route::post('contents/destroy', [AdminContentController::class, 'destroy'])->name('content.bulk.destroy');
    Route::post('content/{content}/copy', [AdminContentController::class, 'copy'])->name('content.copy');

// Admin-Set Routes
    Route::resource('set', '\\'.AdminSetController::class, ['as' => 'api']);
    Route::post('set/{set}/c/attach', [AdminSetController::class, 'attachContents'])->name('set.attachContents');
    Route::get('set/{set}/contents', [AdminSetController::class, 'contents'])->name('set.contents');

// Admin-Product Routes
    Route::post('product/set-discount', [AdminProductController::class, 'setDiscount'])->name('product.set-discount');
    Route::put('product/bulk-update-statuses',
        [AdminProductController::class, 'bulkUpdateStatuses'])->name('product.bulk-update-statuses');
    Route::resource('product', '\\'.AdminProductController::class, ['as' => 'api'])->except(['create', 'edit']);
    Route::get('product/{product}/sets', [AdminProductController::class, 'sets'])->name('product.sets');
    Route::post('product/{product}/copy', [AdminProductController::class, 'copy'])->name('product.copy');

// Admin-User Routes
    Route::resource('user', '\\'.AdminUserController::class, ['as' => 'api'])->except(['create', 'edit']);

// Admin-Permission Routes
    Route::resource('permission', '\\'.AdminPermissionController::class, ['as' => 'api'])->except(['create', 'edit']);

// Admin-Role Routes
    Route::resource('role', '\\'.AdminRoleController::class, ['as' => 'api'])->except(['create', 'edit']);

// Admin-Order Routes
    Route::post('orderBatchTransfer', [AdminOrderController::class, 'orderBatchTransfer'])->name('order.batchTransfer');
    Route::resource('order', '\\'.AdminOrderController::class)->except(['create', 'edit']);

// Admin-Employee Schedule Routes
    Route::post('employeeSchedule/batchUpdate',
        [AdminEmployeeScheduleController::class, 'batchUpdate'])->name('employeeSchedule.batchUpdate');
    Route::resource('employeeSchedules', '\\'.AdminEmployeeScheduleController::class)->only(['index', 'store']);

// Admin-Activity Log Routes
    Route::resource('activityLog', '\\'.AdminActivityLogController::class)->only(['index']);

// Admin-Slideshow Routes
    Route::resource('slideshow', '\\'.AdminSlideshowController::class)->only(['index']);

// Admin-Abrisham Product Choice Route
    Route::get('abrisham/productChoice',
        [AdminOrderController::class, 'abrishamProductChoice'])->name('abrisham.productChoice');

    //=================================== Setting Routes ============================================
    Route::group(['prefix' => 'setting', 'as' => 'setting'], function () {
        Route::resource('', '\\'.SettingController::class)->only(['index', 'store', 'update']);
        Route::get('/', [SettingController::class, 'index'])->name('admin.setting.index');
        Route::post('/', [SettingController::class, 'store'])->name('admin.setting.store');
        Route::put('{setting:key}', [SettingController::class, 'update'])->name('admin.setting.update');
        Route::delete('{setting}', [SettingController::class, 'destroy'])->name('admin.setting.destroy');
        Route::post('file', [SettingController::class, 'file'])->name('file');
    });

    // Setting Controller Routes
    Route::prefix('setting')->name('setting.')->group(function () {
        Route::get('/{setting:key}', [SettingController::class, 'show'])->name('show');
        Route::post('/uesrStore', [SettingController::class, 'userStore'])->middleware('auth:api')->name('user-store');
    });

// Website setting routes
    Route::prefix('website-setting')->name('website-setting.')->group(function () {
        Route::post('/user', [WebsiteSettingController::class, 'storeUserSetting'])->name('store-user-setting');
        Route::get('/user', [WebsiteSettingController::class, 'userSetting'])->name('user-setting');
    });

// Upload Center routes
    Route::post('upload/presigned-request',
        [UploadCenterController::class, 'presignedRequest'])->name('upload.presigned-request');
    Route::get('upload', [UploadCenterController::class, 'upload'])->name('upload');

// User Routes
    Route::prefix('user')->group(function () {

        Route::get('favored', [UserController::class, 'userFavored'])->name('api.v2.user.favored');
        Route::post('exam-save', [UserController::class, 'examSave'])->name('api.v2.user.examSave');
        Route::get('products', [ProductController::class, 'userProducts'])->name('api.v2.user.products');
        Route::get('{user}', [UserController::class, 'showV2'])->name('api.v2.user.show');
        Route::put('{user}', [UserController::class, 'updateV2'])->name('api.v2.user.update');
    });

    Route::post('checkUserAccess', [UserController::class, 'checkUserAccess']);
    Route::get('unknownUsersCityIndex',
        [UserController::class, 'unknownUsersCityIndex'])->name('user.index.unknown.city');

// Coupon Routes
    Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
        Route::resource('', '\\'.CouponController::class)->except(['create', 'edit']);
        Route::get('findByCode', [CouponController::class, 'findByCode'])->name('findByCode');
        Route::post('generateMassiveRandomCoupon',
            [CouponController::class, 'generateMassiveRandomCoupon'])->name('massive.random');
    });
    Route::post('/savePenaltyCoupon', [CouponController::class, 'savePenaltyCoupon'])->name('save.penalty.coupon');

// BonyadEhsan Routes
    Route::group(['prefix' => 'bonyadEhsan', 'as' => 'bonyadEhsan'], function () {
        Route::get('order', [BonyadEhsanOrderController::class, 'index'])->name('order.index');
        Route::delete('order/{order}', [BonyadEhsanOrderController::class, 'destroy'])->name('order.remove');
        Route::post('user', [BonyadEhsanUserController::class, 'store'])->name('user.store');
        Route::get('user/myInfo', [BonyadEhsanUserController::class, 'showLoginUser'])->name('user.show.myInfo');
        Route::get('user/{user}', [BonyadEhsanUserController::class, 'show'])->name('user.show');
        Route::put('user/{user}', [BonyadEhsanUserController::class, 'update'])->name('user.update');
        Route::post('moshaver', [BonyadEhsanUserController::class, 'storeMoshaver'])->name('user.storeMoshaver');
        Route::post('network', [BonyadEhsanUserController::class, 'storeNetwork'])->name('user.storeNetwork');
        Route::post('subNetwork', [BonyadEhsanUserController::class, 'storeSubNetwork'])->name('user.storeSubnetwork');
        Route::post('groupUser', [BonyadEhsanUserController::class, 'storeGroupUser'])->name('user.storeGroupUser');
        Route::delete('delete/{user}', [BonyadEhsanUserController::class, 'delete'])->name('user.delete');
        Route::get('consultant/{consultant}',
            [BonyadEhsanUserController::class, 'consultantInfo'])->name('user.consultant');
        Route::get('selectOption', [
            \App\Http\Controllers\Api\BonyadEhsan\Admin\ProductController::class, 'selectOption'
        ])->name('bonyad.select.option');
        Route::post('studentLimit', [BonyadEhsanUserController::class, 'studentLimit'])->name('user.studentLimit');
        Route::group(['prefix' => 'notification', 'as' => 'notification'], function () {
            Route::get('/', [NotificationController::class, 'index'])->name('bonyad.notification.index');
            Route::post('/{id}/read', [NotificationController::class, 'read'])->name('bonyad.notification.read');
            Route::post('/readAll', [NotificationController::class, 'readAll'])->name('bonyad.notification.readAll');
        });
    });

// Form Builder Routes
    Route::get('/form-builder', '\\'.FormBuilder::class);


// Favorable List Routes
    Route::apiResource('favorable-list', '\\'.FavorableListController::class);

// Voucher Routes
    Route::prefix('voucher')->group(function () {
        Route::post('createByCompany',
            [VoucherManagementController::class, 'createVoucherByCompany'])->name('api.v2.admin.createByCompany');
        Route::post('verify', [VoucherController::class, 'verify'])->name('api.v2.verify.voucher');
        Route::post('disable', [VoucherController::class, 'disable'])->name('api.v2.disable.voucher');
        Route::post('submit', [VoucherController::class, 'submit'])->name('api.v2.submit.voucher');
    });
    Route::resource('vouchers', '\\'.VoucherManagementController::class, ['as' => 'api.v2.admin.'])->only([
        'store', 'show', 'update', 'destroy'
    ]);

// Study Plan routes
    Route::prefix('studyPlan')->group(function () {
        Route::resource('/', '\\'.StudyPlanController::class)->only(['index', 'update', 'show']);
        Route::get('planDate/{plan_date}/event/{event}/showByDate',
            [StudyPlanController::class, 'showByDateAndEvent'])->name('api.v2.studyPlan.show.by.date');
        Route::put('planDate/{plan_date}/event/{event}/updateByDate',
            [StudyPlanController::class, 'updateByDateAndEvent'])->name('api.v2.studyPlan.update.by.date');
        Route::get('{studyPlan}/plans', [StudyPlanController::class, 'plans'])->name('plans');
    });
    Route::resource('plan', '\\'.PlanController::class, ['as' => 'api'])->except(['create', 'edit']);

// Subscriptions routes
    Route::get('subscriptions/user',
        ['\\'.SubscriptoinController::class, 'userSubscriptions'])->name('user.subscriptions');
    Route::post('user/subscription/inquiry',
        ['\\'.SubscriptoinController::class, 'subscriptionInquiry'])->name('user.subscriptions.inquiry');
    Route::post('subscription/update/value',
        ['\\'.SubscriptoinController::class, 'updateValue'])->name('user.subscriptions.updateValue');
    Route::resource('subscription', '\\'.SubscriptoinController::class)->only(['store']);

// Entekhab Reshte routes
    Route::resource('entekhab-reshte', '\\'.EntekhabReshteController::class)->only('store');

// Watch History routes
    Route::post('unwatched', [WatchHistoryController::class, 'destroyByWatchableId']);

// Content Income routes
    Route::get('contentIncomeGroupIndex', [ContentInComeController::class, 'groupIndex']);
    Route::resource('contentIncome', '\\'.ContentInComeController::class)->only(['index', 'show']);

// Order routes
    Route::post('orderproduct', [OrderproductController::class, 'storeV2'])->name('api.v2.orderproduct.store');
    Route::delete('orderproduct/{orderproduct}',
        [OrderproductController::class, 'destroyV2'])->name('api.v2.orderproduct.destroy');
    Route::post('orderproduct/restore',
        [OrderproductController::class, 'restore'])->name('api.v2.orderproduct.restore');
    Route::delete('remove-order-product/{product}',
        [OrderController::class, 'removeOrderProduct'])->name('api.v2.order.remove-order-product');
    Route::post('orderCoupon', [OrderController::class, 'submitCouponV2'])->name('api.v2.coupon.submit');
    Route::delete('orderCoupon', [OrderController::class, 'removeCouponV2'])->name('api.v2.coupon.remove');
    Route::post('/order-referral-code',
        [OrderController::class, 'submitReferralCode'])->name('api.v2.order.submitGiftCard');
    Route::delete('/order-referral-code',
        [OrderController::class, 'removeReferralCode'])->name('api.v2.order.removeGiftCard');

// Order nested routes
    Route::group(['prefix' => 'order'], function () {
        Route::post('3a', [OrderController::class, 'create3aOrder'])->name('api.v2.order.3a');
        Route::post('freeSubscription',
            [OrderController::class, 'freeSubscription'])->name('api.v2.order.freeSubscription');
    });

// Referral Code routes
    Route::prefix('referral-code')->name('api.v2.referral-code.')->group(function () {
        Route::get('/', [ReferralCodesController::class, 'index'])->name('index');
        Route::get('/orderproducts', [ReferralCodesController::class, 'indexOrderproducts'])->name('orderproducts');
        Route::get('/noneProfitableOrderproducts',
            [ReferralCodesController::class, 'indexNoneProfitableOrderproducts'])->name('orderproducts.noneProfitable');
        Route::get('/{referralCode}', [ReferralCodesController::class, 'show'])->name('show');
        Route::post('/batch-store', [ReferralCodesController::class, 'batchStore'])->name('batch-store');
        Route::post('/{referralCode}/assign', [ReferralCodesController::class, 'assign'])->name('assign');
    });

// User routes
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
        Route::get('/products/hasPurchased', [UserController::class, 'hasPurchased'])->name('api.v2.user.hasPurchased');
        Route::get('/isPermittedToPurchase/{product}',
            [UserController::class, 'isPermittedToPurchase'])->name('api.v2.user.isPermittedToPurchase');
        Route::get('/get/entekhab-reshte',
            [UserController::class, 'getEntekhabReshte'])->name('api.v2.user.getEntekhabReshte');
    });

// Sales Man routes
    Route::prefix('sales-man')->name('api.v2.sales-man')->group(function () {
        Route::get('/', [SalesManController::class, 'index'])->name('index');
        Route::post('/contract', [SalesManController::class, 'submitContract'])->name('contract');
    });

// Mobile Verification routes
    Route::prefix('mobile')->group(function () {
        Route::post('verify', [MobileVerificationController::class, 'verify'])->name('api.mobile.verification.verify');
        Route::get('resend', [MobileVerificationController::class, 'resend'])->name('api.mobile.verification.resend');
        Route::get('resendGuest',
            [MobileVerificationController::class, 'resendGuest'])->name('mobile.verification.resendGuest');
        Route::post('verifyMoshavereh',
            [MobileVerificationController::class, 'verifyMoshavereh'])->name('mobile.verification.verifyMoshavereh');
    });

// Dashboard routes
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', '\\'.DashboardPageV2Controller::class)->name('api.v2.asset');
        Route::get('/abrisham', '\\'.AbrishamDashboardPageV2Controller::class)->name('api.v2.asset.abrisham');
    });

// Bookmark route
    Route::get('bookmark', '\\'.BookmarkPageV2Controller::class)->name('api.v2.bookmark');

// Checkout routes
    Route::group(['prefix' => 'checkout'], function () {
        Route::get('payment', [OrderController::class, 'checkoutPayment'])->name('api.v2.checkout.payment');
        Route::post('addDonate', [OrderController::class, 'addDonate']);
        Route::delete('removeDonate', [OrderController::class, 'removeDonate']);
    });

// Payment redirect link
    Route::any('getPaymentRedirectEncryptedLink',
        '\\'.GetPaymentRedirectEncryptedLink::class)->name('api.v2.payment.getEncryptedLink');

// Insert KMT route
    Route::post('insertKMT', [BotsController::class, 'queueBatchInsertJob'])->name('api.bot.pk');

// Time point routes
    Route::group(['prefix' => 'timepoint'], function () {
        Route::resource('', '\\'.TimepointController::class)->except(['create', 'edit']);
        Route::get('{timepoint}/favored', [FavorableController::class, 'getUsersThatFavoredThisFavorable'])
            ->name('api.v2.get.user.favorite.content.timepoint');
        Route::post('{timepoint}/favored', [FavorableController::class, 'markFavorableFavorite'])
            ->name('api.v2.mark.favorite.content.timepoint');
        Route::post('{timepoint}/unfavored', [FavorableController::class, 'markUnFavorableFavorite'])
            ->name('api.v2.mark.unfavorite.content.timepoint');
    });

// Ticket routes
    Route::group(['prefix' => 'ticket'], function () {
        Route::resource('', '\\'.TicketController::class)->except(['edit']);
        Route::post('{ticket}/sendTicketStatusNotice', [TicketController::class, 'sendTicketStatusChangeNotice']);
        Route::post('{ticket}/assign', [TicketController::class, 'assign']);
        Route::post('{ticket}/rate', [TicketController::class, 'rate']);
    });
    Route::group(['prefix' => 'ticketMessage'], function () {
        Route::post('{ticketMessage}/report', [TicketMessageController::class, 'report']);
    });
    Route::resource('ticketPriority', '\\'.TicketPriorityController::class)->only(['index']);
    Route::resource('ticketDepartment', '\\'.TicketDepartmentController::class, ['as' => 'api'])->only(['index']);
    Route::resource('ticketMessage', '\\'.TicketMessageController::class)->except(['create', 'edit']);

// Firebase token routes
    Route::group(['prefix' => 'firebasetoken'], function () {
        Route::resource('', '\\'.FirebasetokenController::class)->only('store');
        Route::delete('{refreshToken}', [FirebasetokenController::class, 'destroyByRefreshToken']);
        Route::put('{refreshToken}', [FirebasetokenController::class, 'updateByRefreshToken']);
    });

// Routes related to BotsController
    Route::post('insertExcel', [BotsController::class, 'queueExcelInsertion'])->name('api.v2.queueExcelInsertion');
    Route::post('sc', [BotsController::class, 'sendCodeToUnknownNumber'])->name('api.v2.sendCodeToUnknownNumber');
    Route::get('getUserData/{user}', [BotsController::class, 'getUserData']);

// Routes related to HomeController
    Route::get('getTelescopeExpiration', [HomeController::class, 'getUserTelescopeExpiration']);

// Routes related to 3AController
    Route::get('getUserFor3a', [_3AController::class, 'getUserFor3a']);
    Route::get('getUserRoleAndPermission', [_3AController::class, 'getUserFor3a']);

// Tag group routes
    Route::resource('tagGroup', '\\'.TagGroupController::class)->only(['index']);

// Routes map-details
    Route::group(['as' => 'api.v2.'], function () {
        Route::resource('map-details', '\\'.MapDetailController::class)->except(['create', 'edit']);
    });

// Routes related to 'studyEvent'
    Route::group(['prefix' => 'studyEvent', 'as' => 'event.'], function () {
        Route::get('{studyevent}/studyPlans', [StudyEventController::class, 'studyPlans'])->name('studyPlans');
        Route::get('whereIsEvent', [StudyEventController::class, 'whereIsEvent'])->name('whereIsEvent');
    });

// Routes related to 'employee timesheet'
    Route::group(['prefix' => 'employeetimesheet', 'as' => 'employeetimesheet.'], function () {
        Route::post('confirmOverTime',
            [EmployeetimesheetController::class, 'confirmEmployeeOverTime'])->name('confirmOverTime');
    });

//Live Routes
    Route::group(['prefix' => 'livedescription', 'as' => 'LiveDescriptionController.'], function () {
        Route::resource('',
            '\\'.LiveDescriptionController::class)->where(['livedescription' => '[0-9]+'])->except(['create', 'edit']);
        Route::get('/getPined', [LiveDescriptionController::class, 'getPined'])
            ->name('getPined');
        Route::get('/{livedescription}/pin', [LiveDescriptionController::class, 'pin'])
            ->name('pin');
        Route::get('/{livedescription}/unpin', [LiveDescriptionController::class, 'unpin'])
            ->name('unpin');
        Route::get('/{livedescription}/seen', [LiveDescriptionController::class, 'increaseSeen'])
            ->name('increaseSeeliveDescriptionn');
    });

//Role Routes
    Route::post('authorize', [RolePermissionController::class, 'getResponse']);
    Route::post('authorizeWithPermissionName', [RolePermissionController::class, 'authorizeWithPermissionName']);

// Exam routes
    Route::group(['prefix' => 'exam'], function () {
        Route::get('rank-chart', [ExamResultsController::class, 'rankChart'])->name('api.v2.rank.charts');
        Route::get('user-rank', [ExamResultsController::class, 'userRank'])->name('api.v2.user.rank');
        Route::get('averageRank', [ExamResultsController::class, 'averageRanking'])->name('api.v2.average.rank');
        Route::get('getUsersOfBonyad', [ExamResultsController::class, 'getUsers'])->name('api.v2.get.users');
        Route::get('check-export/{excelExport}',
            [ExamResultsController::class, 'checkExport'])->name('api.v2.check.export');
    });

// Exam Results Controller Route
    Route::post('/exam/store',
        [ExamResultsController::class, 'store'])->name('api.v2.store.exam')->middleware('3aIpAccess');

    Route::group(['prefix' => 'checkout'], function () {
        Route::get('review', [OrderController::class, 'checkoutReviewV2'])->name('api.v2.checkout.review');
    });
    Route::get('/orderWithTransaction/{order}',
        [App\Http\Controllers\Api\OrderController::class, 'show'])->name('api.v2.orderWithTransaction');

// Payment status routes
    Route::resource('paymentstatuses', '\\'.PaymentStatusController::class)->only(['index']);
    Route::post('bank-accounts', [BankAccountController::class, 'store'])->name('api.v2.bank-account.store');
    Route::get('bank-accounts', [BankAccountController::class, 'index'])->name('api.v2.bank-account.index');
    Route::post('wallet/withdraw', [WalletController::class, 'withdrawWallet'])->name('api.v2.wallet.withdraw');
    Route::get('wallet/withdraw-requests',
        [WalletController::class, 'withdrawRequests'])->name('api.v2.wallet.request');

// Event result routes
    Route::resource('event-result', '\\'.EventResultController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('event-result/event/{event}',
        [EventResultController::class, 'getInfoByEvent'])->name('api.v2.eventResult.getInfo.byEvent');

// Study event routes
    Route::prefix('events')->name('event.')->group(function () {
        Route::get('/', [StudyEventController::class, 'index'])->name('index');
        Route::get('{studyEvent}/advisor', [StudyEventController::class, 'advisor'])->name('advisor');
        Route::get('{studyEvent}/products', [StudyEventController::class, 'products'])->name('products');
    });
    Route::resource('events', '\\'.EventController::class, ['as' => 'api'])->only(['show', 'store']);
    Route::prefix('study-event-report')->name('study-event-report.')->group(function () {
        Route::get('/{studyEventReport}/mark-as-read',
            [StudyEventReportController::class, 'markAsRead'])->name('mark-as-read');
    });

// Additional miscellaneous routes
    Route::get('konkur1403Countdown',
        [HomeController::class, 'getKonkur1403Countdown'])->name('api.v2.getKonkur1403Countdown');

// Route for updating the view of a live conductor
    Route::prefix('/live-conductor')->name('live-conductor.')->group(function () {
        Route::put('/view', [LiveConductorController::class, 'view'])->name('view');
    });

// Route for showing live conductor information
    Route::get('conductor/{liveConductor}/live', [LiveConductorController::class, 'show'])->name('show');

// Seo Controller Route
    Route::get('/seo', '\\'.SeoController::class);

// Coupon Controller Routes
    Route::post('/savePenaltyCoupon', [CouponController::class, 'savePenaltyCoupon'])->name('save.penalty.coupon');

// Bots Controller Routes
    Route::post('sc/pen',
        [BotsController::class, 'sendCodeToUnknownNumberPen'])->name('api.v2.sendCodeToUnknownNumberPen');

// Gateway Controller Route
    Route::get('/gateways', [GatewayController::class, 'index'])->name('api.v2.gateways');

// Android Log Controller Route
    Route::group(['prefix' => 'androidLog'], function () {
        Route::get('failTrack', [AndroidLogController::class, 'failTrack']);
    });

// Api Channel Controller Route
    Route::resource('ch', '\\'.ApiChannelController::class, ['as' => 'api'])->only(['show']);

// Voip Controller Route
    Route::post('/voip_admin', [VoipController::class, 'sendUserToAdmin'])->name('api.voip_websocket_adminPannel');
});