<?php

namespace Database\Seeders\Other;

use App\Repositories\CouponRepo;
use Illuminate\Database\Seeder;

class YaldaCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $totalAmount = 50000 ;

        //discountCode = 40%    count=5000
        $fortyPercent = $totalAmount * 0.05 ;
        for($i = 0 ; $i < $fortyPercent  ; $i++)
        {
            CouponRepo::createBasicOveralCoupon( config('constants.EVENTS.COUPON').'-'.$i . '40'  , 40 , config('constants.EVENTS.COUPON') , null , null ,null , null , 0 , 0 );
        }

            //discountCode = 45%    count=30000
        $thirtyPercent = $totalAmount * 0.3 ;
        for($i =0  ; $i < $thirtyPercent ; $i++)
        {
            CouponRepo::createBasicOveralCoupon( config('constants.EVENTS.COUPON').'-'.$i . '45' , 45 , config('constants.EVENTS.COUPON') , null , null ,null , null , 0 , 0 );
        }

             //discountCode = 50%    count=35000
        $fiftyPercent = $totalAmount * 0.35;
        for($i = 0 ; $i < $fiftyPercent ; $i++)
        {
            CouponRepo::createBasicOveralCoupon( config('constants.EVENTS.COUPON').'-'.$i . '50' , 50 , config('constants.EVENTS.COUPON') , null , null ,null , null , 0 , 0 );
        }


        //discountCode = 55%    count=15000
        $fifteenPercent = $totalAmount * 0.19 ;
        for($i = 0 ; $i < $fifteenPercent ; $i++)
        {
            CouponRepo::createBasicOveralCoupon( config('constants.EVENTS.COUPON') .'-'.$i . '55', 55 , config('constants.EVENTS.COUPON') , null , null ,null , null , 0 , 0 );
        }

        //discountCode = 60%    count=5000
        $sixtyPercent = $totalAmount * 0.05;
        for($i = 0 ; $i < $sixtyPercent ; $i++)
        {
            CouponRepo::createBasicOveralCoupon( config('constants.EVENTS.COUPON').'-'.$i . '60' , 60 , config('constants.EVENTS.COUPON') , null , null ,null , null , 0 , 0 );
        }

        //discountCode = 70%    count=5000
        $seventyPercent = $totalAmount * 0.05;
        for($i = 0 ; $i < $seventyPercent ; $i++)
        {
            CouponRepo::createBasicOveralCoupon( config('constants.EVENTS.COUPON').'-'.$i . '70' , 70 , config('constants.EVENTS.COUPON') , null , null ,null , null , 0 , 0 );
        }

        //discountCode = 100%    count=5000
        $oneHundredPercent = $totalAmount * 0.01 ;
        for($i = 0 ; $i < $oneHundredPercent ; $i++)
        {
            CouponRepo::createBasicOveralCoupon( config('constants.EVENTS.COUPON').'-'.$i . '100' , 100 , config('constants.EVENTS.COUPON') , null , null ,null , null , 0 , 0 );
        }
    }
}
