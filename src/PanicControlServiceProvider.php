<?php

namespace PanicControl;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PanicControlServiceProvider extends PackageServiceProvider
{
    public function registeringPackage()
    {
        $this->app->singleton('panic_control', function ($app) {
            return new PanicControlManager($app);
        });
    }

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
                \PanicControl\Commands\PanicControlActiveCommand::class,
                \PanicControl\Commands\PanicControlDesactiveCommand::class,
                \PanicControl\Commands\PanicControlCreateFileCommand::class,
            ]);
    }
}
