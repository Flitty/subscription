<h1>Simple Subscription Wrap.</h1>

<h2>Installation</h2>

<p>1. Require package in your progect</p>
<pre>composer require flitty/subscription</pre>

<p>2. Register package service provider in your app (app.providers)</p>
<pre>Flitty\Subscription\Providers\SubscriptionServiceProvider::class</pre>

<p>3. Puplish migrations and the package config file</p>
<pre>php artisan vendor:publish --provider=SubscriptionServiceProvider</pre>

<p>4. Create tables</p>
<pre>php artisan migrate</pre>

<p>5. Configure callback link in config file. Default value is:</p>
<pre>'express-checkout-success' => '/subscription/express-checkout-success?driver=pay-pal-subscription',</pre>

<p>5. Add Your subscription types into `subscription_types` table</p>

<p>6. Set up subscriber entity</p>
<p>Register Subscriber trait and contract into Users Model</p>
<pre>use Flitty\Subscription\Traits\Subscriber;</pre>
<pre>implement Flitty\Subscription\Contracts\SubscriberContract</pre>

<p>7. Set up subscription entity</p>
<p>Register Subscription entity trait and contract into subscription entity Model</p>
<pre>use Flitty\Subscription\Traits\Subscribable;</pre>
<pre>implement Flitty\Subscription\Contracts\SubscribableEntityContract</pre>
<p>Subscription entity should has `subscription_type_id` (you can change field name in config 'subscription.type.foreign')</p>

<p>8. You can add coupones in `subscription_coupons` table</p>

<p>9. Register middlewares. Update your 'app/Http/Kernel.php' class. Add the following line to $routeMiddleware</p>
<pre>'subscribed' => HasSubscriptionMiddleware::class,</pre>

<h2>Usage</h2>

<h4>In controller</h4>
<p>Your controller should has at less 3 methods </p>
<pre>public function expressCheckout(ExpressCheckoutRequest $request)
{
    try {
        $response = Auth::user()->subscribeEntity(
            $request->get('subscriptionTypeId'),
            $request->get('coupon'),
            $request->get('driver')
        );
    } catch (UserAlreadyHasTheSubscription $e) {
        Session::flash('error', $e->getMessage());
        $response = redirect()->route('subscription-route-name');
    } catch (SubscriptionRedirectHasBeenFailedException $e) {
        Session::flash('error', $e->getMessage());
        $response = redirect()->route('subscription-route-name');
    }
    return $response;
}
</pre>
<pre>
     public function expressCheckoutSuccess(Request $request)
     {
         try {
             app($request->get('driver'))->subscriptionCallback($request->get('token'));
             Session::flash('notice', Message::getMessageData(Message::SUBSCRIBED_SUCCESSFULLY));
         } catch (SubscriptionCallbackHasBeenFailedException $e) {
             Session::flash('error', $e->getMessage());
         }
         return redirect()->route('subscription-route-name');
     }
</pre>
<pre>  
     public function payPalCallback(Request $request)
     {
         $requestData = $request->all();
         try {
             app(SubscriptionServiceProvider::PAY_PAL_DRIVER)->cmdCallback($requestData);
             $message = 'User has been updated his subscription';
         } catch (SubscriptionHasNotBeenFoundException $e) {
             $message = $e->getMessage();
         } catch (InvalidResponseException $e) {
             $message = $e->getMessage();
         }
         logger()->info($message, $requestData);
         return response()->json(['message' => $message]);
     }
</pre>

<p>Optional methods:</p>
<pre>    
    public function cancelSubscription(CancelSubscriptionRequest $request)
    {
        $type = 'error';
        try {
            Auth::user()->cancelSubscription($request->get('subscriptionTypeId'), $request->get('driver'));
            $message = 'User has been canceled subscription successfully';
            $type = 'notice';
        } catch (UserHasNoSubscriptionException $e) {
            $message = $e->getMessage();
        } catch (CancelSubscriptionHasBeenFailed $e) {
            $message = $e->getMessage();
        }
        Session::flash($type, $message);
        return redirect()->route('settings');
    }
 </pre>
<pre>
    public function suspendSubscription(SuspendSubscriptionRequest $request)
    {
        $type = 'error';
        try {
            Auth::user()->suspendSubscription($request->get('subscriptionTypeId'), $request->get('driver'));
            $message = 'User has been suspended subscription successfully';
            $type = 'notice';
        } catch (UserHasNoSubscriptionException $e) {
            $message = $e->getMessage();
        } catch (CancelSubscriptionHasBeenFailed $e) {
            $message = $e->getMessage();
        }
        Session::flash($type, $message);
        return redirect()->route('settings');
    }
</pre>
<pre>
    public function reactivateSubscription(ReactivateSubscriptionRequest $request)
    {
        $type = 'error';
        try {
            Auth::user()->reactivateSubscription($request->get('subscriptionTypeId'), $request->get('driver'));
            $message = 'User has been reactivated subscription successfully';
            $type = 'notice';
        } catch (UserHasNoSubscriptionException $e) {
            $message = $e->getMessage();
        } catch (CancelSubscriptionHasBeenFailed $e) {
            $message = $e->getMessage();
        }
        Session::flash($type, $message);
        return redirect()->route('settings');
    }
</pre>

<h4>Get subscription entities</h4>
<p>Get all entities of authentificated user</p>
<pre>
    $subscriber = Auth::user();
    $entities = Entity::subscribable($subscriber)->get();
</pre>

<h4>Middleware&Routing</h4>
<p>You can limit the access to routes by using middlewares</p>
<pre>
    Route::get('subscription-entities', 'EntityController@index')->middleware('subscribed:{id of the subscription type}');
</pre>

<h4>Get Subscriber subscriptions</h4>

<p>Get authentificated user subscription type</p>
<pre>
    $subscriber = Auth::user();
    $subscriber->getTypeSubscription($subscriptionTypeId);
</pre>

<p>Get all authentificated user subscriptions</p>
<pre>
    $subscriber = Auth::user();
    $subscriber->getSubscriptions();
</pre>
