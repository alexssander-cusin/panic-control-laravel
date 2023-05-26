<?php

use Illuminate\Support\Lottery;
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
            'url-path' => null,
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();
});

test('rule user sampling', function () {
    // Sampling exists but Panic Control is disabled
    $panic = PanicControlModel::factory()->create([
        'status' => false,
        'rules' => [
            'sampling' => [
                'chance' => 1,
                'out_of' => 10,
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeFalse();

    // Panic Control is enabled but the Sampling set false
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'sampling' => false,
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();

    // Panic Control is enabled but the Sampling set null
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'sampling' => null,
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();

    //Set all Sampling with true
    Lottery::alwaysWin();

    // Panic Control is enabled and Sampling return true
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'sampling' => [
                'chance' => 1,
                'out_of' => 10,
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeTrue();
    expect(Session::get("panic-control.{$panic['name']}.sampling"))->toBeTrue();

    //Set all Sampling with false
    Lottery::alwaysLose();

    // Panic Control is enabled but the Sampling return false
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'sampling' => [
                'chance' => 1,
                'out_of' => 10,
            ],
        ],
    ]);
    expect(PanicControl::check($panic->name))->toBeFalse();
    expect(Session::get("panic-control.{$panic['name']}.sampling"))->toBeFalse();

    // Panic Control is enabled but the Sampling return iquals chance all times
    $panic = PanicControlModel::factory()->create([
        'status' => true,
        'rules' => [
            'sampling' => [
                'chance' => 5,
                'out_of' => 10,
            ],
        ],
    ]);
    $chance = PanicControl::check($panic->name);
    expect(Session::get("panic-control.{$panic['name']}.sampling"))->toBe($chance);
    for ($i = 0; $i < 10; $i++) {
        expect(PanicControl::check($panic->name))->toBe($chance);
    }
});

test('rule user')->todo();
test('rule user group')->todo();
