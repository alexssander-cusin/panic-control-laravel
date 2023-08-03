<?php

// config for PanicControl/PanicControl

return [
    /**
     *--------------------------------------------------------------------------
     * Set up what store will be used
     *--------------------------------------------------------------------------
    */

    'default' => 'database',

    'stores' => [
        'database' => [
            /**
             *--------------------------------------------------------------------------
             * Define the table name will be created in database
             *--------------------------------------------------------------------------
            */

            'table' => 'panic_controls',
        ],
        'file' => [
            /**
             *--------------------------------------------------------------------------
             * Defines which registered disk
             *--------------------------------------------------------------------------
             * The storage listed in /config/filesystem.php should be used
             *
             * Supported Drivers: "local", "ftp", "sftp", "s3"
            */

            'disk' => config('filesystems.default'),

            /**
             *--------------------------------------------------------------------------
             * Defines the name of the file that will be created
             *--------------------------------------------------------------------------
            */

            'path' => 'panic-control.json',
        ],
    ],
    'cache' => [
        /**
         *--------------------------------------------------------------------------
         * Activates the cache usage for the panic controls
         *--------------------------------------------------------------------------
        */

        'enabled' => true,

        /**
         *--------------------------------------------------------------------------
         * Defines what cache store should be used
         *--------------------------------------------------------------------------
         * The storage listed in /config/cache.php should be used
         *
         * Supported drivers: "apc", "array", "database", "file",
         *      "memcached", "redis", "dynamodb", "octane", "null"
        */

        'store' => env('CACHE_DRIVER', 'file'),

        /**
         *--------------------------------------------------------------------------
         * Cache Key Prefix
         *--------------------------------------------------------------------------
         *
         * When utilizing the APC, database, memcached, Redis, or DynamoDB cache
         * stores there might be other applications using the same cache. For
         * that reason, you may prefix every cache key to avoid collisions.
         *
        */

        'key' => 'panic-control',

        /**
         *--------------------------------------------------------------------------
         * Sets the time the cache will expire
         *--------------------------------------------------------------------------
        */

        'ttl' => 60,
    ],

    /**
     *--------------------------------------------------------------------------
     * List custom rules
     *--------------------------------------------------------------------------
    */

    'rules' => [
        'route-name' => PanicControl\Rules\RouteName::class,
        'url-path' => PanicControl\Rules\UrlPath::class,
        'sampling' => PanicControl\Rules\Sampling::class,
        'user' => PanicControl\Rules\User::class,
    ],
];
