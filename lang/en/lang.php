<?php
return [
    'plugin' => [
        'name' => 'MONEI',
        'description' => 'MONEI Payment Gateway',
    ],
    'fields' => [
        'is_enabled' => [
            'label' => 'Enabled',
            'comment' => 'Enable MONEI',
        ],
        'is_test_mode' => [
            'label' => 'Test Mode enabled',
            'comment' => 'Select this option for the initial testing required by MONEI, deselect this option once you pass the required test phase and your production environment is active.',
        ],
        'is_debug_mode' => [
            'label' => 'Debug Mode enabled',
            'comment' => 'Log MONEY events, such as notifications requests.',
        ],
        'title' => [
            'label' => 'Title',
            'comment' => 'This controls the title which the user sees during checkout.',
        ],
        'description' => [
            'label' => 'Description',
            'comment' => 'This controls the description which the user sees during checkout.',
        ],
        'logo' => [
            'label' => 'Logo',
            'comment' => 'Upload image logo.',
        ],
        'shop_name' => [
            'label' => 'Shop Name',
            'comment' => 'Your Shop Name',
        ],
        'url_callback' => [
            'label' => 'URL Callback',
            'comment' => 'URL to which a callback notification should be sent asynchronously.',
        ],
        'account_id' => [
            'label' => 'Account ID',
            'comment' => 'Your Account ID',
        ],
        'orderdo' => [
            'label' => 'What to do after payment?',
            'comment' => 'Chose what to do after the customer pay the order.',
            'options' => [
                'processing' => 'Mark as Processing (default & recomended)',
                'completed' => 'Mark as Complete',
            ],
        ],
        'order_class' => [
            'label' => 'Order Class',
            'comment' => 'Order Class to associate with payment.',
        ],
        'currency' => [
            'label' => 'Currency',
            'comment' => 'Currencies available',
        ],
        'password' => [
            'label' => 'Password',
            'comment' => 'MONEI Password',
        ],
        'id' => [
            'label' => 'Order ID',
        ],
        'first_name' => [
            'label' => 'First Name',
        ],
        'last_name' => [
            'label' => 'Last Name',
        ],
        'total' => [
            'label' => 'Total',
        ],
        'payment_date' => [
            'label' => 'Payment Date',
        ],
        'payment_status' => [
            'label' => 'Payment Status',
        ],
        'payment_status_msg' => [
            'label' => 'Payment Status Message',
        ],
        'checkout_id' => [
            'label' => 'Checkout ID',
        ],
    ],
    'tabs' => [
        'general' => 'General',
        'info' => 'Info',
    ],
    'settings' => [
        'label' => 'MONEI settings',
        'description' => 'Enter your MONEI settings here',
    ],
    'front' => [
        'receipt_text' => 'Thank you for your order, please click the button below to pay with Credit Card via MONEI.',
    ],
    'component' => [
        'payment_form' => [
            'details' => [
                'name' => 'MONEI Payment Form',
                'description' => 'Pay for order, redirecting to MONEI payment page',
            ],
            'url_cancel' => [
                'title' => 'Cancel Page',
                'description' => 'URL to which customers must be redirected when they wish to quit payment flow and return to the merchant\'s site.',
            ],
        ],
    ],
    'validation' => [
        'on_hold' => 'Validation error: Order vs. Notification amounts do not match (order: %1$s - received: %2$s).',
    ],
];