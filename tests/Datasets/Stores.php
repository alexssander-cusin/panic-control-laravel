<?php

use Illuminate\Support\Facades\Storage;
use PanicControl\Contracts\Store;
use PanicControl\Facades\PanicControl;
use PanicControl\Stores\DatabaseStore;
use PanicControl\Stores\FileStore;

dataset('stores', [
    [
        'store.database', function () {
            config()->set('panic-control.default', 'database');
            $this->app->bind(Store::class, DatabaseStore::class);

            return true;
        },
    ],
    [
        'store.file', function () {
            config()->set('panic-control.default', 'file');
            $this->app->bind(Store::class, FileStore::class);
            Storage::disk(config('panic-control.stores.file.disk'))->delete(config('panic-control.stores.file.path'));
            Storage::disk(config('panic-control.stores.file.disk'))->put(config('panic-control.stores.file.path'), json_encode([]));
            PanicControl::clear();

            return true;
        },
    ],
]);
