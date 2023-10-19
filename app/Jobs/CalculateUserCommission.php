<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\WalletService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CalculateUserCommission implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Order $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(WalletService $walletService)
    {
        $orderBillings = $this->order->billings()->where('op_f_category_id', '!=',
            3)->whereDoesntHave('commission')->get();
        if ($orderBillings->count()) {
            $referralRequest = $this->order->referralCode->referralRequest;
            $totalCommission = 0;
            $userCommissionsCollection = $orderBillings->map(function ($billing) use (
                $referralRequest,
                &
                $totalCommission
            ) {
                $totalCommission += $billing->op_share_amount * ($referralRequest->default_commission / 100);
                return [
                    'owner_id' => $referralRequest->owner_id,
                    'orderProduct_id' => $billing->op_id,
                    'payment_transaction_id' => $billing->t_id,
                    'commision' => $referralRequest->default_commission,
                ];
            });
            DB::transaction(function () use (
                $userCommissionsCollection,
                $referralRequest,
                $totalCommission,
                $walletService
            ) {
                $user = $referralRequest->owner;
                $userCommissions = $user->commissions()->createMany($userCommissionsCollection->toArray());
                $wallets = $walletService->getWalletsByUserId($user->id);
                $userMainWallet = array_filter_collapse($wallets['data'], function ($wallet) {
                    return $wallet['withdrawable'] == true;
                });
                if (!isset($userMainWallet['id'])) {
                    throw new Exception('main user wallet not exists for user commission');
                }

                $transactionResult = $walletService->createTransactionForWalletByUserId(
                    $user->id,
                    $userMainWallet['id'],
                    (int) round($totalCommission),
                    config('constants.USER_COMMISSION_REASON'),
                    config('constants.USER_COMMISSION_REASON_TYPE'),
                    config('constants.USER_COMMISSION_DESCRIPTION')
                );
                if ($transactionResult['status_code'] == 201) {
                    foreach ($userCommissions as $userCommission) {
                        $userCommission->update([
                            'transaction_id' => $transactionResult['data']['id'],
                        ]);
                    }
                } else {
                    throw new Exception("can't add commission to user wallet");
                }
            });
        }
    }
}
