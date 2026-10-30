<?php

/*
|--------------------------------------------------------------------------
| Gardener business settings
|--------------------------------------------------------------------------
| Everything here can be changed from .env without touching code.
*/

return [

    // Business name shown on invoices, e-mails and SMS
    'business_name' => env('BUSINESS_NAME', 'Gardener'),

    // Money
    'currency' => env('CURRENCY', 'BDT'),
    'currency_symbol' => env('CURRENCY_SYMBOL', '৳'),

    // Floating WhatsApp button (international format, digits only, e.g. 8801846650807)
    'whatsapp_number' => env('WHATSAPP_NUMBER', '8801846650807'),

    // How many bookings each time slot can take when no team members exist yet
    'default_slot_capacity' => (int) env('DEFAULT_SLOT_CAPACITY', 1),

    /*
    | SMS
    | driver "log"       → messages are only written to storage/logs (default)
    | driver "bulksmsbd" → sent through bulksmsbd.net
    */
    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
        'api_key' => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID'),
        'url' => env('SMS_API_URL', 'http://bulksmsbd.net/api/smsapi'),
    ],

    /*
    | SSLCommerz online payment (bKash, Nagad, Rocket, cards...)
    | Leave STORE_ID empty to hide the "Pay Online" button.
    */
    'sslcommerz' => [
        'store_id' => env('SSLCOMMERZ_STORE_ID'),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
        'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
    ],

    // Languages offered in the switcher
    'locales' => [
        'en' => 'English',
        'bn' => 'বাংলা',
    ],
];
