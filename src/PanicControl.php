<?php

namespace PanicControl;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PanicControl\Models\PanicControl as PanicControlModel;

class PanicControl
{
    private static $list = [];

    public function clear()
    {
        Cache::forget(config('panic-control.cache.name'));
        self::$list = [];
    }

    public function get(string $panic = null): array
    {
        if (! self::$list) {
            self::$list = Cache::remember(config('panic-control.cache.name'), config('panic-control.cache.time'), function () {
                return PanicControlModel::all()->keyBy('service')->toArray();
            });
        }

        if (is_null($panic)) {
            return self::$list;
        }

        try {
            return self::$list[$panic];
        } catch (\Throwable $th) {
            Log::error('Panic Control não encontrado.', ['service' => $panic]);
            throw new Exception('Panic Control não encontrado.');
        }
    }

    public function all(): array
    {
        return $this->get();
    }

    public function check(string $panic): bool
    {
        $panic = $this->get($panic);

        return $panic['status'];
    }

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

        $parameters = array_merge(
            $panic->only([
                'service',
                'description',
                'status',
            ]),
            $parameters
        );

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
