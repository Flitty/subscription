<?php
/**
 * Created by PhpStorm.
 * User: nikolaygolub
 * Date: 04.10.2018
 * Time: 16:41
 */

namespace Flitty\Subscription\Traits;


use Illuminate\Database\Eloquent\Builder;
use Flitty\Subscription\Contracts\SubscriberContract;

/**
 * Trait Subscribable
 * @property $primaryKey - model primary key
 * @package Subscription\Traits
 */
trait Subscribable
{
    /**
     * Returns entities available by selected subscription
     *
     * @param Builder            $query
     * @param SubscriberContract $subscriber - entity with potential subscription
     *
     * @return Builder
     */
    public function scopeSubscribable(Builder $query, SubscriberContract $subscriber)
    {
        if (!$subscriber->getTypeSubscription($this->getSubscriptionTypeId())) {
            return $query->where($this->primaryKey, null);
        }
        return $query;
    }

    /**
     * Set current subscription type id
     *
     * @param int $entityTypeId
     */
    public function setSubscriptionTypeId(int $entityTypeId)
    {
        $this->{config('subscription.type.foreign')} = $entityTypeId;
    }

    /**
     * Get current subscription type id
     *
     * @return int - subscription type id
     */
    public function getSubscriptionTypeId() : int
    {
        return $this->{config('subscription.type.foreign')};
    }
}