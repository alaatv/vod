<?php

namespace App\Listeners;

use App\Events\SendOrderNotificationsEvent;
use App\Jobs\AttachExamsToAbsrishamProUsersJob;
use App\Jobs\CalculateBilling;
use App\Jobs\CalculateUserCommission;
use App\Jobs\GiveGodarJob;
use App\Jobs\GiveToorRaheAbrishamRiyaziProducts;
use App\Jobs\GiveToorRaheAbrishamTajrobiProducts;
use App\Jobs\InterrelationProductsJob;
use App\Jobs\Register3AParticipantsJob;
use App\Models\Event;
use App\Models\Product;
use App\Notifications\AlaaKhooneTopic;
use App\Notifications\ProductChoiceAbrisham;
use App\Notifications\ProductChoiceArash1401;
use App\Notifications\SoalaaProductNotification;
use App\Repositories\NewsletterRepo;
use App\Repositories\ProductRepository;
use App\Traits\APIRequestCommon;
use App\Traits\Helper;

class  SendOrderNotificationsListener
{
    use APIRequestCommon;
    use Helper;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendOrderNotificationsEvent  $event
     * @return void
     */
    public function handle(SendOrderNotificationsEvent $event)
    {
        $myOrder = $event->myOrder;
        $user = $event->user;
        $notificationsBeSent = $event->notificationsBeSent;
        $sync = $event->sync;
        $orderproducts = $myOrder->orderproducts;

        $hasAbrishamRiyaziPack = $myOrder->raheAbrisham99RiyaziPack();

        $hasAbrishamTajrobiPack = $myOrder->raheAbrisham99TajrobiPack();

        $hasTooreRaheAbrishamTajrobi = $myOrder->tooreRaheAbrishamTajoribi1400();
        $hasTooreRaheAbrishamRiyazi = $myOrder->tooreRaheAbrishamRiyazi1400();
        $ReferralCode = $myOrder->referralCode;

        if ($sync) {
            dispatch(new InterrelationProductsJob($myOrder))->onConnection('sync');
        } else {
            dispatch(new InterrelationProductsJob($myOrder));
        }

        if (($hasAbrishamTajrobiPack || $hasAbrishamRiyaziPack) && $notificationsBeSent) {
            $user->notify(new ProductChoiceAbrisham('پک کامل دروس تخصصی راه ابریشم', 'راه ابریشم فیزیک آقای طلوعی',
                '1005', 'راه ابریشم فیزیک آقای کازرانیان', '1006'));
        }

        if ($hasTooreRaheAbrishamRiyazi) {
            dispatch(new GiveToorRaheAbrishamRiyaziProducts($myOrder));
        }

        if ($hasTooreRaheAbrishamTajrobi) {
            dispatch(new GiveToorRaheAbrishamTajrobiProducts($myOrder));
        }

        if ($myOrder->hasProduct(ProductRepository::getProductsById(Product::ALL_SINGLE_ABRISHAM_PRODUCTS)->pluck('id')->toArray())) {
            dispatch(new GiveGodarJob($myOrder, $notificationsBeSent));
        }

        if ($notificationsBeSent && $myOrder->hasProduct(Product::soalaaProducts()->pluck('id')->toArray())) {
            $user->notify(new SoalaaProductNotification());
        }

        if ($notificationsBeSent && $orderproducts->whereIn('product_id',
                [Product::ARASH_PACK_RIYAZI_1401, Product::ARASH_PACK_TAJROBI_1401])->isNotEmpty()) {
            $user->notify(new ProductChoiceArash1401('پک جمعبندی آرش'));
        }

        if ($notificationsBeSent && $orderproducts->whereIn('product_id', [
                Product::ARASH_TITAN_PACK_TAJROBI, Product::ARASH_TITAN_FIZIK, Product::ARASH_TITAN_PACK_RIYAZI
            ])->isNotEmpty()) {
            $user->notify(new ProductChoiceArash1401('جمعبندی آرش + تایتان'));
        }

        if ($notificationsBeSent && $orderproducts->where('product_id', Product::YEKROOZE_FIZIK_1401)->isNotEmpty()) {
            $user->notify(new AlaaKhooneTopic('رفع اشکال درس فیزیک', 'آلاخونه',
                'https://forum.alaatv.com/topic/51923'));
        }
        if ($notificationsBeSent && $orderproducts->where('product_id', Product::YEKROOZE_RIYAZI_1401)->isNotEmpty()) {
            $user->notify(new AlaaKhooneTopic('تاپیک رفع اشکال درس ریاضی', 'آلاخونه',
                'https://forum.alaatv.com/topic/51924'));
        }

        if ($notificationsBeSent && $orderproducts->whereIn('product_id', [
                Product::RAHE_ABRISHAM1401_PRO_PACK_TAJROBI, Product::RAHE_ABRISHAM1401_PRO_PACK_RIYAZI
            ])->isNotEmpty()) {
            $user->notify(new ProductChoiceAbrisham('پک کامل دروس تخصصی ابریشم پرو', 'راه ابریشم پرو آقای طلوعی',
                '2001', 'راه ابریشم پرو آقای کازرانیان', '2002'));
        }
        if ($notificationsBeSent && $orderproducts->where('product_id',
                Product::RAHE_ABRISHAM1402_PACK_TAJROBI)->isNotEmpty()) {
            $user->notify(new ProductChoiceAbrisham('پک تجربی ابریشم 2', 'ریاضیات تجربی ثابتی', '1001',
                ' ریاضیات تجربی نباخته', '1002'));
        }
        if ($notificationsBeSent && $orderproducts->where('product_id',
                Product::RAHE_ABRISHAM1402_PACK_RIYAZI)->isNotEmpty()) {
            $user->notify(new ProductChoiceAbrisham('پک ریاضی ابریشم 2', 'حسابان ثابتی', '2001', 'حسابان نباخته',
                '2002'));
        }

        if ($myOrder->abrishamPro()->isNotEmpty() || $myOrder->abrishamProTabdil()->isNotEmpty()) {
            dispatch(new AttachExamsToAbsrishamProUsersJob($myOrder->user));
        }


        Register3AParticipantsJob::dispatch($myOrder, $user);

        $hasEmtehanNahayi1401 = $orderproducts->whereIn('product_id', Product::EMTEHAN_NAHAYI_1401)->count();
        if ($hasEmtehanNahayi1401) {
            $newsletterData = [
                'mobile' => $user->mobile,
                'first_name' => $user->firstName,
                'last_name' => $user->lastName,
                'grade_id' => $user->grade_id,
                'major_id' => $user->major_id,
                'event_id' => Event::EMTEHAN_NAHAYI_1401,
            ];
            NewsletterRepo::createNewsletter($newsletterData);
        }

        if (!empty($ReferralCode)) {
            dispatch(new CalculateBilling([$myOrder->id]))->onConnection('sync');
            dispatch(new CalculateUserCommission($myOrder));
        }
    }
}
