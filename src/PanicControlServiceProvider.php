<?php

namespace PanicControl;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use PanicControl\Commands\PanicControlCommand;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_panic-control-laravel_table')
            ->hasCommand(PanicControlCommand::class);
    }
}
