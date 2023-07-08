<?php

dataset('stores', [
    ['store.database', fn () => config()->set('panic-control.default', 'database')],
]);
