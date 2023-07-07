<?php

namespace PanicControl\Stores;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
        $validator = Validator::make($parameters, [
            'name' => 'required|unique:'.config('panic-control.stores.database.table').'|max:255',
            'description' => 'max:255',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            Log::error('Campos inválidos.', $validator->errors()->all());
            throw new Exception('Campos inválidos.');
        }

        return PanicControl::create($validator->validated())->toArray();
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

        $validator = Validator::make($parameters, [
            'name' => [
                'required',
                Rule::unique(config('panic-control.stores.database.table'))->ignore($panic->id),
                'max:255',
            ],
            'description' => 'max:255',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            Log::error('Campos inválidos.', $validator->errors()->all());
            throw new Exception('Campos inválidos.');
        }

        $panic->fill($validator->validated());
        $panic->save();

        return $panic->toArray();
    }
}
