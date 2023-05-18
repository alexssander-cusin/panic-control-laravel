<?php

/**
 * Test command for create a panic control
 */

use PanicControl\Models\PanicControl;

test('show all panic control on command')->todo();
test('show details a panic control on command', function () {
    PanicControl::factory()->create([
        'service' => 'test',
    ]);

    $this->artisan('panic-control:show', ['service' => 'test'])
        ->assertExitCode(0);
});

test('active a panic control on command')->todo();
test('deactive a panic control on command')->todo();
