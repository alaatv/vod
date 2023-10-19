<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefactorOrderProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:refactor:orderproducts {--force}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refactor old order product';
    private $giftProduct;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->giftProduct = [
            Product::RAHE_ABRISHAM1401_PRO_ZIST,
            Product::RAHE_ABRISHAM1401_PRO_RIYAZI_TAJROBI,
            Product::RAHE_ABRISHAM1401_PRO_SHIMI,
            Product::RAHE_ABRISHAM1401_PRO_RIYAZIYAT_RIYAZI,
            ...Product::ALL_ABRISHAM_PRO_PRODUCTS_OMOOMI
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::channel('debug')->debug('Running alaaTv:refactor:orderproducts');
        $orders =
            Order::query()->whereIn('orderstatus_id', [
                config('constants.ORDER_STATUS_CLOSED'), config('constants.ORDER_STATUS_POSTED')
            ])->where('paymentstatus_id',
                config('constants.PAYMENT_STATUS_INDEBTED'))->whereHas('transactions')->with('transactions')->get();

        $count = $orders->count();
        if (!$this->option('force') && !$this->confirm("{$count} Order products found. Do you wish to continue?",
                true)) {
            $this->info('Aborted!');
            return false;
        }

        $this->output->progressStart($count);
        /** @var Order $order */
        foreach ($orders as $order) {
            $orderProducts =
                $order->orderproducts->whereIn('orderproducttype_id',
                    [config('constants.ORDER_PRODUCT_TYPE_DEFAULT'), config('constants.ORDER_PRODUCT_HIDDEN')])
                    ->whereNull('instalmentQty');
            DB::transaction(function () use ($orderProducts, $order) {
                $orderProducts->each(function ($orderProduct) {
                    /** @var OrderProduct $orderProduct */
                    $transactions = $orderProduct->order->transactions()
                        ->where('cost', '>', 0)->where((function ($query) {
                            $query->where('paymentmethod_id', '!=', config('constants.PAYMENT_METHOD_WALLET'))
                                ->orWhereNull('paymentmethod_id');
                        }))
                        ->whereIn('transactionstatus_id', [
                            config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                            config('constants.TRANSACTION_STATUS_UNPAID')
                        ]);
                    $totalTransactions = $transactions->count();
                    $totalPaidTransactions = $transactions->successful()->count();
                    /*  if ($orderProduct->order->id == 69) {
                          $transactions = $orderProduct->order->transactions()
                              ->where('cost', '>', 0)->where((function ($query)  {
                                  $query->where('paymentmethod_id', '!=', config('constants.PAYMENT_METHOD_WALLET'))
                                      ->orWhereNull('paymentmethod_id');
                              }))->whereIn("transactionstatus_id", [config('constants.TRANSACTION_STATUS_SUCCESSFUL'), config('constants.TRANSACTION_STATUS_UNPAID')])
                              ->get();
                          $totalTransactions = $transactions->count();

                      }*/

                    if ($totalTransactions) {
                        if (in_array($orderProduct->product_id, [756, 757] + $this->giftProduct)) {
                            $instalmentQty = [40, 30, 30];
                        } else {
                            $instalmentQty = array_fill(0, $totalTransactions, (int) (100 / $totalTransactions));
                            $instalmentQty[0] += 100 - array_sum($instalmentQty);
                        }
                        $paidPercent =
                            array_sum(array_slice($instalmentQty, 0, $totalPaidTransactions));
                        $orderProduct->instalmentQty = $instalmentQty;
                        $orderProduct->paidPercent = $paidPercent;
                        $orderProduct->updateWithoutTimestamp();
                    }
                });
                $order->isInInstalment = 1;
                $order->updateWithoutTimestamp();
                $this->output->progressAdvance();


            });
        }

        $this->output->progressFinish();
        $this->info("\n".'Done!');
        $this->newLine();
        return 0;
    }
}
