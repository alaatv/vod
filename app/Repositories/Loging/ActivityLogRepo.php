<?php


namespace App\Repositories\Loging;
use App\Models\Activity;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ReferralRequest;
use App\Models\Report;
use App\Models\User;
use App\Repositories\AlaaRepo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class ActivityLogRepo extends AlaaRepo implements \Spatie\Activitylog\Contracts\Activity
{
    use LogBlock;

    public static function LogAddProductToOrder($authUser, $subject, $owner, $product)
    {
        activity()
            ->performedOn($subject)
            ->causedBy($authUser)
            ->withProperties(['product_id' => $product, 'user_id' => $owner])
            ->useLog(config('activitylog.log_names.add_product'))
            ->log(config('activitylog.description.create'));
    }

    public static function LogAddGiftToOrder($authUser, $subject, $owner, $product)
    {
        activity()
            ->performedOn($subject)
            ->causedBy($authUser)
            ->withProperties(['gift_id' => $product, 'user_id' => $owner])
            ->useLog(config('activitylog.log_names.add_gift'))
            ->log(config('activitylog.description.create'));
    }

    public static function LogCouponCreation($causer, $subject, $products = [])
    {
        $properties = self::SetCouponProperties($subject, $products);
        activity()
            ->performedOn($subject)
            ->causedBy($causer)
            ->withProperties($properties)
            ->useLog(config('activitylog.log_names.add_coupon'))
            ->log(config('activitylog.description.create'));
    }

    private static function SetCouponProperties(Coupon $coupon, $products): array
    {
        $properties = [
            'products' => $products
        ];
        foreach (Coupon::LOG_ATTRIBUTES as $attribute) {
            $properties[$attribute] = $coupon->{$attribute};
        }
        return $properties;
    }

    public static function logBlockChanges($block, $changes)
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($block)
            ->createdAt(now())
            ->withProperties($changes)
            ->useLog(config('activitylog.log_names.block_content_update'))
            ->log(config('activitylog.description.edit'));
    }

    public static function logReport(User $user, ?Report $report)
    {
        if (is_null($report)) {
            return;
        }
        activity()
            ->causedBy($user)
            ->performedOn($report)
            ->createdAt(now())
            ->withProperties([])
            ->useLog(config('activitylog.log_names.report'))
            ->log(config('activitylog.description.create'));
    }

    public static function LogItemsAddedToOrder($authUser, $subject, $owner, $gifts = [], $noGifts = [])
    {
        $log = self::findCreatedOrder($authUser, $subject);
        $properties = self::SetOrderProperties($subject);

        if ($gifts) {
            $properties['added_gifts'] = $gifts;
            unset($properties['products']);
        }
        if ($noGifts) {
            $properties['added_products'] = $noGifts;
            unset($properties['products']);
        }
        if ($log) {
            $log->update([
                'properties' => $properties
            ]);
        } else {
            activity()
                ->performedOn($subject)
                ->causedBy($authUser)
                ->withProperties($properties)
                ->useLog(config('activitylog.log_names.add_product'))
                ->log(config('activitylog.description.create'));
        }

    }

    private static function findCreatedOrder($authUser, $subject)
    {
        return Activity::query()
            ->where('subject_type', get_class($subject))
            ->where('subject_id', $subject->id)
            ->where('causer_id', $authUser->id)
            ->whereJsonContains('properties', ['owner' => "{$subject->user_id}", 'products' => ''])
            ->first();
    }

    private static function SetOrderProperties($order): array
    {
        $properties = [
            'owner' => $order->user_id,
            'products' => '',
        ];
        foreach (Order::LOG_ATTRIBUTES as $attribute) {
            $properties[$attribute] = $order->{$attribute};
        }
        return $properties;
    }

    public static function LogAddOrder($causer, $subject)
    {
        $properties = self::SetOrderProperties($subject);
        activity()
            ->performedOn($subject)
            ->causedBy($causer)
            ->withProperties($properties)
            ->useLog(config('activitylog.log_names.add_order_by_admin'))
            ->event('created')
            ->log(config('activitylog.description.create'));
    }

    public static function getModelClass(): string
    {
        return Activity::class;
    }

    public static function filter(
        string $subject = null,
        int $subjectId = null,
        string $event = null,
        User $causer = null,
        array $relations = []
    ) {
        $query = self::initiateQuery()->with($relations);

        if (isset($subject)) {
            $query->where('subject_type', $subject);
        }
        if (isset($subjectId)) {
            $query->where('subject_id', $subjectId);
        }
        if (isset($event)) {
            $query->where('event', $event);
        }

        if (isset($causer)) {
            $query->causedBy($causer);
        }

        return $query;
    }

    public static function logBonyadEhsanUserRegistration(User $causer, User $newBonyadUser)
    {
        activity()
            ->causedBy($causer)
            ->performedOn($newBonyadUser)
            ->createdAt(now())
            ->withProperties([])
            ->useLog(config('activitylog.log_names.bonyad_user'))
            ->event('created')
            ->log(config('activitylog.description.create'));
    }

    public static function referralCodesGenerated(User $causer, ReferralRequest $referralRequest)
    {
        activity()
            ->performedOn($referralRequest)
            ->causedBy($causer)
            ->useLog(config('activitylog.log_names.referral_code'))
            ->event('created')
            ->log(config('activitylog.description.create'));
    }

    public function subject(): MorphTo
    {
        // TODO: Implement subject() method.
    }

    public function causer(): MorphTo
    {
        // TODO: Implement causer() method.
    }

    public function getExtraProperty(string $propertyName, mixed $defaultValue): mixed
    {
        // TODO: Implement getExtraProperty() method.
    }

    public function changes(): Collection
    {
        // TODO: Implement changes() method.
    }

    public function scopeInLog(Builder $query, ...$logNames): Builder
    {
        // TODO: Implement scopeInLog() method.
    }

    public function scopeCausedBy(Builder $query, Model $causer): Builder
    {
        // TODO: Implement scopeCausedBy() method.
    }

    public function scopeForEvent(Builder $query, string $event): Builder
    {
        // TODO: Implement scopeForEvent() method.
    }

    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        // TODO: Implement scopeForSubject() method.
    }
}
