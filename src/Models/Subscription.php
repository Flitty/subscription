<?php

namespace Flitty\Subscription\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Subscription\Models\Subscription
 *
 * @property int $id
 * @property string $expire_at
 * @property int $user_id
 * @property string|null $type
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Flitty\Subscription\Models\SubscriptionCoupon $subscriptionCoupon
 * @property-read \Flitty\Subscription\Models\SubscriptionType $subscriptionType
 * @method static \Illuminate\Database\Query\Builder|\Flitty\Subscription\Models\Subscription onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereExpireAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Flitty\Subscription\Models\Subscription withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\Flitty\Subscription\Models\Subscription withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $subscription_coupon_id
 * @property int $subscription_type_id
 * @property string $status
 * @property string|null $recurring_payment_id
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereRecurringPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereSubscriptionCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereSubscriptionTypeId($value)
 * @property string|null $suspended_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereSuspendedAt($value)
 * @property string $driver
 * @property-read \App\User $subscriber
 * @method static \Illuminate\Database\Eloquent\Builder|\Flitty\Subscription\Models\Subscription whereDriver($value)
 */
class Subscription extends Model
{
    use SoftDeletes;

    const LIVE_STATUS = 'Live';
    const CANCELED_STATUS = 'Canceled';
    const PROCESSED_STATUS = 'Processed';
    const EXPIRED_STATUS = 'Expired';
    const SUSPENDED_STATUS = 'Suspended';

    protected $table = 'subscriptions';
    protected $fillable = [
        'suspended_at',
        'subscription_coupon_id',
        'subscription_type_id',
        'recurring_payment_id',
        'expire_at',
        'status',
        'user_id',
        'driver',
    ];
    protected $primaryKey = 'id';

    /**
     * Relation with coupons
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriptionCoupon()
    {
        return $this->belongsTo(
            config('subscription.coupon.model'),
            config('subscription.coupon.foreign'),
            config('subscription.coupon.own')
        );
    }


    /**
     * Relation to subscription type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriptionType()
    {
        return $this->belongsTo(
            config('subscription.type.model'),
            config('subscription.type.foreign'),
            config('subscription.type.own')
        );
    }

    /**
     * Calculate expire date after suspension
     *
     * @return Carbon
     */
    public function suspendExpireDiff() : Carbon
    {
        $expireAt = Carbon::parse($this->expire_at);
        $suspendedAt = Carbon::parse($this->suspended_at);
        $dateDiff = $suspendedAt->diff($expireAt);
        return Carbon::now()->add($dateDiff);
    }

    /**
     * Get subscriber
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriber()
    {
        return $this->belongsTo(
            config('subscription.subscriber.model'),
            config('subscription.subscriber.foreign'),
            config('subscription.subscriber.own')
        );
    }

}
