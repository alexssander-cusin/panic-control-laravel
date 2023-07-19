<?php

namespace PanicControl\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use PanicControl\PanicControlServiceProvider;
use PanicControl\Providers\StoreServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'PanicControl\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            PanicControlServiceProvider::class,
            StoreServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('panic-control.cache.time', 0);

        $migration = include __DIR__.'/../database/migrations/create_panic_control_table.php.stub';
        $migration->up();
    }

    public function assertPanicControlHas($parameters)
    {
        match (config('panic-control.default')) {
            'database' => $this->assertDatabaseHas(config('panic-control.stores.database.table'), $parameters),
            default => throw new \Exception('Invalid store provided'),
        };
    }

    public function assertPanicControlMissing($parameters)
    {
        match (config('panic-control.default')) {
            'database' => $this->assertDatabaseMissing(config('panic-control.stores.database.table'), $parameters),
            default => throw new \Exception('Invalid store provided'),
        };
    }
}
