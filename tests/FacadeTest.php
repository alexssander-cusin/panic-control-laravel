<?php

use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

test('Create a Panic Control by facade', function () {
    $count = PanicControlModel::count();

    $panic = [
        'name' => 'panic-name',
        'description' => 'panic-description',
        'status' => true,
    ];

    $panicControl = PanicControl::create($panic);

    expect($panicControl)->toBeInstanceOf(\PanicControl\Models\PanicControl::class);

    expect($panicControl->name)->toBe($panic['name']);
    expect($panicControl->description)->toBe($panic['description']);
    expect($panicControl->status)->toBe($panic['status']);

    $this->assertDatabaseHas(config('panic-control.database.table'), $panic);

    expect(PanicControlModel::count())->toBe($count + 1);
});

test('Failed to create Panic with wrong parameters', function (string $test, array $parameters) {
    if ($test == 'name.notUnique') {
        $parameters = PanicControlModel::factory()->make(['name' => $parameters['name']])->toArray();
    }

    $count = PanicControlModel::count();

    expect(fn () => PanicControl::create($parameters))->toThrow(Exception::class);

    $this->assertDatabaseMissing(config('panic-control.database.table'), $parameters);

    expect(PanicControlModel::count())->toBe($count);
})->with([
    ['name.empty', fn () => PanicControlModel::factory()->make(['name' => ''])->toArray()],
    ['name.notUnique', fn () => PanicControlModel::factory()->create()->toArray()],
    ['name.max:264', fn () => PanicControlModel::factory()->make(['name' => 'namenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamenamename'])->toArray()],
    ['description.max:264', fn () => PanicControlModel::factory()->make(['description' => 'descriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescription'])->toArray()],
    ['status.string', ['name' => 'name', 'description' => 'description', 'status' => 'disabled']],
]);

test('update a Panic Control by facade from panic name', function ($key, $value) {
    $panic = PanicControlModel::factory()->create();

    $newPanic = PanicControl::update($panic->name, [$key => $value]);

    $this->assertDatabaseMissing(config('panic-control.database.table'), $panic->toArray());
    $this->assertDatabaseHas(config('panic-control.database.table'), $newPanic->only(['name', 'description', 'status']));
})->with([
    ['name', 'new name'],
    ['description', 'new description'],
    ['status', true],
]);

test('check status a Panic Control by facade', function () {
    //Check status TRUE
    $panic = PanicControlModel::factory()->create([
        'status' => true,
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();

    //Check Status FALSE
    $panic = PanicControlModel::factory()->create([
        'status' => false,
    ]);
    expect(PanicControl::check($panic->name))->toBeFalse();

    //Panic not exists in debug false
    expect(PanicControl::check('panic-not-found'))->toBeFalse();

    //Panic not exists in debug true
    config()->set('app.debug', true);
    expect(fn () => PanicControl::check('panic-not-found'))->toThrow(PanicControlDoesNotExist::class);
});

test('list all Panic Control by facade', function () {
    $panics = PanicControlModel::factory()->count(3)->create();

    expect(PanicControl::all())->toHaveCount(3);
});

test('detail a Panic Control by facade', function () {
    $panic = PanicControlModel::factory()->create();

    expect(PanicControl::find($panic->name))->toMatchArray($panic->toArray());
});
