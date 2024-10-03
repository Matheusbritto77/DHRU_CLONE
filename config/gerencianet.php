<?php

return [
    'mode' => env('GN_MODE'),
    'debug'=> env('GN_DEBUG'),
    'default_key_pix'=> env('GN_DEFAULT_KEY_PIX'),
    'sandbox' => [
        'client_id' => env('GN_TEST_CLIENT_ID'),
        'client_secret' => env('GN_TEST_CLIENT_SECRET'),
        'certificate_name' => env('GN_TEST_CERTIFICATE_NAME'),
    ],
    'production' =>  [
        'client_id' => env('GN_PROD_CLIENT_ID'),
        'client_secret' => env('GN_PROD_CLIENT_SECRET'),
        'certificate_name' => env('GN_PROD_CERTIFICATE_NAME'),
    ],
];


