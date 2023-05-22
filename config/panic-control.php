<?php

// config for PanicControl/PanicControl

return [
    'cache' => [
        'store' => env('CACHE_DRIVER', 'file'),
        'key' => 'panic-control',
        'time' => 60,
    ],
    'rules' => [
        'route-name' => PanicControl\Rules\RouteName::class,
    ],
];
