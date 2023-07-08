<?php

namespace PanicControl\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use PanicControl\Contracts\Store;
use PanicControl\Stores\DatabaseStore;

class StoreServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Set the store to use
         */
        match (config('panic-control.default')) {
            'database' => $this->app->bind(Store::class, DatabaseStore::class),
            default => throw new \Exception('Invalid store provided'),
        };
    }

    /**
     * Provides services.
     */
    public function provides(): array
    {
        return [
            Store::class,
        ];
    }
}
