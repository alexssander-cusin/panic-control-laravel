<?php

/**
 * Test command for create a panic control
 */

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

test('show all panic control on command but list is empty', function (string $driver) {
    match ($driver) {
        'database' => PanicControlModel::truncate(),
        'file' => Storage::disk(config('panic-control.drivers.file.disk'))->put(config('panic-control.drivers.file.path'), json_encode([])),
        'endpoint' => makeFakeEndpoint(response: [], status: 200),
    };
    PanicControl::clear();
    $this->artisan('panic-control:list')
        ->expectsOutputToContain('Nenhum Panic Control encontrado.')
        ->assertExitCode(Command::FAILURE);
})->with('stores');

test('show all panic control on command', function (string $driver) {
    $panics = createPanic(count: 3);

    $this->artisan('panic-control:list')
        ->expectsOutputToContain($panics[0]['name'])
        ->expectsOutputToContain($panics[1]['name'])
        ->expectsOutputToContain($panics[2]['name'])
        ->assertExitCode(Command::SUCCESS);
})->with('stores');

test('show details a panic control on command', function (string $driver) {
    $panic = createPanic(count: 1, parameters: [
        'name' => 'test',
    ])[0];

    $this->artisan('panic-control:show', ['name' => 'test'])
        ->expectsOutputToContain($panic['name'])
        ->assertExitCode(Command::SUCCESS);

    $this->artisan('panic-control:show', ['name' => 'not-found'])
        ->expectsOutput('Panic Control: not-found does not exist.')
        ->assertExitCode(Command::FAILURE);
})->with('stores');

test('active a panic control on command', function (string $driver) {
    $panic = createPanic(count: 1, parameters: [
        'status' => false,
    ])[0];

    expect(PanicControl::check($panic['name']))->toBeFalse();

    if ($driver == 'endpoint') {
        $this->artisan('panic-control:active', ['name' => $panic['name']])
            ->expectsOutput('Panic Control: Driver endpoint does not support update method.')
            ->assertExitCode(Command::FAILURE);

        expect(PanicControl::check($panic['name']))->toBeFalse();
    } else {
        $this->artisan('panic-control:active', ['name' => $panic['name']])
            ->expectsOutput("Panic Control: {$panic['name']} ativado com sucesso.")
            ->assertExitCode(Command::SUCCESS);

        expect(PanicControl::check($panic['name']))->toBeTrue();
    }
})->with('stores');

test('deactive a panic control on command', function (string $driver) {
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
    ])[0];

    expect(PanicControl::check($panic['name']))->toBeTrue();

    if ($driver == 'endpoint') {
        $this->artisan('panic-control:desactive', ['name' => $panic['name']])
            ->expectsOutput('Panic Control: Driver endpoint does not support update method.')
            ->assertExitCode(Command::FAILURE);

        expect(PanicControl::check($panic['name']))->toBeTrue();
    } else {
        $this->artisan('panic-control:desactive', ['name' => $panic['name']])
            ->expectsOutput("Panic Control: {$panic['name']} desativado com sucesso.")
            ->assertExitCode(Command::SUCCESS);

        expect(PanicControl::check($panic['name']))->toBeFalse();
    }

})->with('stores');

test('create file when not exists and the store set file', function (string $driver) {

    Storage::disk(config('panic-control.drivers.file.disk'))->delete(config('panic-control.drivers.file.path'));

    if ($driver === 'file') {
        $this->artisan('panic-control:create-file')
            ->expectsOutput('Arquivo criado com sucesso.')
            ->assertExitCode(Command::SUCCESS);

        expect(Storage::disk(config('panic-control.drivers.file.disk'))->exists(config('panic-control.drivers.file.path')))->toBeTrue();
    } else {
        $this->artisan('panic-control:create-file')
            ->expectsOutput('O store configurado não é do tipo FILE.')
            ->assertExitCode(Command::FAILURE);

        expect(Storage::disk(config('panic-control.drivers.file.disk'))->exists(config('panic-control.drivers.file.path')))->toBeFalse();
    }
})->with('stores');
