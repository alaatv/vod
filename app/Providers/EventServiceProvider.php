<?php

namespace App\Providers;

use App\Events\Authenticated;
use App\Events\ChangeDanaStatus;
use App\Events\ContentRedirected;
use App\Events\FavoredTimePoint;
use App\Events\FavoriteEvent;
use App\Events\FreeInternetAccept;
use App\Events\GetLiveConductor;
use App\Events\LogSendBulkSmsEvent;
use App\Events\MobileVerified;
use App\Events\Plan\PlanSyncContentsEvent;
use App\Events\Plan\StudyPlanSyncContentsEvent;
use App\Events\ResendUnsuccessfulBulkMessageEvent;
use App\Events\ResendUnsuccessfulMessageEvent;
use App\Events\SendOrderNotificationsEvent;
use App\Events\UnFavoredContent;
use App\Events\UnfavoriteEvent;
use App\Events\UnsuccessfulMessageNotifyEvent;
use App\Events\UpdateSentSmsStatusEvent;
use App\Events\UserAvatarUploaded;
use App\Events\UserPurchaseCompleted;
use App\Events\UserRedirectedToPayment;
use App\Listeners\AttachLiveConductorToUser;
use App\Listeners\AuthenticatedListener;
use App\Listeners\BonyadEhsanEventSubscriber;
use App\Listeners\ChangeDanaStatusListener;
use App\Listeners\FavoriteEventListener;
use App\Listeners\FreeInternetAcceptListener;
use App\Listeners\LogNotification;
use App\Listeners\LogSendBulkSmsListener;
use App\Listeners\MakeContentAsFavored;
use App\Listeners\MakeTicketEntekhabReshteListener;
use App\Listeners\MakeTimePointsAsUnFavored;
use App\Listeners\MobileVerifiedListener;
use App\Listeners\Plan\SyncContentsToPlanListener;
use App\Listeners\Plan\SyncContentsToStudyPlanListener;
use App\Listeners\RedirectContentListener;
use App\Listeners\RegisteredListener;
use App\Listeners\RemoveOldUserAvatarListener;
use App\Listeners\ResendUnsuccessfulBulkMessageListener;
use App\Listeners\ResendUnsuccessfulMessageListener;
use App\Listeners\SendOrderNotificationsListener;
use App\Listeners\UnfavoriteEventListener;
use App\Listeners\UnlockProductContent;
use App\Listeners\UnsuccessfulMessageNotifyListener;
use App\Listeners\UpdateSentSmsStatusListener;
use App\Listeners\UserPurchaseCompletedListener;
use App\Listeners\UserRedirectedToPaymentListener;
use App\Models\Event;
use App\Observers\EventObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Events\RefreshTokenCreated;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Authenticated::class => [
            AuthenticatedListener::class,
        ],
        Registered::class => [
            //            SendMobileVerificationNotification::class,
            //            SendEmailVerificationNotification::class,
            RegisteredListener::class,
        ],
        MobileVerified::class => [
            MobileVerifiedListener::class,
        ],
        UserPurchaseCompleted::class => [
            UserPurchaseCompletedListener::class,
            UnlockProductContent::class,
        ],
        FreeInternetAccept::class => [
            FreeInternetAcceptListener::class,
        ],
        AccessTokenCreated::class => [
//            'App\Listeners\RevokeOldTokens',
        ],

        RefreshTokenCreated::class => [
//            'App\Listeners\PruneOldTokens',
        ],
        UserAvatarUploaded::class => [
            RemoveOldUserAvatarListener::class,
        ],
        ContentRedirected::class => [
            RedirectContentListener::class,
        ],
        UserRedirectedToPayment::class => [
            UserRedirectedToPaymentListener::class,
        ],
        FavoriteEvent::class => [
            FavoriteEventListener::class,
        ],
        UnfavoriteEvent::class => [
            UnfavoriteEventListener::class,
        ],
        NotificationSent::class => [
            LogNotification::class,
        ],
        LogSendBulkSmsEvent::class => [
            LogSendBulkSmsListener::class,
        ],
        UnsuccessfulMessageNotifyEvent::class => [
            UnsuccessfulMessageNotifyListener::class,
        ],
        ResendUnsuccessfulMessageEvent::class => [
            ResendUnsuccessfulMessageListener::class,
        ],
        ResendUnsuccessfulBulkMessageEvent::class => [
            ResendUnsuccessfulBulkMessageListener::class,
        ],
        PlanSyncContentsEvent::class => [
            SyncContentsToPlanListener::class,
        ],
        StudyPlanSyncContentsEvent::class => [
            SyncContentsToStudyPlanListener::class,
        ],
        SendOrderNotificationsEvent::class => [
            SendOrderNotificationsListener::class,
            MakeTicketEntekhabReshteListener::class
        ],
        UpdateSentSmsStatusEvent::class => [
            UpdateSentSmsStatusListener::class,
        ],
        FavoredTimePoint::class => [
            MakeContentAsFavored::class,
        ],
        UnFavoredContent::class => [
            MakeTimePointsAsUnFavored::class,
        ],
        ChangeDanaStatus::class => [
            ChangeDanaStatusListener::class
        ],
        GetLiveConductor::class => [
            AttachLiveConductorToUser::class,
        ],

    ];

    protected $subscribe = [
        BonyadEhsanEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::observe(EventObserver::class);
        parent::boot();
    }
}
