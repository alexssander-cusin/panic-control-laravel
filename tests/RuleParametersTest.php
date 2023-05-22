<?php

use Illuminate\Support\Facades\Route;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

test('rule does not exist', function () {
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'not-found' => [
                'not-found-parameter',
            ],
        ],
    ]);

    expect(fn () => PanicControl::check($panic->service))->toThrow(\PanicControl\Exceptions\PanicControlRuleDoesNotExist::class);
})->skip();

test('route name', function () {
    Route::shouldReceive('currentRouteName')->andReturn('route-name-test');

    // Route Name exists but Panic Control is disabled
    $panic = PanicControlModel::factory()->create([
        'status' => false,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->service))->toBeFalse();

    // Route Name exists and Panic Control is enabled
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->service))->toBeTrue();

    // Route Name exists and Panic Control is enabled but the route name is not in the list
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-error',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->service))->toBeFalse();

    // Panic Control is enabled but the route name set false
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'route-name' => false,
        ],
    ]);
    expect(PanicControl::check($panic->service))->toBeTrue();

    // Panic Control is enabled but the route name set null
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'route-name' => false,
        ],
    ]);
    expect(PanicControl::check($panic->service))->toBeTrue();
});

test('url path')->todo();
test('user')->todo();
test('user group')->todo();
test('percent access')->todo();
