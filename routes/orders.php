<?php

use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\GetPaymentRedirectEncryptedLink;
use App\Http\Controllers\Api\OfflinePaymentController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderproductController;
use App\Http\Controllers\Api\PaymentStatusController;
use App\Http\Controllers\Api\PaymentVerifierController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    // Checkout routes
    Route::group(['prefix' => 'checkout'], function () {
        Route::get('payment', [OrderController::class, 'checkoutPayment'])->name('api.v2.checkout.payment');
        Route::post('addDonate', [OrderController::class, 'addDonate']);
        Route::delete('removeDonate', [OrderController::class, 'removeDonate']);
    });

    // Payment redirect link
    Route::any('getPaymentRedirectEncryptedLink',
        '\\'.GetPaymentRedirectEncryptedLink::class)->name('api.v2.payment.getEncryptedLink');

    // Payment status routes
    Route::resource('paymentstatuses', '\\'.PaymentStatusController::class)->only(['index']);
    Route::post('bank-accounts', [BankAccountController::class, 'store'])->name('api.v2.bank-account.store');
    Route::get('bank-accounts', [BankAccountController::class, 'index'])->name('api.v2.bank-account.index');
    Route::post('wallet/withdraw', [WalletController::class, 'withdrawWallet'])->name('api.v2.wallet.withdraw');
    Route::get('wallet/withdraw-requests',
        [WalletController::class, 'withdrawRequests'])->name('api.v2.wallet.request');

    //Verify Payment Routs
    Route::group(['prefix' => 'verifyPayment'], function () {
        Route::group(['prefix' => 'online'], function () {
            Route::any('{paymentMethod}/{device}',
                [PaymentVerifierController::class, 'verify'])->name('verifyOnlinePayment');
            Route::any('{status}/{paymentMethod}/{device}',
                [PaymentStatusController::class, 'show'])->name('showOnlinePaymentStatus');
        });
        Route::any('offline/{paymentMethod}/{device}',
            [OfflinePaymentController::class, 'verifyPayment'])->name('verifyOfflinePayment');
    });

    //Billing
    Route::resource('billing', '\\'.BillingController::class)->only(['index']);

    //order product
    Route::group(['prefix' => 'orderproduct'], function () {
        Route::post('restore',
            [OrderproductController::class, 'restore'])->name('api.orderproduct.restore');
    });

    // Order routes
    Route::prefix('order')->group(function () {

        Route::post('detachorderproduct', [OrderController::class, 'detachOrderproduct']);
        Route::post('addOrderproduct/{product}', [OrderController::class, 'addOrderproduct']);
        Route::post('addProducts/{order}', [OrderController::class, 'addProducts'])->name('api.order.add.products');
        Route::get('get4kGift/{product}', [OrderController::class, 'add4kToArashOrder'])->name('api.order.get4kGift');
        Route::get('/upgradeOrder', [OrderController::class, 'upgrade'])->name('api.order.upgrade');
        Route::post('3a', [OrderController::class, 'create3aOrder'])->name('api.v2.order.3a');
        Route::post('freeSubscription',
            [OrderController::class, 'freeSubscription'])->name('api.v2.order.freeSubscription');
    });

    Route::get('purchaseCoupon', [OrderController::class, 'couponOrder'])->name('api.purchase.coupon');
    Route::post('/free', [OrderController::class, 'storeFree'])->name('api.ajax.order.store.free');

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

});
