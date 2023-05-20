<?php

// config for PanicControl/PanicControl
return [
    'cache' => [
        'store' => env('CACHE_DRIVER', 'file'),
        'key' => 'panic-control',
        'time' => 60,
    ],
];
