<?php

namespace PanicControl\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Orchestra\Testbench\TestCase as Orchestra;
use PanicControl\Facades\PanicControl;
use PanicControl\PanicControlServiceProvider;

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
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('panic-control.drivers.database.connection', 'testing');
        config()->set('panic-control.cache.ttl', 0);

        $migration = include __DIR__.'/../database/migrations/create_panic_control_table.php.stub';
        $migration->up();
    }

    public function assertPanicControlHas($parameters)
    {
        match (config('panic-control.default')) {
            'database' => $this->assertDatabaseHas(config('panic-control.drivers.database.table'), $parameters),
            'endpoint', 'file' => count(Arr::where(PanicControl::all(), function (array $value, int|string $key) use ($parameters) {
                $return = true;
                foreach ($parameters as $k => $v) {
                    if ($value[$k] != $v) {
                        $return = false;
                    }
                }

                return $return;
            })) > 0,
            default => throw new \Exception('Invalid store provided'),
        };
    }

    public function assertPanicControlMissing($parameters)
    {
        match (config('panic-control.default')) {
            'database' => $this->assertDatabaseMissing(config('panic-control.drivers.database.table'), $parameters),
            'endpoint', 'file' => count(Arr::where(PanicControl::all(), function (array $value, int|string $key) use ($parameters) {
                $return = true;
                foreach ($parameters as $k => $v) {
                    if ($value[$k] != $v) {
                        $return = false;
                    }
                }

                return $return;
            })) == 0,
            default => throw new \Exception('Invalid store provided'),
        };
    }
}
