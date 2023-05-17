<?php

/**
 * Test command for create a panic control
 */

use PanicControl\Models\PanicControl;

it('create a basic panic control on command', function () {
    $panic = PanicControl::factory()->make();

    dd($panic->toArray());

    $this->artisan('panic-control:create')
        ->expectsQuestion('Name of panic control?', $panic->service)
        ->expectsQuestion('What category?', $panic->category)
        ->expectsQuestion('Description of panic control?', $panic->description)
        ->expectsQuestion('Status of panic control?', $panic->status)
        ->expectsOutput('Panic control created successfully.')
        ->assertExitCode(0);

    $this->assertDatabaseHas('panic_controls', [
        'service' => $panic->service,
        'description' => $panic->description,
        'status' => $panic->status,
        'category_id' => $panic->category_id,
    ]);
});

it('active a panic control on command')->todo();
it('deactive a panic control on command')->todo();
it('show all panic control on command')->todo();
it('show details a panic control on command')->todo();
