<?php
return [
    'drivers' => [
        'PayPal' => [
            'mode'    => 'sandbox', // Can only be 'sandbox' Or 'live'. If empty or invalid, 'live' will be used.
            'sandbox' => [
                'username'    => env('PAYPAL_SANDBOX_API_USERNAME', ''),
                'password'    => env('PAYPAL_SANDBOX_API_PASSWORD', ''),
                'secret'      => env('PAYPAL_SANDBOX_API_SECRET', ''),
                'certificate' => env('PAYPAL_SANDBOX_API_CERTIFICATE', ''),
                'app_id'      => 'APP-80W284485P519543T', // Used for testing Adaptive Payments API in sandbox mode
            ],
            'live' => [
                'username'    => env('PAYPAL_LIVE_API_USERNAME', ''),
                'password'    => env('PAYPAL_LIVE_API_PASSWORD', ''),
                'secret'      => env('PAYPAL_LIVE_API_SECRET', ''),
                'certificate' => env('PAYPAL_LIVE_API_CERTIFICATE', ''),
                'app_id'      => '', // Used for Adaptive Payments API
            ],

            'payment_action' => 'Sale', // Can only be 'Sale', 'Authorization' or 'Order'
            'currency'       => 'AUD',
            'billing_type'   => 'MerchantInitiatedBilling',
            'notify_url'     => '', // Change this accordingly for your application.
            'locale'         => '', // force gateway language  i.e. it_IT, es_ES, en_US ... (for express checkout only)
            'validate_ssl'   => false, // Validate SSL when creating api client.
            'express-checkout-success' => '/subscription/express-checkout-success?driver=pay-pal-subscription',
        ]
    ],
    'cancel_url' => '/settings',
    'currency'       => 'USA',
    'subscription' => [
        'model' => Flitty\Subscription\Models\Subscription::class,
        'foreign' => 'subscription_id',
        'own' => 'id'
    ],
    'transaction' => [
        'model' => Flitty\Subscription\Models\Transaction::class,
        'foreign' => 'transaction_id',
        'own' => 'id'
    ],
    'subscription_activity' => [
        'model' => Flitty\Subscription\Models\SubscriptionActivity::class,
        'foreign' => 'transaction_activity_id',
        'own' => 'id'
    ],
    'coupon' => [
        'model' => Flitty\Subscription\Models\SubscriptionCoupon::class,
        'foreign' => 'subscription_coupon_id',
        'own' => 'id'
    ],
    'type' => [
        'model' => Flitty\Subscription\Models\SubscriptionType::class,
        'foreign' => 'subscription_type_id',
        'own' => 'id'
    ],
    'subscriber' => [
        'model' => \App\User::class,
        'foreign' => 'user_id',
        'own' => 'id'
    ]
];