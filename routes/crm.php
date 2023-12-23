<?php

use App\Http\Controllers\Api\Admin\CouponController;
use App\Http\Controllers\Api\ReferralCodesController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketDepartmentController;
use App\Http\Controllers\Api\TicketMessageController;
use App\Http\Controllers\Api\TicketPriorityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| V2
|--------------------------------------------------------------------------
*/
Route::prefix('v2')->group(function () {
    // Ticket routes
    Route::resource('ticket', '\\'.TicketController::class)->except(['edit']);
    Route::group(['prefix' => 'ticket'], function () {
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

    // Coupon Routes
    Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
        Route::resource('', '\\'.CouponController::class)->except(['create', 'edit']);
        Route::get('findByCode', [CouponController::class, 'findByCode'])->name('findByCode');
        Route::post('generateMassiveRandomCoupon',
            [CouponController::class, 'generateMassiveRandomCoupon'])->name('massive.random');
    });
    Route::post('/savePenaltyCoupon', [CouponController::class, 'savePenaltyCoupon'])->name('save.penalty.coupon');

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
});
