<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('store database count test')->todo();
it('store file count test')->todo();
it('store link count test')->todo();
