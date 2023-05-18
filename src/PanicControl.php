<?php

namespace PanicControl;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PanicControl\Models\PanicControl as PanicControlModel;

class PanicControl
{
    public function create(array $parameters): PanicControlModel
    {
        $validator = Validator::make($parameters, [
            'service' => 'required|unique:panic_controls|max:255',
            'description' => 'max:255',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            Log::error('Campos inválidos.', $validator->errors()->all());
            throw new Exception('Campos inválidos.');
        }

        return PanicControlModel::create($validator->validated());
    }

    public function update(string|int $panic, array $parameters): PanicControlModel
    {
        $panic = (is_int($panic)) ? PanicControlModel::find($panic) : PanicControlModel::where('service', $panic)->first();

        if (empty($panic)) {
            Log::error('Panic Control não encontrado.', ['service' => $panic, 'parameters' => $parameters]);
            throw new Exception('Panic Control não encontrado.');
        }

        $validator = Validator::make($parameters, [
            'service' => [
                'required',
                Rule::unique('panic_controls')->ignore($panic->id),
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

        return $panic;
    }
}
