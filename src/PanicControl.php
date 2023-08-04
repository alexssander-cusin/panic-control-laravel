<?php

namespace PanicControl;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PanicControl\Contracts\Store;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Exceptions\PanicControlRuleDoesNotExist;
use PanicControl\Validations\UniqueNameRule;

class PanicControl
{
    private static $list = [];

    public function __construct(
        private Store $store
    ) {
    }

    protected function getCacheControl(string $panic = null): array
    {
        $cache = config('panic-control.cache');
        if (! self::$list) {
            $cacheStore = $cache['enabled'] ? $cache['store'] : 'array';
            self::$list = Cache::store($cacheStore)->remember($cache['key'], $cache['ttl'], function () {
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
        return $this->getCacheControl();
    }

    public function find(string $panic): array
    {
        return $this->getCacheControl($panic);
    }

    /**
     * Create with Panic Control is active
     */
    public function check(string $panic): bool
    {
        try {
            $panic = $this->getCacheControl($panic);
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
                    throw new PanicControlRuleDoesNotExist($rule);
                }

                if (is_bool($rule)) {
                    $status = $rule;
                }
            }
        }

        return $status;
    }

    public function create(array $parameters): array
    {
        $validator = Validator::make($parameters, [
            'name' => [
                'required',
                'string',
                'max:255',
                new UniqueNameRule,
            ],
            'description' => 'max:255',
            'status' => 'boolean',
            'rules' => 'array',
        ]);

        if ($validator->fails()) {
            Log::error('Campos inválidos.', $validator->errors()->all());
            throw new Exception('Campos inválidos.');
        }

        $panic = $this->store->create($parameters);

        $this->clear();

        return $panic;
    }

    public function update(string|int $panic, array $parameters): array
    {
        $validator = Validator::make($parameters, [
            'name' => [
                'string',
                'max:255',
                new UniqueNameRule($panic),
            ],
            'description' => 'max:255',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            Log::error('Campos inválidos.', $validator->errors()->all());
            throw new Exception('Campos inválidos.');
        }

        $panic = $this->store->update($panic, $parameters);

        $this->clear();

        return $panic;
    }

    public function count(): int
    {
        return $this->store->count();
    }

    public function clear()
    {
        $cache = config('panic-control.cache');

        if ($cache['enabled']) {
            Cache::store($cache['store'])->forget($cache['key']);
        }

        self::$list = [];
    }
}
