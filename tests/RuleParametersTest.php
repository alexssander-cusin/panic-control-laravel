<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Lottery;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

test('rule does not exist', function () {
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'not-found' => [
                'not-found-parameter',
            ],
        ],
    ])->toArray());

    expect(fn () => PanicControl::check($panic['name']))->toThrow(\PanicControl\Exceptions\PanicControlRuleDoesNotExist::class);
});

test('multi rules', function () {
    $this->get('http://localhost/url-path-test');
    \Illuminate\Support\Facades\Route::shouldReceive('currentRouteName')->andReturn('route-name-test');

    // Rules exists but Panic Control is disabled
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => false,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => [
                'url-path-test',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Rules exists and Panic Control is enabled
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => [
                'url-path-test',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Rules exists and Panic Control is enabled but one rule is false or null
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => false,
            'sampling' => null,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Rules not list and Panic Control is enabled but one rule is false or null
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-error',
            ],
            'url-path' => false,
            'sampling' => null,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Rules exists and Panic Control is enabled but one rule is not in the list
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => [
                'url-path-error',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Rules exists and Panic Control is enabled but one rule is not in the list and the other is false
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => [
                'url-path-error',
            ],
            'sampling' => false,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();
});

test('rule route name', function () {
    \Illuminate\Support\Facades\Route::shouldReceive('currentRouteName')->andReturn('route-name-test');

    // Route Name exists but Panic Control is disabled
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => false,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Route Name exists and Panic Control is enabled
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Route Name exists and Panic Control is enabled but the route name is not in the list
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-error',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Panic Control is enabled but the route name set false
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'route-name' => false,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Panic Control is enabled but the route name set null
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'route-name' => false,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();
});

test('rule url path', function () {
    $this->get('http://localhost/url-path-test');

    // Url Path exists but Panic Control is disabled
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => false,
        'rules' => [
            'url-path' => [
                'url-path-test',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Url Path exists and Panic Control is enabled
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'url-path' => [
                'url-path-test',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Url Path exists and Panic Control is enabled but the Url Path is not in the list
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'url-path' => [
                'url-path-error',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Panic Control is enabled but the Url Path set false
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'url-path' => false,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Panic Control is enabled but the Url Path set null
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'url-path' => null,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();
});

test('rule user sampling', function () {
    // Sampling exists but Panic Control is disabled
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => false,
        'rules' => [
            'sampling' => [
                'chance' => 1,
                'out_of' => 10,
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Panic Control is enabled but the Sampling set false
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'sampling' => false,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Panic Control is enabled but the Sampling set null
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'sampling' => null,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    //Set all Sampling with true
    Lottery::alwaysWin();

    // Panic Control is enabled and Sampling return true
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'sampling' => [
                'chance' => 1,
                'out_of' => 10,
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();
    expect(Session::get("panic-control.{$panic['name']}.sampling"))->toBeTrue();

    //Set all Sampling with false
    Lottery::alwaysLose();

    // Panic Control is enabled but the Sampling return false
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'sampling' => [
                'chance' => 1,
                'out_of' => 10,
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();
    expect(Session::get("panic-control.{$panic['name']}.sampling"))->toBeFalse();

    // Panic Control is enabled but the Sampling return iquals chance all times
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'sampling' => [
                'chance' => 5,
                'out_of' => 10,
            ],
        ],
    ])->toArray());
    $chance = PanicControl::check($panic['name']);
    expect(Session::get("panic-control.{$panic['name']}.sampling"))->toBe($chance);
    for ($i = 0; $i < 10; $i++) {
        expect(PanicControl::check($panic['name']))->toBe($chance);
    }
});

test('rule user', function () {
    // Panic Control is enabled and the user is not logged in
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'user' => [
                'user@test.com',
                1,
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    Auth::shouldReceive('check')->andReturn(true);
    Auth::shouldReceive('user')->andReturn((object) ['id' => 1, 'email' => 'user@test.com']);

    // User exists but Panic Control is disabled
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => false,
        'rules' => [
            'user' => [
                'user@test.com',
                1,
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // User name exists and Panic Control is enabled
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'user' => [
                'user@test.com',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // User ID exists and Panic Control is enabled
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'user' => [
                1,
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Panic Control is enabled but the User Name is not in the list
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'user' => [
                'error@test.com',
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Panic Control is enabled but the User ID is not in the list
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'user' => [
                2,
            ],
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Panic Control is enabled but the User set false
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'user' => false,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Panic Control is enabled but the User set null
    $panic = PanicControl::create(PanicControlModel::factory()->make([
        'status' => true,
        'rules' => [
            'user' => null,
        ],
    ])->toArray());
    expect(PanicControl::check($panic['name']))->toBeTrue();
});
