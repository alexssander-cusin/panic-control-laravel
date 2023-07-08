<?php

/**
 * Test command for create a panic control
 */

use Illuminate\Console\Command;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

test('show all panic control on command', function (string $storeName, null $store) {
    $panics = PanicControlModel::factory()->count(3)->make()->toArray();
    foreach ($panics as $panic) {
        PanicControl::create($panic);
    }

    $this->artisan('panic-control:list')
        ->expectsOutputToContain($panics[0]['name'])
        ->expectsOutputToContain($panics[1]['name'])
        ->expectsOutputToContain($panics[2]['name'])
        ->assertExitCode(Command::SUCCESS);

})->with('stores');

test('show details a panic control on command', function (string $storeName, null $store) {
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'name' => 'test',
    ])->toArray());

    $this->artisan('panic-control:show', ['name' => 'test'])
        ->expectsOutputToContain($panic['name'])
        ->assertExitCode(Command::SUCCESS);

    $this->artisan('panic-control:show', ['name' => 'not-found'])
        ->expectsOutput('Panic Control não encontrado.')
        ->assertExitCode(Command::FAILURE);
})->with('stores');

test('active a panic control on command', function (string $storeName, null $store) {
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => false,
    ])->toArray());

    expect(PanicControl::check($panic['name']))->toBeFalse();

    $this->artisan('panic-control:active', ['name' => $panic['name']])
        ->expectsOutput('Panic Control ativado com sucesso.')
        ->assertExitCode(Command::SUCCESS);

    expect(PanicControl::check($panic['name']))->toBeTrue();
})->with('stores');

test('deactive a panic control on command', function (string $storeName, null $store) {
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
    ])->toArray());

    expect(PanicControl::check($panic['name']))->toBeTrue();

    $this->artisan('panic-control:desactive', ['name' => $panic['name']])
        ->expectsOutput('Panic Control desativado com sucesso.')
        ->assertExitCode(Command::SUCCESS);

    expect(PanicControl::check($panic['name']))->toBeFalse();
})->with('stores');
