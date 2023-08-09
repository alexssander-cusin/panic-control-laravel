<?php

test('verify status on helper', function (string $storeName, bool $store) {
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
    ])[0];
    $this->assertTrue(getPanicControlActive($panic['name']));

    $panic = createPanic(count: 1, parameters: [
        'status' => false,
    ])[0];
    $this->assertFalse(getPanicControlActive($panic['name']));
})->with('stores');
