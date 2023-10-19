<?php

namespace Database\Seeders;

use Database\Seeders\Coupons\CouponSeeder;
use Database\Seeders\Orders\AllOrdersSeeder;
use Database\Seeders\Permission\AssignPermissionToRole;
use Database\Seeders\ReferralCode\MakeReferralCodeForAdmin;
use Database\Seeders\Tickets\TicketSeeder;
use Database\Seeders\Users\AllUsersSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {

//        $this->call(PhoneNumberProviderSeeder::class);
//        $this->call(ReportStatusesSeeder::class);
//        $this->call(ReportTypeSeeder::class);
//        $this->call(FinancialCategoriesSeeder::class);
//        $this->call(YaldaCouponSeeder::class);
//        $this->call(SeaRolePermissionSeeder::class);
//        $this->call(KunkurReginsSeeder::class);
//        $this->call(MajorSeeder::class);

//        $this->call(SetsTableSeeder::class);
//        $this->call(BlocksTableSeeder::class);
//        $this->call(WebsitePageSeeder::class);
//        $this->call(BannerSeeder::class);
//        $this->call(FakeOrdersSeeder::class);
//        $this->call(BlockTypesSeeder::class);
//        $this->call(TagsGroupsSeeder::class);
//        $this->call(TagsSeeder::class);
//        $this->call(SmsResultSeeder::class);
//        $this->call(SmsProvidersSeeder::class);
//        $this->call(LotteryStatusesSeeder::class);


        /*        $this->call(AssignmentStatusSeeder::class);
                $this->call(ConsultationStatusSeeder::class);
                $this->call(MajorSeeder::class);
                $this->call(OrderStatusSeeder::class);
                $this->call(PaymentMethodSeeder::class);
                $this->call(PaymentStatusSeeder::class);
                $this->call(PermissionSeeder::class);
                $this->call(ProductTypeSeeder::class);
                $this->call(RoleSeeder::class);
                $this->call(UserStatusSeeder::class);
                $this->call(TransactionStatusSeeder::class);
                $this->call(AttributeTypeSeeder::class);
                $this->call(GenderSeeder::class);
                $this->call(TransactionGatewaysSeeder::class);
                $this->call(BonSeeder::class);
                $this->call(UserBonStatusSeeder::class);
                $this->call(AttributeControlSeeder::class);
                $this->call(UserUploadStatusSeeder::class);
                $this->call(ContacttypeSeeder::class);
                $this->call(PhonetypeSeeder::class);
                $this->call(RelativeSeeder::class);
                $this->call(CouponTypeSeeder::class);
                $this->call(OnlineTransactionGatewaysSeeder::class);
                $this->call(ProductFileTypeSeeder::class);
                $this->call(VerificationMessageStatusSeeder::class);
                $this->call(WebsitePageSeeder::class);
                $this->call(MajorInterrelationTypeSeeder::class);
                $this->call(MajorTypeSeeder::class);
                $this->call(ContentTypeInterrelationSeeder::class);
                $this->call(GradeSeeder::class);
                $this->call(CheckoutStatusSeeder::class);
                $this->call(OrderproductTypeSeeder::class);
                $this->call(OrderproductInterrelationSeeder::class);
                $this->call(TransactionInterrelationSeeder::class);
                $this->call(ProductInterrelationSeeder::class);
                $this->call(BloodTypeSeeder::class);
                $this->call(DiscountTypeSeeder::class);
                $this->call(WallettypeSeeder::class);
                $this->call(BannersBlockSeeder::class);
                $this->call(EventParticipantGroupsSeeder::class);
                $this->call(ReasonsOfLockedOrderproductsSeeder::class);
        */
        $this->call([
            AllUsersSeeder::class,
            AllOrdersSeeder::class,
            CouponSeeder::class,
            ProductvoucherSeeder::class,
//            BonyadSeeder::class,
            ContentStatusSeeder::class,
            AssignPermissionToRole::class,
            MakeReferralCodeForAdmin::class,
            MainBankAccountSeeder::class,
            FavorableSeeder::class,
            TicketSeeder::class,
            HardshipSeeder::class,
            ServiceSeeder::class,
            TransactionGatewaysSeeder::class,
        ]);
    }
}
