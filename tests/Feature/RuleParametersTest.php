<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Lottery;
use PanicControl\Facades\PanicControl;

test('rule does not exist', function (string $driver) {
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'not-found' => [
                'not-found-parameter',
            ],
        ],
    ])[0];

    expect(fn () => PanicControl::check($panic['name']))->toThrow(\PanicControl\Exceptions\PanicControlRuleDoesNotExist::class);
})->with('stores');

test('multi rules', function (string $driver) {
    $this->get('http://localhost/url-path-test');
    \Illuminate\Support\Facades\Route::shouldReceive('currentRouteName')->andReturn('route-name-test');

    // Rules exists but Panic Control is disabled
    $panic = createPanic(count: 1, parameters: [
        'status' => false,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => [
                'url-path-test',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Rules exists and Panic Control is enabled
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => [
                'url-path-test',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Rules exists and Panic Control is enabled but one rule is false or null
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => false,
            'sampling' => null,
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Rules not list and Panic Control is enabled but one rule is false or null
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-error',
            ],
            'url-path' => false,
            'sampling' => null,
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Rules exists and Panic Control is enabled but one rule is not in the list
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
            'url-path' => [
                'url-path-error',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Rules exists and Panic Control is enabled but one rule is not in the list and the other is false
    $panic = createPanic(count: 1, parameters: [
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
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();
})->with('stores');

test('rule route name', function (string $driver) {
    \Illuminate\Support\Facades\Route::shouldReceive('currentRouteName')->andReturn('route-name-test');

    // Route Name exists but Panic Control is disabled
    $panic = createPanic(count: 1, parameters: [
        'status' => false,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Route Name exists and Panic Control is enabled
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-test',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Route Name exists and Panic Control is enabled but the route name is not in the list
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'route-name' => [
                'route-name-error',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Panic Control is enabled but the route name set false
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'route-name' => false,
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Panic Control is enabled but the route name set null
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'route-name' => false,
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();
})->with('stores');

test('rule url path', function (string $driver) {
    $this->get('http://localhost/url-path-test');

    // Url Path exists but Panic Control is disabled
    $panic = createPanic(count: 1, parameters: [
        'status' => false,
        'rules' => [
            'url-path' => [
                'url-path-test',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Url Path exists and Panic Control is enabled
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'url-path' => [
                'url-path-test',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Url Path exists and Panic Control is enabled but the Url Path is not in the list
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'url-path' => [
                'url-path-error',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Panic Control is enabled but the Url Path set false
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'url-path' => false,
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Panic Control is enabled but the Url Path set null
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'url-path' => null,
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();
})->with('stores');

test('rule user sampling', function (string $driver) {
    // Sampling exists but Panic Control is disabled
    $panic = createPanic(count: 1, parameters: [
        'status' => false,
        'rules' => [
            'sampling' => [
                'chance' => 1,
                'out_of' => 10,
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Panic Control is enabled but the Sampling set false
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'sampling' => false,
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Panic Control is enabled but the Sampling set null
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'sampling' => null,
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    //Set all Sampling with true
    Lottery::alwaysWin();

    // Panic Control is enabled and Sampling return true
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'sampling' => [
                'chance' => 1,
                'out_of' => 10,
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();
    expect(Session::get("panic-control.{$panic['name']}.sampling"))->toBeTrue();

    //Set all Sampling with false
    Lottery::alwaysLose();

    // Panic Control is enabled but the Sampling return false
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'sampling' => [
                'chance' => 1,
                'out_of' => 10,
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();
    expect(Session::get("panic-control.{$panic['name']}.sampling"))->toBeFalse();

    // Panic Control is enabled but the Sampling return iquals chance all times
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'sampling' => [
                'chance' => 5,
                'out_of' => 10,
            ],
        ],
    ])[0];
    $chance = PanicControl::check($panic['name']);
    expect(Session::get("panic-control.{$panic['name']}.sampling"))->toBe($chance);
    for ($i = 0; $i < 10; $i++) {
        expect(PanicControl::check($panic['name']))->toBe($chance);
    }
})->with('stores');

test('rule user', function (string $driver) {
    // Panic Control is enabled and the user is not logged in
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'user' => [
                'user@test.com',
                1,
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    Auth::shouldReceive('check')->andReturn(true);
    Auth::shouldReceive('user')->andReturn((object) ['id' => 1, 'email' => 'user@test.com']);

    // User exists but Panic Control is disabled
    $panic = createPanic(count: 1, parameters: [
        'status' => false,
        'rules' => [
            'user' => [
                'user@test.com',
                1,
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // User name exists and Panic Control is enabled
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'user' => [
                'user@test.com',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // User ID exists and Panic Control is enabled
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'user' => [
                1,
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Panic Control is enabled but the User Name is not in the list
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'user' => [
                'error@test.com',
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Panic Control is enabled but the User ID is not in the list
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'user' => [
                2,
            ],
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeFalse();

    // Panic Control is enabled but the User set false
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'user' => false,
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();

    // Panic Control is enabled but the User set null
    $panic = createPanic(count: 1, parameters: [
        'status' => true,
        'rules' => [
            'user' => null,
        ],
    ])[0];
    expect(PanicControl::check($panic['name']))->toBeTrue();
})->with('stores');
