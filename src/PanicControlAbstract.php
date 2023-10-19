<?php

namespace PanicControl;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Exceptions\PanicControlRuleDoesNotExist;

abstract class PanicControlAbstract
{
    private static $list = [];

    protected function getCacheControl(string $panic = null): array
    {
        $cache = config('panic-control.cache');

        if (empty(self::$list[$this->key])) {
            $cacheStore = $cache['enabled'] ? $cache['store'] : 'array';

            self::$list[$this->key] = Cache::store($cacheStore)->remember("{$cache['key']}:{$this->key}", $cache['ttl'], function () {
                return $this->all();
            });
        }

        if (is_null($panic)) {
            return self::$list[$this->key];
        }

        return self::$list[$this->key][$panic] ?? throw new PanicControlDoesNotExist($panic);
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
        $validator = Validator::make($parameters, array_merge_recursive($this->validator(), [
            'name' => [
                'required',
                'string',
            ],
            'status' => 'boolean',
            'rules' => 'array',
        ]));

        if ($validator->fails()) {
            throw new Exception('Panic Control: '.implode(', ', $validator->errors()->all()));
        }

        $panic = $this->store($parameters);

        $this->clear();

        return $panic;
    }

    public function edit(string|int $panic, array $parameters): array
    {
        $validator = Validator::make($parameters, array_merge_recursive($this->validator($panic), [
            'name' => [
                'string',
            ],
            'status' => 'boolean',
            'rules' => 'array',
        ]));

        if ($validator->fails()) {
            throw new Exception('Panic Control: '.implode(', ', $validator->errors()->all()));
        }

        $panic = $this->update($panic, $parameters);

        $this->clear();

        return $panic;
    }

    public function clear()
    {
        $cache = config('panic-control.cache');

        if ($cache['enabled']) {
            Cache::store($cache['store'])->forget($cache['key']);
        }

        self::$list[$this->key] = [];
    }
}
