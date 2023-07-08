<?php

use PanicControl\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

dataset('stores', [
    ['store.database', fn () => config()->set('panic-control.default', 'database')],
]);
