<?php

namespace PanicControl;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PanicControl\Models\PanicControl as PanicControlModel;

class PanicControl
{
    public function create(array $panic): PanicControlModel
    {
        $validator = Validator::make($panic, [
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
}
