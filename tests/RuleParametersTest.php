<?php

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

    expect(fn () => PanicControl::check($panic->name))->toThrow(\PanicControl\Exceptions\PanicControlRuleDoesNotExist::class);
});

test('multi rules', function () {
    $this->get('http://localhost/url-path-test');
    \Illuminate\Support\Facades\Route::shouldReceive('currentRouteName')->andReturn('route-name-test');

    // Rules exists but Panic Control is disabled
    $panic = PanicControlModel::factory()->create([
        'status' => false,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => [
                'url-path-test',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeFalse();

    // Rules exists and Panic Control is enabled
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => [
                'url-path-test',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();

    // Rules exists and Panic Control is enabled but one rule is not in the list
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => [
                'url-path-error',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeFalse();
});

test('rule route name', function () {
    \Illuminate\Support\Facades\Route::shouldReceive('currentRouteName')->andReturn('route-name-test');

    // Route Name exists but Panic Control is disabled
    $panic = PanicControlModel::factory()->create([
        'status' => false,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeFalse();

    // Route Name exists and Panic Control is enabled
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();

    // Route Name exists and Panic Control is enabled but the route name is not in the list
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-error',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeFalse();

    // Panic Control is enabled but the route name set false
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'route-name' => false,
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();

    // Panic Control is enabled but the route name set null
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'route-name' => false,
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();
});

test('rule url path', function () {
    $this->get('http://localhost/url-path-test');

    // Url Path exists but Panic Control is disabled
    $panic = PanicControlModel::factory()->create([
        'status' => false,
        'rules' => [
            'url-path' => [
                'url-path-test',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeFalse();

    // Url Path exists and Panic Control is enabled
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'url-path' => [
                'url-path-test',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();

    // Url Path exists and Panic Control is enabled but the Url Path is not in the list
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'url-path' => [
                'url-path-error',
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeFalse();

    // Panic Control is enabled but the Url Path set false
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'url-path' => false,
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();

    // Panic Control is enabled but the Url Path set null
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'url-path' => false,
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();
});

test('rule percent access')->todo();
test('rule user')->todo();
test('rule user group')->todo();
