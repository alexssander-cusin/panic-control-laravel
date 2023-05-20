<?php

/**
 * Test command for create a panic control
 */

use Illuminate\Console\Command;
use PanicControl\Models\PanicControl;

test('show all panic control on command', function () {
    $panics = PanicControl::factory()->count(3)->create();

    $this->artisan('panic-control:list')
        ->expectsOutputToContain($panics[0]->service)
        ->expectsOutputToContain($panics[1]->service)
        ->expectsOutputToContain($panics[2]->service)
        ->assertExitCode(Command::SUCCESS);

});

test('show details a panic control on command', function () {
    $panic = PanicControl::factory()->create([
        'service' => 'test',
    ]);

    $this->artisan('panic-control:show', ['service' => 'test'])
        ->expectsOutputToContain($panic->service)
        ->assertExitCode(Command::SUCCESS);

    $this->artisan('panic-control:show', ['service' => 'not-found'])
        ->expectsOutput('Panic Control não encontrado.')
        ->assertExitCode(Command::FAILURE);
});

test('active a panic control on command')->todo();
test('deactive a panic control on command')->todo();
