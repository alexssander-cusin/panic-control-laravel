<?php

use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

test('Create a Panic Control by facade', function () {
    $count = PanicControlModel::count();

    $panic = [
        'service' => 'service',
        'description' => 'description',
        'status' => true,
    ];

    $panicControl = PanicControl::create($panic);

    expect($panicControl)->toBeInstanceOf(\PanicControl\Models\PanicControl::class);

    expect($panicControl->service)->toBe($panic['service']);
    expect($panicControl->description)->toBe($panic['description']);
    expect($panicControl->status)->toBe($panic['status']);

    $this->assertDatabaseHas('panic_controls', $panic);

    expect(PanicControlModel::count())->toBe($count + 1);
});

test('Failed to create Panic with wrong parameters', function (string $test, array $parameters) {
    if($test == 'service.notUnique') {
        $parameters = PanicControlModel::factory()->make(['service' => $parameters['service']])->toArray();
    }

    $count = PanicControlModel::count();

    expect(fn () => PanicControl::create($parameters))->toThrow(Exception::class);

    $this->assertDatabaseMissing('panic_controls', $parameters);

    expect(PanicControlModel::count())->toBe($count);
})->with([
    ['service.empty', fn() => PanicControlModel::factory()->make(['service' => ''])->toArray()],
    ['service.notUnique', fn() => PanicControlModel::factory()->create()->toArray()],
    ['service.max:259', fn() => PanicControlModel::factory()->make(['service' => 'serviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceservice'])->toArray()],
    ['description.max:264', fn() => PanicControlModel::factory()->make(['description' => 'descriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescription'])->toArray()],
    ['status.string', fn() => PanicControlModel::factory()->make(['status' => 'disabled'])->toArray()],
]);

test('update a Panic Control by facade', function () {
    $panic = PanicControlModel::factory()->create();
    $newName = 'service updated';

    $newPanic = PanicControl::update($panic->service, ['service' => $newName]);

    $this->assertDatabaseMissing('panic_controls', ['service' => $panic->service]);
    $this->assertDatabaseHas('panic_controls', ['service' => $newName]);
});

test('check status a Panic Control by facade')->todo();
test('list all Panic Control by facade')->todo();
test('detail a Panic Control by facade')->todo();
