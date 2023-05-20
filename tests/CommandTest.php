<?php

/**
 * Test command for create a panic control
 */

use Illuminate\Console\Command;
use PanicControl\Models\PanicControl;

test('show all panic control on command')->todo();

test('show details a panic control on command', function () {
    $panic = PanicControl::factory()->create([
        'service' => 'test',
    ]);

    $this->artisan('panic-control:show', ['service' => 'test'])
        ->expectsOutput("service: {$panic->service}")
        ->expectsOutput("description: {$panic->description}")
        ->expectsOutput("status: {$panic->status}")
        ->assertExitCode(Command::SUCCESS);

    $this->artisan('panic-control:show', ['service' => 'not-found'])
        ->expectsOutput('Panic Control não encontrado.')
        ->assertExitCode(Command::FAILURE);
});

test('active a panic control on command')->todo();
test('deactive a panic control on command')->todo();
