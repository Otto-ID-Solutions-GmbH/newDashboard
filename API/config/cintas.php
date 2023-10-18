<?php

return [
    'customer_cuid' => env('CINTAS_CUSTOMER_CUID', \EndyJasmi\Cuid::make()),
    'facility_cuid' => env('CINTAS_FACILITY_CUID', \EndyJasmi\Cuid::make()),

    'errors' => [
        'send_mails' => env('SEND_ERROR_MAILS', true),
        'recipients' => env('ERROR_MAIL_RECIPIENTS', null) !== null ? explode(';', env('ERROR_MAIL_RECIPIENTS')) : ['support@witcotech.de']
    ],

    'view' => [
        'items_per_page' => 25,
        'top_n' => 5
    ],

    'time' => [
        'timezone' => env('CINTAS_FACILITY_TIMEZONE', 'UTC'),
    ],

    'process' => [
        'bundle_threshold' => env('CINTAS_BUNDLE_TRESHOLD', 0.4),
        'outdated_limit' => env('CINTAS_OUTDATED_LIMIT', 90)
    ],

    'label_printer' => [
        'name' => env('CINTAS_LABEL_WRITER_NAME', 'LabelWriter'),
    ]
];
