<?php

namespace PanicControl;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PanicControl\Contracts\Store;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Exceptions\PanicControlRuleDoesNotExist;
use PanicControl\Models\PanicControl as PanicControlModel;

class PanicControl
{
    private static $list = [];

    public function __construct(
        private Store $store
    ) {
    }

    protected function get(string $panic = null): array
    {
        $cache = config('panic-control.cache');
        if (! self::$list) {
            $cacheStore = $cache['enabled'] ? $cache['store'] : 'array';
            self::$list = Cache::store($cacheStore)->remember($cache['key'], $cache['time'], function () {
                return $this->store->all();
            });
        }

        if (is_null($panic)) {
            return self::$list;
        }

        return self::$list[$panic] ?? throw new PanicControlDoesNotExist($panic);
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
            if (! app()->hasDebugModeEnabled()) {
                Log::error($th->getMessage(), ['name' => $panic]);

                return false;
            }
            throw $th;
        }

        $status = $panic['status'];

        if ($status && $panic['rules']) {
            foreach ($panic['rules'] as $rule => $parameters) {
                if (empty($parameters)) {
                    continue;
                }

                try {
                    $rule = app(config('panic-control.rules')[$rule], [
                        'panic' => $panic,
                    ])->rule($parameters);
                } catch (\Throwable $th) {
                    // if (app()->environment('testing')) {
                    //     throw $th;
                    // }
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
            'name' => 'required|unique:'.config('panic-control.stores.database.table').'|max:255',
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

        return $panic;
    }

    public function clear()
    {
        Cache::forget(config('panic-control.cache.name'));
        self::$list = [];
    }
}
