<?php

// config for PanicControl/PanicControl
return [
    'cache' => [
        'store' => env('QUEUE_CONNECTION', 'file'),
        'key' => 'panic-control',
        'time' => 60,
    ],
];
