# Panic Control for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alexssander-cusin/panic-control-laravel.svg?style=flat-square)](https://packagist.org/packages/alexssander-cusin/panic-control-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/alexssander-cusin/panic-control-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/alexssander-cusin/panic-control-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/alexssander-cusin/panic-control-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/alexssander-cusin/panic-control-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/alexssander-cusin/panic-control-laravel.svg?style=flat-square)](https://packagist.org/packages/alexssander-cusin/panic-control-laravel)
## Installation

You can install the package via composer:

```bash
composer require alexssander-cusin/panic-control-laravel
```

You can publish and run the migrations with (only for database store):

```bash
php artisan vendor:publish --tag="panic-control-laravel-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="panic-control-laravel-config"
```

This are the contents of the published config file:

```php
return [
    /** 
     *--------------------------------------------------------------------------
     * Set up what store will be used
     *--------------------------------------------------------------------------
    */
    
    'default' => 'database',
    
    'drivers' => [
        'database' => [
            /**
             *--------------------------------------------------------------------------
             * Defines which registered connections
             *--------------------------------------------------------------------------
             * The storage listed in /config/database.php should be used
             */
            
            'connection' => config('database.default'),

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
        'endpoint' => [
            /**
             *--------------------------------------------------------------------------
             * Defines the URL of the endpoint
             *--------------------------------------------------------------------------
             */
            'url' => 'https://localhost/panic-control.json',
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
```

## Usage

### Facade

Create a Panic Control [^1]

```php
use PanicControl\Facades\PanicControl;

PanicControl::create([
    'name' => 'panic-control-name',
    'description' => 'Description for Panic Control',
    'status' => false,
]);
```

Update a Panic Control [^1]

```php
use PanicControl\Facades\PanicControl;

$panic = 'panic-control-name'; //Panic Control Name or ID

PanicControl::update($panic, [
    'name' => 'new-panic-control-name',
]);
```

Get all Panic Control

```php
use PanicControl\Facades\PanicControl;

PanicControl::all();
```

Get a Panic Control

```php
use PanicControl\Facades\PanicControl;

PanicControl::find('panic-control-name');
```

Check if Panic Control is Active

```php
use PanicControl\Facades\PanicControl;

PanicControl::check('panic-control-name');
```

### Helper

Check if Panic Control is Active

```php
getPanicControlActive('panic-control-name');
```

### Command

List all Panic Control

```bash
php artisan panic-control:list
```

Detail a Panic Control

```bash
php artisan panic-control:show panic-control-name
```

Activate a Panic Control [^1]

```bash
php artisan panic-control:active panic-control-name
```

Deactivate a Panic Control [^1]

```bash
php artisan panic-control:desactive panic-control-name
```

## Rules

We can add supplementary rules that will respect the main status

> All rules must return true for the panic to be activated, if nothing is registered or return null|false, it is disregarded.

### Route Name

Checks whether the `Route::currentRouteName()` return is listed inside the `route-name` key.

```php
use PanicControl\Facades\PanicControl;

PanicControl::create([
    'name' => 'panic-control-name',
    'description' => 'Description for Panic Control',
    'status' => true,
    'rules' => [
        'route-name' => [
            'route.name.home',
            'route.name.contact'
        ],
    ],
]);
```

### URL Path

Checks whether the `Request::path()` return is listed the `url-path` key.

```php
use PanicControl\Facades\PanicControl;

PanicControl::create([
    'name' => 'panic-control-name',
    'description' => 'Description for Panic Control',
    'status' => true,
    'rules' => [
        'url-path' => [
            'url/path/home',
            'url/path/contact'
        ],
    ],
]);
```

### Sampling

Will be activated for just a sample of the users, based on the number of chances, and the "out of" sample. In the example below, the panic control will be activated for 5 out of 10 users i.e. half of the users.
> IMPORTANT: the chance is a probability, there may be a small variation both for more and for less.

```php
use PanicControl\Facades\PanicControl;

PanicControl::create([
    'name' => 'panic-control-name',
    'description' => 'Description for Panic Control',
    'status' => true,
    'rules' => [
        'rules' => [
            'sampling' => [
                'chance' => 5,
                'out_of' => 10,
            ],
        ],
    ],
]);
```

### User logged

Check if user logged `id` or `email` is listed the `user` key.

```php
use PanicControl\Facades\PanicControl;

PanicControl::create([
    'name' => 'panic-control-name',
    'description' => 'Description for Panic Control',
    'status' => true,
    'rules' => [
        'rules' => [
            'user' => [
                1, //User ID
                'user@test.com', //User EMAIL
            ],
        ],
    ],
]);
```

### Custom Rules

To create a custom rule follow the example

```php
use PanicControl\Rules\Rule;
use PanicControl\Contracts\Rule as RuleContract;

class ClassName extends Rule implements RuleContract
{
    public function rule(array $parameters): bool|null
    {
        return false|true|null;
    }
}
```

The class must be registered in `config/panic-control.php` under the `rules` key.

```php
return [
    ...
    'rules' => [
        'class-name' => Namespace/ClassName::class,
    ],
];
```

In the `rules` column of the database, add the key registered in `config/panic-control.php` with the parameters that will be sent to the class.

```php
use PanicControl\Facades\PanicControl;

PanicControl::create([
    'name' => 'panic-control-name',
    'description' => 'Description for Panic Control',
    'status' => true,
    'rules' => [
        'class-name' => 'parameters',
    ],
]);
```
## Drivers

By default, the driver configured in `config('panic-control.default')`, but can be changed with support for: **database**, **file**, **endpoint**.

```php
use PanicControl\Facades\PanicControl;

PanicControl::driver('file')->count()
```

### Extending Driver 

> This feature is in beta tests

If you want to include support for other driver, you can easily register a new driver in the `AppServiceProvider` as shown below:

```php
use PanicControl\Facades\PanicControl;

PanicControl::extend('other', function(){
  return return new \PanicControl\PanicControl(new OtherDrive());
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Alexssander Cusin](https://github.com/alexssander-cusin)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[^1]: Not supported for *endpoint store*.
