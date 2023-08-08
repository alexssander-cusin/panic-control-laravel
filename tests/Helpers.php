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
            Http::fake([
                '*' => Http::response($panics, 200),
            ]);
        } else {
            throw $th;
        }
    } catch (\Throwable $th) {
        throw $th;
    }

    return $panics;
}
