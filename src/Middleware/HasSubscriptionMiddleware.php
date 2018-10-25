<?php

namespace Flitty\Subscription\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\UnauthorizedException;

class HasSubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     * Get provided subscription type ids.
     * If user has one of that subscription type access has been granted
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param                           $subscriptionTypes - identificators example string - '1|2|3|4'
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $subscriptionTypes)
    {
        $user = Auth::user();
        $subscriptionTypeIds = explode('|', $subscriptionTypes);
        if (!$user) {
            throw new UnauthorizedException('User is not authorized');
        }
        foreach ($subscriptionTypeIds as $subscriptionTypeId) {
            if ($user->getTypeSubscription($subscriptionTypeId)) {
                return $next($request);
            }
        }
        Session::flash('error', 'Subscribe to get full access');
        return redirect(url(config('subscription.cancel_url')));
    }
}
