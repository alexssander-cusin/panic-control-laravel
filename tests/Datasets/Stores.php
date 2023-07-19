<?php

use Illuminate\Support\Facades\Storage;
use PanicControl\Contracts\Store;
use PanicControl\Stores\DatabaseStore;
use PanicControl\Stores\FileStore;

dataset('stores', [
    [
        'store.database', function () {
            config()->set('panic-control.default', 'database');
            $this->app->bind(Store::class, DatabaseStore::class);
        },
    ],
]);
