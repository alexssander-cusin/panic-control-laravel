<?php

use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as ModelsPanicControl;

test('Create a Panic Control by facade', function () {
    $count = ModelsPanicControl::count();

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

    expect(ModelsPanicControl::count())->toBe($count + 1);
});

test('Failed to create Panic with wrong parameters', function (string $test, array $parameters) {
    if($test == 'service.notUnique') {
        $parameters = ModelsPanicControl::factory()->make(['service' => $parameters['service']])->toArray();
    }

    $count = ModelsPanicControl::count();

    expect(fn() => PanicControl::create($parameters))->toThrow(Exception::class);

    $this->assertDatabaseMissing('panic_controls', $parameters);

    expect(ModelsPanicControl::count())->toBe($count);
})->with([
    ['service.empty', fn() => ModelsPanicControl::factory()->make(['service' => ''])->toArray()],
    ['service.notUnique', fn() => ModelsPanicControl::factory()->create()->toArray()],
    ['service.max:259', fn() => ModelsPanicControl::factory()->make(['service' => 'serviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceserviceservice'])->toArray()],
    ['description.max:264', fn() => ModelsPanicControl::factory()->make(['description' => 'descriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescriptiondescription'])->toArray()],
    ['status.string', fn() => ModelsPanicControl::factory()->make(['status' => 'disabled'])->toArray()],
]);

test('update a Panic Control by facade')->todo();
test('check status a Panic Control by facade')->todo();
test('list all Panic Control by facade')->todo();
test('detail a Panic Control by facade')->todo();
