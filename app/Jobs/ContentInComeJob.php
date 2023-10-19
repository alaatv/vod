<?php

namespace App\Jobs;

use App\Models\ContentIncome;
use App\Models\ContentIncome;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Transactiongateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ContentInComeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $queue;

    private $order;
    /**
     * @var bool
     */
    private $update;

    /**
     * Create a new job instance.
     *
     * @param $order
     * @param  bool  $update
     */
    public function __construct(Order $order, bool $update = false)
    {
        $this->queue = 'default2';
        $this->order = $order;
        $this->update = $update;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->update ? $this->processUpdate() : $this->processCreate();
    }

    private function processUpdate()
    {
        $transactions = $this->order->transactions;
        $orderproducts = $this->order->orderproducts;
        foreach ($orderproducts as $orderproduct) {
            $shareCost = $orderproduct->setShareCost();

            foreach ($transactions as $transaction) {

                foreach (optional($orderproduct->product)->sets as $set) {

                    $paidContents = optional($set->contents)->where('isFree', 0);
                    $paidContentsCount = $paidContents->count();
                    $eachContentShareCost = $paidContentsCount ? bcdiv($shareCost / $paidContentsCount, 1, 2) : 0;
                    $eachContentShareCost = isset($eachContentShareCost) ? $eachContentShareCost : 0;

                    foreach (optional($set->contents)->where('isFree', 0) as $content) {

                        $contentInCome = $content->contentInCome
                            ->firstWhere('orderproduct_id', $orderproduct->getKey());
                        if (!isset($contentInCome)) {
                            continue;
                        }

                        $gateway = $transaction->transactiongateway;
                        $gatewayDisplayName = optional($gateway)->displayName;

                        $contentInCome->update([
                            'tmp_gateway' => $gatewayDisplayName,
                            'share_cost' => $shareCost,
                            'share_cost_gw' => optional($gateway)->id == Transactiongateway::GATE_WAY_MELLAT_ID ? $eachContentShareCost : 0,
                            'transaction_completed_at' => $transaction->completed_at
                        ]);

                    }
                }
            }

        }
    }

    private function processCreate()
    {
        $orderproducts = $this->order->orderproducts;
        foreach ($orderproducts as $orderproduct) {
            $shareOfCost = $orderproduct->setShareCost();
            if ($shareOfCost == 0) {
                continue;
            }

            $orderproductFinalCost = $orderproduct->price['final'];
            if ($orderproductFinalCost == 0) {
                continue;
            }

            $transactions = $this->order->transactions()
                ->whereNull('wallet_id')
                ->where('cost', '>', 0)
                ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'))
                ->get();

            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {

                $shareCost = $shareOfCost * $transaction->cost;

                $sets = optional($orderproduct->product)->sets;
                foreach ($sets as $set) {

                    $paidContents = optional($set->contents)->where('isFree', 0);
                    $paidContentsCount = $paidContents->count();
                    $eachContentShareCost = $paidContentsCount ? bcdiv($shareCost / $paidContentsCount, 1, 2) : 0;
                    $eachContentShareCost = isset($eachContentShareCost) ? $eachContentShareCost : 0;
                    foreach ($paidContents as $content) {
                        DB::transaction(function () use (
                            $content,
                            $transaction,
                            $shareCost,
                            $orderproduct,
                            $eachContentShareCost
                        ) {
                            $gateway = $transaction->transactiongateway;
                            $gatewayDisplayName = optional($gateway)->displayName;
                            ContentIncome::query()->firstOrCreate([
                                'content_id' => $content->getKey(),
                                'orderproduct_id' => $orderproduct->getKey(),
                                'transaction_id' => $transaction->getKey(),
                            ], [
                                'tmp_gateway' => $gatewayDisplayName,
                                'share_cost' => $eachContentShareCost,
                                'share_cost_gw' => optional($gateway)->id == Transactiongateway::GATE_WAY_MELLAT_ID ? $eachContentShareCost : 0,
                                'transaction_completed_at' => $transaction->completed_at
                            ]);
                        });
                    }
                }
            }
        }
    }
}
