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
            'comment' => 'Log MONEI events, such as notifications requests.',
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
            'label' => 'Shop Name'
        ],
        'order_data' => [
            'label' => 'Order Data',
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
        'id' => [
            'label' => 'Order ID',
        ],
        'order_id_full' => [
            'label' => 'Order ID',
        ],
        'name' => [
            'label' => 'Name',
        ],
        'email' => [
            'label' => 'E-mail',
        ],
        'total_full' => [
            'label' => 'Total',
        ],
        'created_at' => [
            'label' => 'Created at',
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
        'transaction_id' => [
            'label' => 'Transaction ID',
        ],
        'refund_btn' => [
            'label' => 'Make Refund',
        ],
        'refunds' => [
            'label' => 'Refunds',
        ],
        'amount' => [
            'label' => 'Amount',
        ],
        'date' => [
            'label' => 'Date',
        ],
        'api_key' => [
            'label' => 'API Key',
            'comment' => 'API Key for production use',
        ],
        'api_key_test' => [
            'label' => 'Test API Key',
            'comment' => 'API Key for testing',
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
            'url_complete' => [
                'title' => 'Complete Page',
                'description' => 'URL to which customers must be redirected after payment is completed.',
            ],
        ],
        'success_page' => [
            'details' => [
                'name' => 'MONEI Order Success Page',
                'description' => 'Receive order success data and display order info.',
            ],
            'order_id' => [
                'title' => 'Order ID',
                'description' => 'To get Order object.',
            ],
        ],
    ],
    'validation' => [
        'on_hold' => 'Validation error: Order vs. Notification amounts do not match (order: %1$s - received: %2$s).',
    ],
];