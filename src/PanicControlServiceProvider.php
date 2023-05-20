<?php

namespace PanicControl;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PanicControlServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('panic-control-laravel')
            ->hasConfigFile(['panic-control'])
            ->hasMigration('create_panic_control_table')
            ->hasCommands([
                \PanicControl\Commands\PanicControlShowCommand::class,
                \PanicControl\Commands\PanicControlListCommand::class,
            ]);
    }
}
