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
        ->expectsOutputToContain($panics[0]->name)
        ->expectsOutputToContain($panics[1]->name)
        ->expectsOutputToContain($panics[2]->name)
        ->assertExitCode(Command::SUCCESS);

});

test('show details a panic control on command', function () {
    $panic = PanicControlModel::factory()->create([
        'name' => 'test',
    ]);

    $this->artisan('panic-control:show', ['name' => 'test'])
        ->expectsOutputToContain($panic->name)
        ->assertExitCode(Command::SUCCESS);

    $this->artisan('panic-control:show', ['name' => 'not-found'])
        ->expectsOutput('Panic Control não encontrado.')
        ->assertExitCode(Command::FAILURE);
});

test('active a panic control on command', function () {
    $panic = PanicControlModel::factory()->create([
        'status' => false,
    ]);

    expect(PanicControl::check($panic->name))->toBeFalse();

    $this->artisan('panic-control:active', ['name' => $panic->name])
        ->expectsOutput('Panic Control ativado com sucesso.')
        ->assertExitCode(Command::SUCCESS);

    expect(PanicControl::check($panic->name))->toBeTrue();
});

test('deactive a panic control on command', function () {
    $panic = PanicControlModel::factory()->create([
        'status' => true,
    ]);

    expect(PanicControl::check($panic->name))->toBeTrue();

    $this->artisan('panic-control:desactive', ['name' => $panic->name])
        ->expectsOutput('Panic Control desativado com sucesso.')
        ->assertExitCode(Command::SUCCESS);

    expect(PanicControl::check($panic->name))->toBeFalse();
});
