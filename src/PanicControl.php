<?php

namespace PanicControl;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PanicControl\Contracts\Store;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Exceptions\PanicControlRuleDoesNotExist;

class PanicControl
{
    private $storeName;

    private static $list = [];

    public function __construct(
        private Store $store
    ) {
        $this->storeName = $this->store::class;
    }

    protected function getCacheControl(string $panic = null): array
    {
        $cache = config('panic-control.cache');

        if (! isset(self::$list[$this->storeName])) {
            $cacheStore = $cache['enabled'] ? $cache['store'] : 'array';
            self::$list[$this->storeName] = Cache::store($cacheStore)->remember("{$cache['key']}:{$this->storeName}", $cache['ttl'], function () {
                return $this->store->all();
            });
        }

        if (is_null($panic)) {
            return self::$list[$this->storeName];
        }

        return self::$list[$this->storeName][$panic] ?? throw new PanicControlDoesNotExist($panic);
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
        $validator = Validator::make($parameters, array_merge_recursive($this->store->validator(), [
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

        $panic = $this->store->create($parameters);

        $this->clear();

        return $panic;
    }

    public function update(string|int $panic, array $parameters): array
    {
        $validator = Validator::make($parameters, array_merge_recursive($this->store->validator($panic), [
            'name' => [
                'string',
            ],
            'status' => 'boolean',
            'rules' => 'array',
        ]));

        if ($validator->fails()) {
            throw new Exception('Panic Control: '.implode(', ', $validator->errors()->all()));
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
