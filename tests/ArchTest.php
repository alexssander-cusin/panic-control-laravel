<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('store database')->todo();
it('store file')->todo();
it('store link')->todo();
