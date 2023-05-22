<?php

use PanicControl\Models\PanicControl as PanicControlModel;

test('verify status on helper', function () {
    $panic = PanicControlModel::factory()->create(['status' => true]);
    $this->assertTrue(getPanicControlActive($panic->name));

    $panic = PanicControlModel::factory()->create(['status' => false]);
    $this->assertFalse(getPanicControlActive($panic->name));
});
