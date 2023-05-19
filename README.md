# This is my package panic-control-laravel

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
        'name' => 'panic-control',
        'time' => 60,
    ],
];
```

## Usage

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

Check a Panic Control:

```php
use PanicControl\Facades\PanicControl;

$panic = 'panic-control-name'; //Panic Control Name or ID
PanicControl::update($panic);
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

## Testing

```bash
composer test
```

## TODO

- [ ] Support Panic Control category

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Alexssander Cusin](https://github.com/alexssander-cusin)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
