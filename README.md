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

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="panic-control-laravel-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="panic-control-laravel-config"
```

This is the contents rof the published config file:

```php
return [
    'database' => [
        'table' => 'panic_controls',
    ],
    'cache' => [
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
```

## Usage

### Facade

Create a Panic Control:

```php
use PanicControl\Facades\PanicControl;

PanicControl::create([
    'name' => 'panic-control-name',
    'description' => 'Description for Panic Control',
    'status' => false,
]);
```

Update a Panic Control:

```php
use PanicControl\Facades\PanicControl;

$panic = 'panic-control-name'; //Panic Control Name or ID
PanicControl::update($panic, [
    'name' => 'new-panic-control-name',
]);
```

Get all Panic Control:

```php
use PanicControl\Facades\PanicControl;

PanicControl::all();
```

Get a Panic Control:

```php
use PanicControl\Facades\PanicControl;

PanicControl::find('panic-control-name');
```

Check if Panic Control is Active

```php
PanicControl::check('panic-control-name');
```

### Helper

Check if Panic Control is Active

```php
use PanicControl\Facades\PanicControl;

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

Active a Panic Control

```bash
php artisan panic-control:active panic-control-name
```

Desactive a Panic Control

```bash
php artisan panic-control:desactive panic-control-name
```

## Rules

We can add supplementary rules that will respect the main status

> All rules must return true for the panic to be activated, if nothing is registered, it is disregarded.

### Route Name

Checks whether the `Route::currentRouteName()` callback is listed under the `route-name` key.

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

Checks whether the `Request::path()` return is listed under the `url-path` key.

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

Set active when the chance number by a possibilities number, example 1 out of 10 or 5 out of 10.

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

Check if user logged id or email is in list.

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
PanicControl::create([
    'name' => 'panic-control-name',
    'description' => 'Description for Panic Control',
    'status' => true,
    'rules' => [
        'class-name' => 'parameters',
    ],
]);
```

## Testing

```bash
composer test
```

## TODO

- [ ] Support Panic Control category

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Alexssander Cusin](https://github.com/alexssander-cusin)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
