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

class AttachExamsToAbrishamProUsersCommand extends Command
{
    use APIRequestCommon;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:abrishamPro:examAttach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'attach exams to abrisham pro users';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $abrishamProProductIds = array_keys(Product::ALL_ABRISHAM_PRO_PRODUCTS);
        $abrishamTabdilProductIds = Product::ALL_ABRISHAM_PRO_TABDIL;
        $totalProducts = array_merge($abrishamProProductIds, $abrishamTabdilProductIds);
        $examProductIds =
            ProductRepository::_3aExamsProductsIds()->isChild()->whereHas('children')->idFrom(803)->idTo(952)->pluck('id')->toArray(); //3a exams

        $quizProuctExamIds =
            ProductRepository::_3aExamsProductsIds()->whereIn('id', [961, 960])->pluck('id')->toArray(); //quiz exams

        $examProductIds = array_merge($examProductIds, $quizProuctExamIds);

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
        })->orWhereHas('orders', function ($q0) use ($totalProducts) {
            $q0->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'))
                ->whereHas('orderproducts', function (Builder $query) use ($totalProducts) {
                    $query
                        ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
                        ->whereIn('product_id', $totalProducts);
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
            $hadPurchasedSome = false;
            foreach ($examProductIds as $examProductId) {
                if (in_array($examProductId, $userPurchasedProductIds)) {
                    $hadPurchasedSome = true;
                    continue;
                }
                $orderProductData[] = $examProductId;
            }

            if (!empty($orderProductData)) {
                if (count($orderProductData) != 14 && !$hadPurchasedSome) {
                    Log::channel('debug')->error("In AttachExamsToAbrishamProUsersCommand : count of orderproducts for user {$user->id} is not 14");
                }

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
                        Log::channel('debug')->error('In AttachExamsToAbrishamProUsersCommand :Product '.$productId.', Exam '.$exam->id.' was not registered for user '.$user->id);
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
