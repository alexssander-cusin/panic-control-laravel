<?php

// config for PanicControl/PanicControl

return [
    'database' => [
        'table' => 'panic_controls',
    ],
    'cache' => [
        'enabled' => true,
        'store' => env('CACHE_DRIVER', 'file'),
        'key' => 'panic-control',
        'time' => 60,
    ],
    'rules' => [
        'route-name' => PanicControl\Rules\RouteName::class,
        'url-path' => PanicControl\Rules\UrlPath::class,
        'sampling' => PanicControl\Rules\Sampling::class,
        'user' => PanicControl\Rules\User::class,
    ],
];
