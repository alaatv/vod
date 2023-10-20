<?php

namespace App\Console\Commands;

use App\Exports\DefaultClassExport;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserThatHasTwoGroupProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:userHasProducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User That Has Two Group Products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $productGroup1 = Product::ALL_CHATR_NEJAT2_PRODUCTS;
        ksort($productGroup1);
        $productGroupId1 = array_keys($productGroup1);
        $productGroup2 = Product::ALL_ABRISHAM_PRO_PRODUCTS;
        ksort($productGroup2);
        $productGroupId2 = array_keys($productGroup2);
        $headers = ['نام و نام خانوادگی', 'آیدی', 'موبایل'];
        $headers =
            array_merge($headers, array_column(array_merge($productGroup1, $productGroup2), 'lesson_name'));

        $usersThatByGroup1 = \DB::table('users')->whereNull('users.deleted_at')
            ->join('orders', function ($join) {
                $join->on('orders.user_id', '=', 'users.id');
            })->join('orderproducts', function ($join) {
                $join->on('orderproducts.order_id', '=', 'orders.id');
            })
            ->where('orders.paymentstatus_id', '=', config('constants.PAYMENT_STATUS_PAID'))
            ->where('orders.orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
            ->whereNull('orders.deleted_at')
            ->whereNull('orderproducts.deleted_at')
            ->where('orderproducts.orderproducttype_id', '=', 1)
            ->whereIn('orderproducts.product_id', $productGroupId1)
            ->select('users.id', 'orderproducts.product_id', 'orders.id as order_id', 'users.firstName',
                'users.lastName', 'users.mobile');

        $usersThatByGroup2 = \DB::table('users')->whereNull('users.deleted_at')
            ->join('orders', function ($join) {
                $join->on('orders.user_id', '=', 'users.id');
            })->join('orderproducts', function ($join) {
                $join->on('orderproducts.order_id', '=', 'orders.id');
            })
            ->where('orders.paymentstatus_id', '=', config('constants.PAYMENT_STATUS_PAID'))
            ->where('orders.orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
            ->whereNull('orders.deleted_at')
            ->whereNull('orderproducts.deleted_at')
            ->where('orderproducts.orderproducttype_id', '=', 1)
            ->whereIn('orderproducts.product_id', $productGroupId2)
            ->select('users.id', 'orderproducts.product_id', 'orders.id as order_id', 'users.firstName',
                'users.lastName', 'users.mobile');
        $merge = DB::query()->fromSub($usersThatByGroup1, 'g1')
            ->joinSub($usersThatByGroup2, 'g2', function ($join) {
                $join->on('g1.id', '=', 'g2.id');
            })->select(
                'g1.id',
                'g1.firstName',
                'g1.lastName',
                'g1.mobile',
                'g1.product_id as g1_p_id',
                'g2.product_id as g2_p_id',
            )->distinct();
        $merge = $merge->get()->toArray();
        $result = [];
        $allProducts = array_unique(array_merge($productGroupId1, $productGroupId2));
        foreach ($merge as $item) {
            foreach ($allProducts as $product) {
                ${'p_'.$product} = 0;
            }
            ${'p_'.$item->g1_p_id} = 1;
            ${'p_'.$item->g2_p_id} = 1;

            $r = [
                'user' => $item->firstName.' '.$item->lastName,
                'id' => $item->id,
                'mobile' => $item->mobile,
            ];
            foreach ($allProducts as $product) {
                $r += array_merge($r, [
                    'p_'.$product => (int) (array_get(array_get($result, $item->id, []), 'p_'.$product,
                            0) || ${'p_'.$product})
                ]);
            }

            $result[$item->id] = $r;
        }

        $disk = config('disks.GENERAL');
        $now = now('Asia/Tehran')->format('YmdHis');
        $fileName = "report_users_with_chatr2_products_bought_pro_products_$now.xlsx";
        Excel::store(new DefaultClassExport(collect($result), $headers), $fileName, $disk);
        return 0;
    }
}
