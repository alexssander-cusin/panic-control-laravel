<?php

/**
 * Test command for create a panic control
 */

use Illuminate\Console\Command;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

test('show all panic control on command', function () {
    $panics = PanicControlModel::factory()->count(3)->create();

    $this->artisan('panic-control:list')
        ->expectsOutputToContain($panics[0]->service)
        ->expectsOutputToContain($panics[1]->service)
        ->expectsOutputToContain($panics[2]->service)
        ->assertExitCode(Command::SUCCESS);

});

test('show details a panic control on command', function () {
    $panic = PanicControlModel::factory()->create([
        'service' => 'test',
    ]);

    $this->artisan('panic-control:show', ['service' => 'test'])
        ->expectsOutputToContain($panic->service)
        ->assertExitCode(Command::SUCCESS);

    $this->artisan('panic-control:show', ['service' => 'not-found'])
        ->expectsOutput('Panic Control não encontrado.')
        ->assertExitCode(Command::FAILURE);
});

test('active a panic control on command', function () {
    $panic = PanicControlModel::factory()->create([
        'status' => false,
    ]);

    expect(PanicControl::check($panic->service))->toBeFalse();

    $this->artisan('panic-control:active', ['service' => $panic->service])
        ->expectsOutput('Panic Control ativado com sucesso.')
        ->assertExitCode(Command::SUCCESS);

    expect(PanicControl::check($panic->service))->toBeTrue();
});

test('deactive a panic control on command', function () {
    $panic = PanicControlModel::factory()->create([
        'status' => true,
    ]);

    expect(PanicControl::check($panic->service))->toBeTrue();

    $this->artisan('panic-control:desactive', ['service' => $panic->service])
        ->expectsOutput('Panic Control desativado com sucesso.')
        ->assertExitCode(Command::SUCCESS);

    expect(PanicControl::check($panic->service))->toBeFalse();
});
