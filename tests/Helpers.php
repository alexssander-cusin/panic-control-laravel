<?php

use Illuminate\Support\Facades\Http;
use PanicControl\Exceptions\PanicControlStoreNotSupport;
use PanicControl\Facades\PanicControl;
use PanicControl\Models\PanicControl as PanicControlModel;

function createPanic($count = 1, $parameters = []): array
{
    $panics = PanicControlModel::factory()->count($count)->make($parameters)->toArray();

    try {
        foreach ($panics as $panic) {
            PanicControl::create($panic);
        }
    } catch (PanicControlStoreNotSupport $th) {
        if ($th->context()['store'] == 'endpoint') {
            makeFakeEndpoint($panics, 200);
        } else {
            throw $th;
        }
    } catch (\Throwable $th) {
        throw $th;
    }

    return $panics;
}

function makeFakeEndpoint($response = [], $status = 200)
{
    $reflection = new \ReflectionObject(Http::getFacadeRoot());
    $property = $reflection->getProperty('stubCallbacks');
    $property->setAccessible(true);
    $property->setValue(Http::getFacadeRoot(), collect());

    Http::fake([
        '*' => Http::response($response, $status),
    ]);

    PanicControl::clear();
}
