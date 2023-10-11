<?php

use Illuminate\Support\Facades\Storage;
use PanicControl\Facades\PanicControl;

dataset('stores', [
    'database' => function () {
        config()->set('panic-control.default', 'database');

        return 'database';
    },
    'file' => function () {
        config()->set('panic-control.default', 'file');

        Storage::disk(config('panic-control.drivers.file.disk'))->delete(config('panic-control.drivers.file.path'));
        Storage::disk(config('panic-control.drivers.file.disk'))->put(config('panic-control.drivers.file.path'), json_encode([]));
        PanicControl::clear();

        return 'file';
    },
    'endpoint' => function () {
        config()->set('panic-control.default', 'endpoint');

        return 'endpoint';
    },
]);
