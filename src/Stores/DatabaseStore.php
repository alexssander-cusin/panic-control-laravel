<?php

namespace PanicControl\Stores;

use Exception;
use Illuminate\Support\Facades\Log;
use PanicControl\Contracts\Store;
use PanicControl\Models\PanicControl;

final class DatabaseStore implements Store
{
    public function all(): array
    {
        return PanicControl::all()->keyBy('name')->toArray();
    }

    public function create(array $parameters): array
    {

        return PanicControl::create($parameters)->toArray();
    }

    public function update(string|int $panic, array $parameters): array
    {
        $panic = (is_int($panic)) ? PanicControl::find($panic) : PanicControl::where('name', $panic)->first();

        if (empty($panic)) {
            Log::error('Panic Control não encontrado.', ['name' => $panic, 'parameters' => $parameters]);
            throw new Exception('Panic Control não encontrado.');
        }

        $parameters = array_merge(
            $panic->only([
                'name',
                'description',
                'status',
            ]),
            $parameters
        );

        $panic->fill($parameters);
        $panic->save();

        return $panic->toArray();
    }

    public function count(): int
    {
        return PanicControl::count();
    }
}
