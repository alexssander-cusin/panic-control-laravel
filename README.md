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

This is the contents of the published config file:

```php
return [
    'cache' => [
        'store' => env('QUEUE_CONNECTION', 'file'),
        'key' => 'panic-control',
        'time' => 60,
    ],
];
```

## Usage

### Facade

Create a Panic Control:

```php
use PanicControl\Facades\PanicControl;

PanicControl::create([
    'service' => 'service-name',
    'description' => 'Description for Panic Control',
    'status' => false,
]);
```

Update a Panic Control:

```php
use PanicControl\Facades\PanicControl;

$panic = 'panic-control-name'; //Panic Control Name or ID
PanicControl::update($panic, [
    'service' => 'new-panic-name',
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
