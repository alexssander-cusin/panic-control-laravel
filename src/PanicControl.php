<?php

namespace PanicControl;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PanicControl\Contracts\Store;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Exceptions\PanicControlRuleDoesNotExist;

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
        return $this->getCacheControl();
    }

    public function find(string $panic): array
    {
        return $this->getCacheControl($panic);
    }

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

    public function create(array $parameters): array
    {
        $panic = $this->store->create($parameters);

        $this->clear();

        return $panic;
    }

    public function update(string|int $panic, array $parameters): array
    {
        $panic = $this->store->update($panic, $parameters);

        $this->clear();

        return $panic;
    }

    public function clear()
    {
        Cache::forget(config('panic-control.cache.name'));
        self::$list = [];
    }
}
