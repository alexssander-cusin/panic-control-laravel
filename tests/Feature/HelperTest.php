<?php

use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

test('verify status on helper', function (string $storeName, bool $store) {
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
    ])->toArray());
    $this->assertTrue(getPanicControlActive($panic['name']));

    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => false,
    ])->toArray());
    $this->assertFalse(getPanicControlActive($panic['name']));
})->with('stores');
