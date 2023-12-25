<?php

use App\Http\Controllers\Api\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Api\Admin\AttributeController;
use App\Http\Controllers\Api\Admin\AttributeSetController;
use App\Http\Controllers\Api\Admin\BlockController as AdminBlockController;
use App\Http\Controllers\Api\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Api\Admin\CouponController;
use App\Http\Controllers\Api\Admin\EmployeeScheduleController as AdminEmployeeScheduleController;
use App\Http\Controllers\Api\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Api\Admin\SetController as AdminSetController;
use App\Http\Controllers\Api\Admin\SlideshowController as AdminSlideshowController;
use App\Http\Controllers\Api\Admin\TransactionController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\CacheController;
use Illuminate\Support\Facades\Route;

//=================================== Admin Routes ============================================
Route::prefix('v2')->group(function () {
    Route::group(['middleware' => 'auth:api', 'prefix' => 'admin'], function () {

        // Admin-Clear Cache
        Route::get('cache-clear', '\\'.CacheController::class)
            ->name('admin.cache-clear');

        // Admin-Manage Transactions
        Route::resource('transaction', '\\'.TransactionController::class);

        // Admin-Manage Attributes
        Route::resource('attribute', '\\'.AttributeController::class);

        // Admin-Manage Attribute Sets
        Route::resource('attribute-set', '\\'.AttributeSetController::class);

        // Admin-FAQs
        Route::group(['prefix' => 'faq', 'as' => 'admin.faq.'], function () {
            Route::get('/', [AdminFaqController::class, 'index'])
                ->name('index');
            Route::post('/', [AdminFaqController::class, 'store'])
                ->name('store');
            Route::put('/{faq}', [AdminFaqController::class, 'update'])
                ->name('update');
            Route::delete('/{faq}', [AdminFaqController::class, 'delete'])
                ->name('delete');
        });

        // Admin-Block Routes
        Route::resource('block', '\\'.AdminBlockController::class)->except(['create', 'edit']);
        Route::patch('/block/{block}/syncProducts',
            [AdminBlockController::class, 'syncProducts'])->name('syncProducts');
        Route::patch('/block/{block}/syncSets', [AdminBlockController::class, 'syncSets'])->name('syncSets');
        Route::patch('/block/{block}/syncBanners', [AdminBlockController::class, 'syncBanners'])->name('syncBanners');
        Route::patch('/block/{block}/syncContents',
            [AdminBlockController::class, 'syncContents'])->name('syncContents');

        // Admin-Content Routes
        Route::resource('contents', '\\'.AdminContentController::class, ['as' => 'api'])->only([
            'index', 'show', 'update',
        ]);
        Route::get('c', [AdminContentController::class, 'index'])->name('c.index');
        Route::post('contents/destroy', [AdminContentController::class, 'destroy'])->name('content.bulk.destroy');
        Route::post('content/{content}/copy', [AdminContentController::class, 'copy'])->name('content.copy');

        // Admin-Set Routes
        Route::resource('set', '\\'.AdminSetController::class, ['as' => 'api']);
        Route::post('set/{set}/c/attach', [AdminSetController::class, 'attachContents'])->name('set.attachContents');
        Route::get('set/{set}/contents', [AdminSetController::class, 'contents'])->name('set.contents');

        // Admin-Product Routes
        Route::post('product/set-discount',
            [AdminProductController::class, 'setDiscount'])->name('product.set-discount');
        Route::put('product/bulk-update-statuses',
            [AdminProductController::class, 'bulkUpdateStatuses'])->name('product.bulk-update-statuses');
        Route::resource('product', '\\'.AdminProductController::class, ['as' => 'api'])->except(['create', 'edit']);
        Route::get('product/{product}/sets', [AdminProductController::class, 'sets'])->name('product.sets');
        Route::post('product/{product}/copy', [AdminProductController::class, 'copy'])->name('product.copy');

        // Admin-User Routes
        Route::resource('user', '\\'.AdminUserController::class, ['as' => 'api'])->except(['create', 'edit']);

        // Admin-Permission Routes
        Route::resource('permission', '\\'.AdminPermissionController::class, ['as' => 'api'])->except([
            'create', 'edit',
        ]);

        // Coupon Routes
        Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
            Route::resource('', '\\'.CouponController::class)->except(['create', 'edit']);
            Route::get('findByCode', [CouponController::class, 'findByCode'])->name('findByCode');
            Route::post('generateMassiveRandomCoupon',
                [CouponController::class, 'generateMassiveRandomCoupon'])->name('massive.random');
        });
        Route::post('/savePenaltyCoupon', [CouponController::class, 'savePenaltyCoupon'])->name('save.penalty.coupon');

        // Admin-Role Routes
        Route::resource('role', '\\'.AdminRoleController::class, ['as' => 'api'])->except(['create', 'edit']);

        // Admin-Order Routes
        Route::post('orderBatchTransfer',
            [AdminOrderController::class, 'orderBatchTransfer'])->name('order.batchTransfer');
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
    });
});
