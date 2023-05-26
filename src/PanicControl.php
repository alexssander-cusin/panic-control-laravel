<?php

namespace PanicControl;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Exceptions\PanicControlRuleDoesNotExist;
use PanicControl\Models\PanicControl as PanicControlModel;

class PanicControl
{
    private static $list = [];

    protected function get(string $panic = null): array
    {
        $config = config('panic-control.cache');
        if (! self::$list) {
            self::$list = Cache::store($config['store'])->remember($config['key'], $config['time'], function () {
                return PanicControlModel::all()->keyBy('name')->toArray();
            });
        }

        if (is_null($panic)) {
            return self::$list;
        }

        try {
            return self::$list[$panic];
        } catch (\Throwable $th) {
            throw new PanicControlDoesNotExist($panic);
        }
    }

    public function all(): array
    {
        return $this->get();
    }

    public function find(string $panic): array
    {
        return $this->get($panic);
    }

    public function check(string $panic): bool
    {

        try {
            $panic = $this->get($panic);
        } catch (PanicControlDoesNotExist $th) {
            if (app()->environment('production')) {
                Log::error($th->getMessage(), ['name' => $panic]);

                return false;
            }
            throw $th;
        }

        $status = $panic['status'];

        if ($status && $panic['rules']) {
            foreach ($panic['rules'] as $rule => $parameters) {
                if ($panic === false || empty($parameters)) {
                    continue;
                }

                try {
                    $rule = app(config('panic-control.rules')[$rule], [
                        'panic' => $panic,
                    ])->rule($parameters);
                } catch (\Throwable $th) {
                    throw new PanicControlRuleDoesNotExist($rule);
                }

                if (is_bool($rule)) {
                    $status = $rule;
                }
            }
        }

        return $status;
    }

    public function create(array $parameters): PanicControlModel
    {
        $validator = Validator::make($parameters, [
            'name' => 'required|unique:'.config('panic-control.database.table').'|max:255',
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
        $panic = (is_int($panic)) ? PanicControlModel::find($panic) : PanicControlModel::where('name', $panic)->first();

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
                Rule::unique(config('panic-control.database.table'))->ignore($panic->id),
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

    public function clear()
    {
        Cache::forget(config('panic-control.cache.name'));
        self::$list = [];
    }
}
