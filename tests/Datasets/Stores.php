<?php

dataset('stores', [
    ['store.database', fn () => config()->set('panic-control.default', 'database')],
    ['store.file', fn () => config()->set('panic-control.default', 'file')],
]);
