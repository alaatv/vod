<?php

use App\Http\Controllers\AndroidLogController;
use App\Http\Controllers\Api\_3AController;
use App\Http\Controllers\Api\Admin\CouponController;
use App\Http\Controllers\Api\Admin\MarketingController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\TransactionController;
use App\Http\Controllers\Api\Admin\VoipController;
use App\Http\Controllers\Api\Admin\VoucherManagementController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AppVersionController;
use App\Http\Controllers\Api\AttributegroupController;
use App\Http\Controllers\Api\BatchContentInsertController;
use App\Http\Controllers\Api\BonyadEhsan\Admin\NotificationController;
use App\Http\Controllers\Api\BonyadEhsan\Admin\OrderController as BonyadEhsanOrderController;
use App\Http\Controllers\Api\BonyadEhsan\Admin\UserController as BonyadEhsanUserController;
use App\Http\Controllers\Api\BotsController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\DanaController;
use App\Http\Controllers\Api\DashboardPageController;
use App\Http\Controllers\Api\DashboardPageV2Controller;
use App\Http\Controllers\Api\DonateController;
use App\Http\Controllers\Api\DraftController;
use App\Http\Controllers\Api\EmployeetimesheetController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventResultController;
use App\Http\Controllers\Api\EwanoController;
use App\Http\Controllers\Api\ExamResultsController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\FaqPageController;
use App\Http\Controllers\Api\FavorableListController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FirebasetokenController;
use App\Http\Controllers\Api\FormBuilder;
use App\Http\Controllers\Api\ForrestController;
use App\Http\Controllers\Api\GatewayController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\IndexPageController;
use App\Http\Controllers\Api\LiveConductorController;
use App\Http\Controllers\Api\LiveController;
use App\Http\Controllers\Api\LiveDescriptionController;
use App\Http\Controllers\Api\MapDetailController;
use App\Http\Controllers\Api\MapPageController;
use App\Http\Controllers\Api\MobileVerificationController;
use App\Http\Controllers\Api\NetworkMarketingController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderproductController;
use App\Http\Controllers\Api\PeriodDescriptionController;
use App\Http\Controllers\Api\PhoneBookController;
use App\Http\Controllers\Api\PhoneController;
use App\Http\Controllers\Api\PhoneNumberController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductfileController;
use App\Http\Controllers\Api\ProductphotoController;
use App\Http\Controllers\Api\ReceiveSMSController;
use App\Http\Controllers\Api\RulesPageController;
use App\Http\Controllers\Api\SalesManController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\SeoController;
use App\Http\Controllers\Api\ShahrController;
use App\Http\Controllers\Api\ShopPageController;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\SmsUserController;
use App\Http\Controllers\Api\SourceController;
use App\Http\Controllers\Api\StudyEventController;
use App\Http\Controllers\Api\StudyEventReportController;
use App\Http\Controllers\Api\StudyPlanController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SubscriptoinController;
use App\Http\Controllers\Api\TagGroupController;
use App\Http\Controllers\Api\UploadCenterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UseruploadController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\VoucherPageController;
use App\Http\Controllers\Api\WatchHistoryController;
use App\Http\Controllers\Api\WebsiteSettingController;
use App\Http\Controllers\Auth\ApiLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RolePermissionController;
use App\PaymentModule\Controllers\RedirectAPIUserToPaymentRoute;
use App\PaymentModule\Controllers\RedirectUserToPaymentPage;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {

    // FAQs
    Route::resource('faqs', FaqController::class)->only(['index', 'show']);
    Route::get('faq', '\\'.FaqPageController::class)->name('api.v2.faq');

    // Debug routes
    Route::get('debug', [HomeController::class, 'debug'])->name('api.v2.debug');
    Route::get('satra', [HomeController::class, 'satra']);

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
    Route::post('checkUserAccess', [UserController::class, 'checkUserAccess']);
    Route::get('unknownUsersCityIndex',
        [UserController::class, 'unknownUsersCityIndex'])->name('user.index.unknown.city');

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
            \App\Http\Controllers\Api\BonyadEhsan\Admin\ProductController::class, 'selectOption',
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
        'store', 'show', 'update', 'destroy',
    ]);

    // Study Plan routes
    Route::resource('studyPlan', '\\'.StudyPlanController::class)->only(['index', 'update', 'show']);
    Route::prefix('studyPlan')->group(function () {
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

    // Watch History routes
    Route::post('unwatched', [WatchHistoryController::class, 'destroyByWatchableId']);

    // Sales Man routes
    Route::prefix('sales-man')->group(function () {
        Route::get('/', [SalesManController::class, 'index'])->name('sales-man.index');
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

    // Insert KMT route
    Route::post('insertKMT', [BotsController::class, 'queueBatchInsertJob'])->name('api.bot.pk');

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

    // Bots Controller Routes
    Route::post('sc/pen',
        [BotsController::class, 'sendCodeToUnknownNumberPen'])->name('api.v2.sendCodeToUnknownNumberPen');

    // Gateway Controller Route
    Route::get('/gateways', [GatewayController::class, 'index'])->name('api.v2.gateways');

    // Android Log Controller Route
    Route::group(['prefix' => 'androidLog'], function () {
        Route::get('failTrack', [AndroidLogController::class, 'failTrack']);
    });

    // Voip Controller Route
    Route::post('/voip_admin', [VoipController::class, 'sendUserToAdmin'])->name('api.voip_websocket_adminPannel');

    //Added Routs

    //Other
    Route::any('paymentRedirect/{paymentMethod}/{device}',
        '\\'.RedirectUserToPaymentPage::class)->name('redirectToBank'); //TODO:Check
    Route::get('user/{user}/dashboard', '\\'.DashboardPageController::class)->name('api.user.dashboard');
    Route::resource('batch-content-insert', '\\'.BatchContentInsertController::class)->only(['index', 'store']);
    Route::get('findByCode', [CouponController::class, 'findByCode'])->name('api.admin.coupon.findByCode');
    Route::post('marketing-report', [UserController::class, 'marketingReport'])->name('marketing-report');
    Route::get('complete-register', [UserController::class, 'completeRegister'])->name('completeRegister');
    Route::post('exchangeOrderproduct/{order}', [OrderController::class, 'exchangeOrderproduct']);
    Route::post('groupRegistration', [UserController::class, 'groupRegistration'])->name('api.groupRegistration');
    Route::resource('draft', '\\'.DraftController::class);
    Route::any('goToPaymentRoute/{paymentMethod}/{device}/',
        '\\'.RedirectAPIUserToPaymentRoute::class)->name('redirectToPaymentRoute');
    Route::any('user/editProfile/android/{data}',
        [UserController::class, 'redirectToProfile'])->name('redirectToEditProfileRoute');
    Route::get('h', '\\'.VoucherPageController::class)->name('web.voucher.submit.form');

    //Study Event
    Route::get('b/{studyEventName}', [StudyeventController::class, 'store'])->name('api.barname');
    Route::get('studyevent/{studyevent}/plansOfDate',
        [StudyeventController::class, 'whereIsTaftan'])->name('api.whereIsTaftan');

    //ping
    Route::get('php-ping', [HomeController::class, 'phpPing'])->name('api.phpPing');

    //submit Konkur Result
    Route::get('96', [UserController::class, 'submitKonkurResult']);
    Route::get('97', [UserController::class, 'submitKonkurResult']);
    Route::get('98', [UserController::class, 'submitKonkurResult'])->name('api.user.konkurResult.98');
    Route::get('99', [UserController::class, 'submitKonkurResult'])->name('api.user.konkurResult.99');
    Route::get('1400', [UserController::class, 'submitKonkurResult'])->name('api.user.konkurResult.1400');
    Route::get('1401', [UserController::class, 'submitKonkurResult'])->name('api.user.konkurResult.1401');

    //Live Conductors
    Route::prefix('live-conductors')->name('live-conductors.')->group(function () {
        Route::get('/', [LiveConductorController::class, 'index'])->name('index');
        Route::post('/report', [LiveConductorController::class, 'report'])->name('report');
    });

    //Marketing
    Route::group(['prefix' => 'marketing'], function () {
        Route::post('referalCode/use',
            [NetworkMarketingController::class, 'useCode'])->name('api.marketing.useReferalCode');
        Route::post('getPackScores',
            [NetworkMarketingController::class, 'getPackScores'])->name('api.marketing.getPackScores');
        Route::get('getYaldaDiscount',
            [SubscriptionController::class, 'getYaldaDiscount'])->name('api.marketing.getYaldaDiscount');
        Route::get('admin', [MarketingController::class, 'marketingAdmin'])->name('api.admin.marketing');
    });

    //translation
    Route::post('transactionToDonate/{transaction}', [TransactionController::class, 'convertToDonate']);
    Route::post('completeTransaction/{transaction}', [TransactionController::class, 'completeTransaction']);
    Route::post('myTransaction/{transaction}', [TransactionController::class, 'limitedUpdate']);
    Route::get('getUnverifiedTransactions',
        [TransactionController::class, 'getUnverifiedTransactions']); //TODO:Need to check

    //Website Setting
    Route::group(['prefix' => 'websiteSetting'], function () {
        Route::get('{Websitesetting}/showFaq',
            [WebsiteSettingController::class, 'showFaq'])->name('api.setting.faq.show');
        Route::post('{Websitesetting}/updateFaq',
            [WebsiteSettingController::class, 'updateFaq'])->name('api.setting.faq.update');
        Route::get('{Websitesetting}/editFaq/{faqId}',
            [WebsiteSettingController::class, 'editFaq'])->name('api.setting.faq.edit');
        Route::delete('{Websitesetting}/deleteFaq/{faqId}',
            [WebsiteSettingController::class, 'destroyFaq'])->name('api.setting.faq.delete');
    });

    //Some Resources

    // shahr
    Route::resource('shahr', ShahrController::class)->only('index');

    // attributegroup
    Route::resource('attributegroup', AttributegroupController::class)->except(['show', 'create']);

    // userupload
    Route::resource('userupload', UseruploadController::class)->except(['create', 'edit', 'destroy']);

    // phone
    Route::resource('phone', PhoneController::class)->only(['store', 'update', 'destroy']);

    // productfile
    Route::resource('productfile', ProductfileController::class)->except(['index', 'destroy', 'show']);

    // productphoto
    Route::resource('productphoto', ProductphotoController::class)->only(['store', 'destroy']);

    // city
    Route::resource('city', CityController::class)->only('index');

    // file
    Route::resource('file', FileController::class)->only(['store', 'destroy']);

    // section
    Route::resource('section', SectionController::class)->except('create');

    // periodDescription
    Route::resource('periodDescription', PeriodDescriptionController::class);

    // source
    Route::resource('source', SourceController::class)->except('create');

    // phonebook
    Route::resource('phonebook', PhoneBookController::class)->only(['index', 'store']);

    // phonenumber
    Route::resource('phonenumber', PhoneNumberController::class)->only(['index', 'store']);

    //List Pending Description
    Route::get('listPendingDescriptionContents',
        [ContentController::class, 'indexPendingDescriptionContent'])->name('api.c.list.pending.description.content');

    //Live
    Route::post('startlive', [LiveController::class, 'startLive'])->name('api.start.live');
    Route::post('endlive', [LiveController::class, 'endLive'])->name('api.end.live');

    //SMS
    Route::post('smsLink', [HomeController::class, 'smsLink'])->name('api.sms.link');
    Route::get('sms/{sms}/resend-bulk-sms',
        [SmsController::class, 'resendUnsuccessfulBulkSms'])->name('resend.unsuccessful.bulk.sms');
    Route::post('adminSendSMS', [SmsController::class, 'sendSMS'])->name('api.sendSmsendSms');
    Route::resource('smsUser', '\\'.SmsUserController::class)->only('index');
    Route::get('user/{user}/sms', [UserController::class, 'smsIndex'])->name('user.sms');

    //News Letter
    Route::resource('newsletter', '\\'.NewsletterController::class)->only(['store']);

    //Ajax
    Route::group(['prefix' => '/ajax'], routes: function () {

        Route::group(['prefix' => 'orderproduct'], function () {
            Route::post('batchExtensionRequest', [
                OrderproductController::class, 'batchExtensionRequest',
            ])->name('api.ajax.orderproduct.batchExtensionRequest');
            Route::post('batchExtend', [
                OrderproductController::class, 'batchExtend',
            ])->name('api.ajax.orderproduct.batchExtend');
        });
        Route::group(['prefix' => 'product'], function () {
            Route::post('{product}/attachRelation', [
                AdminProductController::class, 'attachRelation',
            ])->name('web.ajax.product.attach.relation');
            Route::delete('{product}/detachRelation', [
                AdminProductController::class, 'detachRelation',
            ])->name('web.ajax.product.detach.relation');
        });
    });

    //Analytics
    Route::group(['prefix' => 'analytics'], function () {
        Route::get('/abrisham', [AnalyticsController::class, 'abrisham'])->name('api.analytics.abrisham');
    });

    //Dana Check Token
    Route::get('check-dana-token', [DanaController::class, 'checkDanaToken'])->name('api.checkDanaToken');

    //map
    Route::get('map', '\\'.MapPageController::class)->name('api.map');

});
