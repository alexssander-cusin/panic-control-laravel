<?php

// config for PanicControl/PanicControl

return [
    'default' => 'database',
    'stores' => [
        'database' => [
            'table' => 'panic_controls',
        ],
        'file' => [
            'disk' => config('filesystems.default'),
            'path' => 'panic-control.json',
        ],
        'link' => [
            'url' => 'https://localhost/panic-control.json',
        ],
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
