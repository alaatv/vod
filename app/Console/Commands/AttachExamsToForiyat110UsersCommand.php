<?php

namespace App\Console\Commands;

use App\Models\_3aExam;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Repositories\ProductRepository;
use App\Traits\APIRequestCommon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AttachExamsToForiyat110UsersCommand extends Command
{
    use APIRequestCommon;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:foriyat110:examAttach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'attach exams to foriyat 110 users';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $totalProducts = Product::where('category', 'VIP110')->get()->pluck('id')->toArray();
        $examProductIds =
            ProductRepository::_3aExamsProductsIds()->whereIn('id', [957, 958, 959])->pluck('id')->toArray();

        $abrishamProPurchasers = User::query()->whereHas('orders', function ($q0) use ($totalProducts) {
            $q0->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
                ->whereHas('orderproducts', function (Builder $query) use ($totalProducts) {
                    $query
                        ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
                        ->whereIn('product_id', $totalProducts);
                })->whereHas('transactions', function ($q2) {
                    $q2->successful()
                        ->where('cost', '>', 0)
                        ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'));
                });
        })->distinct()->get();

        $count = $abrishamProPurchasers->count();
        if (!$this->confirm("$count tabdil abrisham users found , continue?")) {
            return false;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $userIds = [];
        foreach ($abrishamProPurchasers as $user) {
            if (in_array($user->id, $userIds)) {
                continue;
            }
            $userIds [] = $user->id;

            $userPurchasedProductIds = $user->getUserProductsId();
            $orderProductData = [];
            foreach ($examProductIds as $examProductId) {
                if (in_array($examProductId, $userPurchasedProductIds)) {
                    continue;
                }
                $orderProductData[] = $examProductId;
            }

            if (!empty($orderProductData)) {
                $order = OrderRepo::createBasicCompletedOrder($user->id, config('constants.PAYMENT_STATUS_PAID'), 0, 0);
                foreach ($orderProductData as $orderProductDatum) {
                    OrderproductRepo::createGiftOrderproduct($order->id, $orderProductDatum, 0);
                }
            }

            foreach ($examProductIds as $productId) {
                $exams = _3aExam::productId($productId)->get();
                foreach ($exams as $exam) {
                    $result = $this->register3ARequest($user, $exam->id);
                    if (!$result) {
                        Log::channel('debug')->error('In AttachExamsToForiyat110UsersCommand : Product '.$productId.', Exam '.$exam->id.' was not registered for user '.$user->id);
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
        Artisan::call('cache:clear');
        $this->info('Done!');
        return false;
    }
}
