<?php

/**
 * Test command for create a panic control
 */

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;
use Pest\Panic;

test('show all panic control on command', function (string $storeName, null $store) {
    //Test empty panic control
    match ($storeName) {
        'store.database' => PanicControlModel::truncate(),
    };
    PanicControl::clear();
    $this->artisan('panic-control:list')
        ->expectsOutputToContain('Nenhum Panic Control encontrado.')
        ->assertExitCode(Command::FAILURE);

    //List all panic control
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
        ->expectsOutput('Panic Control: not-found does not exist.')
        ->assertExitCode(Command::FAILURE);
})->with('stores');

test('active a panic control on command', function (string $storeName, null $store) {
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => false,
    ])->toArray());

    expect(PanicControl::check($panic['name']))->toBeFalse();

    $this->artisan('panic-control:active', ['name' => $panic['name']])
        ->expectsOutput("Panic Control: {$panic['name']} ativado com sucesso.")
        ->assertExitCode(Command::SUCCESS);

    expect(PanicControl::check($panic['name']))->toBeTrue();
})->with('stores');

test('deactive a panic control on command', function (string $storeName, null $store) {
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
    ])->toArray());

    expect(PanicControl::check($panic['name']))->toBeTrue();

    $this->artisan('panic-control:desactive', ['name' => $panic['name']])
        ->expectsOutput("Panic Control: {$panic['name']} desativado com sucesso.")
        ->assertExitCode(Command::SUCCESS);

    expect(PanicControl::check($panic['name']))->toBeFalse();
})->with('stores');

test('create file when not exists and the store set file', function (string $storeName, null $store) {

    Storage::disk(config('panic-control.stores.file.disk'))->delete(config('panic-control.stores.file.path'));

    if ($storeName === 'store.file') {
        $this->artisan('panic-control:create-file')
            ->expectsOutput('Arquivo criado com sucesso.')
            ->assertExitCode(Command::SUCCESS);

        expect(Storage::disk(config('panic-control.stores.file.disk'))->exists(config('panic-control.stores.file.path')))->toBeTrue();
    } else {
        $this->artisan('panic-control:create-file')
            ->expectsOutput('O store configurado não é do tipo FILE.')
            ->assertExitCode(Command::FAILURE);

        expect(Storage::disk(config('panic-control.stores.file.disk'))->exists(config('panic-control.stores.file.path')))->toBeFalse();
    }
})->with('stores');
