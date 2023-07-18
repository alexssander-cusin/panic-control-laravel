<?php

use Illuminate\Support\Arr;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

test('Create a Panic Control by facade', function (string $storeName, null $store) {
    $count = PanicControl::count();

    $panic = [
        'name' => 'panic-name',
        'description' => 'panic-description',
        'status' => true,
    ];

    $panicControl = PanicControl::create($panic);

    expect($panicControl)->toBeArray();

    expect($panicControl['name'])->toBe($panic['name']);
    expect($panicControl['description'])->toBe($panic['description']);
    expect($panicControl['status'])->toBe($panic['status']);

    expect(PanicControl::count())->toBe($count + 1);

    $this->assertPanicControlHas($panic);
})->with('stores');

test('Failed to create Panic with wrong parameters', function (string $storeName, null $store, string $test, array $parameters) {
    if ($test == 'name.notUnique') {
        PanicControl::create($parameters);
        $parameters = PanicControlModel::factory()->make(['name' => $parameters['name']])->toArray();
    }

    $count = PanicControl::count();

    expect(fn () => PanicControl::create($parameters))->toThrow(Exception::class);

    $this->assertPanicControlMissing($parameters);

    expect(PanicControl::count())->toBe($count);
})->with('stores')->with([
    ['name.empty', fn () => PanicControlModel::factory()->make(['name' => ''])->toArray()],
    ['name.notUnique', fn () => PanicControlModel::factory()->make()->toArray()],
    ['name.max:264', fn () => PanicControlModel::factory()->make(['name' => 'namenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamename'])->toArray()],
    ['description.max:264', fn () => PanicControlModel::factory()->make(['description' => 'descriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescription'])->toArray()],
    ['status.string', ['name' => 'name', 'description' => 'description', 'status' => 'disabled']],
]);

test('update a Panic Control by facade from panic name', function (string $storeName, null $store, $key, $value) {
    $panic = PanicControl::create(PanicControlModel::factory()->make()->toArray());

    $newPanic = PanicControl::update($panic['name'], [$key => $value]);

    expect($newPanic)->toBeArray();
    $this->assertPanicControlMissing($panic);
    $this->assertPanicControlHas(Arr::only($newPanic, ['name', 'description', 'status']));
})->with('stores')->with([
    ['name', 'new name'],
    ['description', 'new description'],
    ['status', true],
]);

test('check status a Panic Control by facade', function (string $storeName, null $store) {
    //Check status TRUE
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
    ])->toArray());

    expect(PanicControl::check($panic['name']))->toBeTrue();

    //Check Status FALSE
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => false,
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    //Panic not exists in debug false
    expect(PanicControl::check('panic-not-found'))->toBeFalse();

    //Panic not exists in debug true
    config()->set('app.debug', true);
    expect(fn () => PanicControl::check('panic-not-found'))->toThrow(PanicControlDoesNotExist::class);
})->with('stores');

test('list all Panic Control by facade', function (string $storeName, null $store) {
    $panics = PanicControlModel::factory()->count(3)->make()->toArray();

    foreach ($panics as $panic) {
        PanicControl::create($panic);
    }

    expect(PanicControl::all())->toHaveCount(3);
})->with('stores');

test('detail a Panic Control by facade', function (string $storeName, null $store) {
    $panic = PanicControl::create(PanicControlModel::factory()->make()->toArray());

    expect(PanicControl::find($panic['name']))->toMatchArray($panic);
})->with('stores');
