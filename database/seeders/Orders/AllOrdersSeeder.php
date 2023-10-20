<?php

namespace Database\Seeders\Orders;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AllOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Order::truncate();
        Orderproduct::truncate();
        Transaction::truncate();
        Coupon::truncate();
        $this->call([
            CanceledOrderSeeder::class,
            ClosedAndATMPaidOrderSeeder::class,
            ClosedAndPaidOrderSeeder::class,
            ClosedAndPaidOrderWithCouponSeeder::class,
            ClosedAndUnpaidOrderSeeder::class,
            ClosedATMAndOnlinePaidOrderSeeder::class,
            ClosedOnePaidOrderWithInstalment::class,
            ClosedUnpaidOrderWithInstalment::class,
            ClosedUnPaidOrderWithApprovedInstalment::class,
            ClosedAndPaidOrderWithPercentageDiscountSeeder::class,
            ClosedAndPaidOrderWithDiscountAndCouponSeeder::class,
            ClosedAndPaidOrderWithDiscountAndPartialCouponSeeder::class,
            ClosedAndPaidOrderWithManagerWithManagerComments::class,
            ClosedAndPaidOrderWithCustomerDescription::class,
            ClosedAndWalletPaidOrderSeeder::class,
            ClosedWalletAndOnlinePaidOrderSeeder::class,
            ClosedWalletAndAtmPaidOrderSeeder::class,
            ClosedAndPaidOrderWithReferralCodeSeeder::class,
            ClosedAndPaidOrderWithNoneProfitableReferralCodeSeeder::class,
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
