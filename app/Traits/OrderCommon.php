<?php namespace App\Traits;



use App\Collection\AttributevalueCollection;
use App\Jobs\GiveSubscriptoinWalletCredit;
use App\Models\Attribute;
use App\Models\Attributevalue;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\InvoicePaid;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;


trait OrderCommon
{
    /**
     * @param  int  $orderId
     * @param     $wallets
     */
    public function withdrawWalletPendings(int $orderId, $wallets): void
    {
        /** @var Wallet $wallet */
        foreach ($wallets as $wallet) {
            if (!($wallet->balance > 0 && $wallet->pending_to_reduce > 0)) {
                continue;
            }
            $withdrawResult = $wallet->withdraw($wallet->pending_to_reduce, $orderId);
            if ($withdrawResult['result']) {
                $wallet->update([
                    'pending_to_reduce' => 0,
                ]);
            }

        }
    }

    public function checkCouponRequirements($coupon, $user)
    {
        if (is_null($coupon->required_products)) {
            return true;
        }

        foreach ($coupon->required_products as $ar) {
            if ($this->searchProductInUserAssetsCollection2(Product::find($ar), $user)) {
                return true;
            }
        }

        return false;
    }

    public function checkCouponUnrequirements($coupon, $user)
    {
        if (is_null($coupon->unrequired_products)) {
            return true;
        }

        foreach ($coupon->unrequired_products as $ar) {
            if ($this->searchProductInUserAssetsCollection2(Product::find($ar), $user)) {
                return false;
            }
        }

        return true;
    }

    protected function canPayOrderByWallet(User $user, int $cost)
    {
        $canPayByWallet = false;
        $wallets =
            $user->wallets->sortByDesc('wallettype_id'); //Chon mikhastim aval az kife poole hedie kam shavad!

        /** @var Wallet $wallet */
        foreach ($wallets as $wallet) {
            if ($cost <= 0) {
                break;
            }

            $amount = $wallet->balance;
            if ($amount <= 0) {
                continue;
            }

            if ($cost < $amount) {
                $amount = $cost;
            }

            $canWithDraw = $wallet->canWithdraw($amount);
            if (!$canWithDraw) {
                continue;
            }
            $wallet->pending_to_reduce = $amount;
            if ($wallet->update()) {
                $cost = $cost - $amount;
                $canPayByWallet = true;
            }
        }

        return [
            'result' => $canPayByWallet,
            'cost' => $cost,
        ];
    }

    /**
     * @param  string  $paymentMethod
     * @param  string  $device
     * @param  string  $encryptedPostfix
     *
     * @return string
     */
    private function getEncryptedUrl(string $paymentMethod, string $device, string $encryptedPostfix)
    {
        $parameters = [
            'paymentMethod' => $paymentMethod,
            'device' => $device,
            'encryptionData' => $encryptedPostfix,
        ];

        return URL::temporarySignedRoute(
            'redirectToPaymentRoute',
            3600,
            $parameters
        );
    }

    /**
     * @param  bool  $hadSubscriptions
     * @param  array  $responseMessages
     * @param  Order  $myOrder
     * @param  int  $hasRaheAbrisham
     * @param  bool  $hasTooreRaheAbrirham
     *
     * @return array
     */
    private function makeResponseMessage(Order $myOrder): array
    {
        $hadSubscriptions = $this->addSubscriptions($myOrder);
        $hasTooreRaheAbrirham = $myOrder->tooreRaheAbrisham1400();
        $hasRaheAbrisham =
            $myOrder->orderproducts->whereIn('product_id', array_keys(Product::ALL_ABRISHAM_PRODUCTS))->count();
        $hasEmtehanNahayi1401 = $myOrder->orderproducts->whereIn('product_id', Product::EMTEHAN_NAHAYI_1401)->count();

        if (isset($myOrder) && $myOrder->seller == config('constants.ALAA_SELLER')) {
            optional($myOrder->user)->notify(new InvoicePaid($myOrder,
                ($hasRaheAbrisham || $hasTooreRaheAbrirham) ? route('web.user.asset.abrisham') : null));
        }

        $responseMessages = [];

        if ($hasEmtehanNahayi1401) {
            $assetLink = '<a href="'.route('web.landing.30').'" class="btn btn-info m-btn--pill m-btn--air m-btn animated infinite heartBeat">
                                دانلود امتحان نهایی
                                </a>';
            $responseMessages[] = 'برای دانلود امتحان نهایی به صفحه روبرو بروید: '.$assetLink;

            return $responseMessages;
        }

        if ($hadSubscriptions) {
            $responseMessages[] = '<div class="alert alert-warning" role="alert">
                                            <strong>اشتراک شما فعال شد</strong>
                                            </div>';
            if ($myOrder->orderproducts->count() > 1) {
                $assetLink = '<a href="'.route('web.user.asset').'" class="btn btn-info m-btn--pill m-btn--air m-btn animated infinite heartBeat">
                                دانلودهای من
                                </a>';
                $responseMessages[] = 'برای دانلود محصولاتی که خریده اید به صفحه روبرو بروید: '.$assetLink;
                $responseMessages[] =
                    'توجه کنید که محصولات پیش خرید شده در تاریخ مقرر شده برای دانلود قرار داده می شوند';
            }

            return $responseMessages;
        }

        if ($myOrder->seller == config('constants.SOALAA_SELLER')) {
            $responseMessages[] = '<div class="alert alert-warning" role="alert">
                                            <strong>به سایت سؤالا مراجعه کنید  <a href="https://soalaa.com/user_exam_list">گلیک کنید</a></strong>
                                            </div>';

            return $responseMessages;
        }

        if ($hasRaheAbrisham || $hasTooreRaheAbrirham) {
            $assetLink =
                '<a href="'.route('web.user.asset.abrisham').'" class="btn btn-info m-btn--pill m-btn--air m-btn animated infinite heartBeat">داشبورد راه ابریشم</a>';
            $responseMessages[] = 'برای استفاده از محصولات راه ابریشم خود به '.$assetLink.' بروید.';

            return $responseMessages;
        }

        $assetLink = '<a href="'.route('web.user.asset').'" class="btn btn-info m-btn--pill m-btn--air m-btn animated infinite heartBeat">
                                دانلودهای من
                                </a>';
        $responseMessages[] = 'برای دانلود محصولاتی که خریده اید به صفحه روبرو بروید: '.$assetLink;
        $responseMessages[] =
            'توجه کنید که محصولات پیش خرید شده در تاریخ مقرر شده برای دانلود قرار داده می شوند';
        return $responseMessages;
    }

    public function addSubscriptions(Order $order)
    {
        $user = $order->user;
        $done = false;

        $subscriptionOrderproducts = $order->getSubscriptionOrderproduct();
        if ($subscriptionOrderproducts->isEmpty()) {
            return $done;
        }

        /** @var Orderproduct $subscriptionOrderproduct */
        foreach ($subscriptionOrderproducts as $subscriptionOrderproduct) {

            /** @var AttributevalueCollection $subscriptionAttributeValues */
            $subscriptionAttributeValues = $subscriptionOrderproduct->product->attributevaluesByType(config('constants.ATTRIBUTE_TYPE_SUBSCRIPTION'));
            $subscriptionDurationAttribute = $subscriptionAttributeValues->pullSubscriptionAttributevalue(Attribute::SUBSCRIPTION_DURATION_ATTRIBUTE);
            if (!isset($subscriptionDurationAttribute)) {
                continue;
            }

            $values = [];
            /** @var Attributevalue $subscriptionAttributeValue */
            foreach ($subscriptionAttributeValues->whereNotNull('values') as $subscriptionAttributeValue) {
                $attributeName = optional(optional($subscriptionAttributeValue)->attribute)->name;
                if (!isset($attributeName)) {
                    continue;
                }

                $values[$attributeName] = $subscriptionAttributeValue->values;
            }
            ////////////////////////////////////////////////////////

            $month = 1;
            if ($subscriptionDurationAttribute->name == '12') {
                $month = 3;
            } elseif ($subscriptionDurationAttribute->name == '48') {
                $month = 12;
            }

            $user->subscribedProducts()->attach($subscriptionOrderproduct->product_id, [
                'order_id' => $order->id,
                'values' => json_encode($values),
                'valid_since' => Carbon::now('Asia/Tehran'),
                'valid_until' => Carbon::now('Asia/Tehran')->addMonths($month),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $credit = optional(Arr::get($values, 'wallet'))->credit_amount;
            if (!isset($credit)) {
                Log::channel('giveWalletCredit')->info('No credit amount found in attributevalue. user :'.$user->id);
                continue;
            }

            dispatch(new GiveSubscriptoinWalletCredit($user, $credit));
//            $user->notify(new TelescopeFCM($subscriptionOrderproduct->product->name));

            $done = true;
        }

        return $done;
    }

    /*
    * private function handleWalletChargingCanceledPayment(Transaction $transaction)
   {
       $transaction->transactionstatus_id = config('constants.TRANSACTION_STATUS_UNSUCCESSFUL');
       $transaction->update();
   }

   private function handleWalletChargingSuccessPayment(string $refId, Transaction $transaction, string $cardPanMask = null)
   {
       $bankAccountId = null;
       if (isset($cardPanMask)) {
           $bankAccount = Bankaccount::firstOrCreate(['accountNumber' => $cardPanMask]);
           $bankAccountId = $bankAccount->id;
       }
       $this->changeTransactionStatusToSuccessful($refId, $transaction, $bankAccountId);
       $transaction->wallet->deposit($transaction->cost * (-1), true);
   }*/
}
