<?php

use Illuminate\Support\Arr;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Exceptions\PanicControlStoreNotSupport;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

test('Create a Panic Control by facade', function (string $storeName, bool $store) {
    try {
        if ($storeName == 'store.endpoint') {
            makeFakeEndpoint(response: [], status: 200);
        }

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

    } catch (\Throwable $th) {
        if ($storeName == 'store.endpoint') {
            expect(fn () => throw $th)->toThrow(PanicControlStoreNotSupport::class);

            return;
        } else {
            throw $th;
        }
    }
})->with('stores');

test('Failed to create Panic with wrong parameters', function (string $storeName, bool $store, string $test, array $parameters) {
    if ($storeName == 'store.endpoint') {
        makeFakeEndpoint(response: [], status: 200);
    }

    if ($test == 'name.notUnique' && $storeName != 'store.endpoint') {
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

test('update a Panic Control by facade from panic name', function (string $storeName, bool $store, $key, $value) {
    try {
        $panic = createPanic(count: 1)[0];

        $newPanic = PanicControl::update($panic['name'], [$key => $value]);

        expect($newPanic)->toBeArray();
        $this->assertPanicControlMissing($panic);
        $this->assertPanicControlHas(Arr::only($newPanic, ['name', 'description', 'status']));
    } catch (\Throwable $th) {
        if ($storeName == 'store.endpoint') {
            expect(fn () => throw $th)->toThrow(PanicControlStoreNotSupport::class);

            return;
        } else {
            throw $th;
        }
    }
})->with('stores')->with([
    ['name', 'new name'],
    ['description', 'new description'],
    ['status', true],
]);

test('check status a Panic Control by facade', function (string $storeName, bool $store) {
    //Check status TRUE
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    //Check Status FALSE
    $panic = createPanic(count: 1, parameters: [
        'status' => false,
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    //Panic not exists in debug false
    expect(PanicControl::check('panic-not-found'))->toBeFalse();

    //Panic not exists in debug true
    config()->set('app.debug', true);
    expect(fn () => PanicControl::check('panic-not-found'))->toThrow(PanicControlDoesNotExist::class);
})->with('stores');

test('list all Panic Control by facade', function (string $storeName, bool $store) {
    $panic = createPanic(count: 3);

    expect(PanicControl::all())->toHaveCount(3);
})->with('stores');

test('detail a Panic Control by facade', function (string $storeName, bool $store) {
    $panic = createPanic(count: 1)[0];

    expect(PanicControl::find($panic['name']))->toMatchArray($panic);
})->with('stores');

test('count Panic Controls by facade', function (string $storeName, bool $store) {
    if ($storeName == 'store.endpoint') {
        makeFakeEndpoint(response: [], status: 200);
    }
    expect(PanicControl::count())->toBeInt()->toBe(0);

    createPanic(count: 1);
    expect(PanicControl::count())->toBeInt()->toBe(1);
})->with('stores');
