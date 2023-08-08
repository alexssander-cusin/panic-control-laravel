<?php

namespace PanicControl\Stores;

use Illuminate\Support\Facades\Http;
use PanicControl\Contracts\Store;
use PanicControl\Exceptions\PanicControlStoreNotSupport;

class EndpointStore implements Store
{
    public function all(): array
    {
        return collect(Http::get(config('panic-control.stores.endpoint.url'))->json())
            ->keyBy('name')
            ->toArray();
    }

    public function create(array $parameters): array
    {
        throw new PanicControlStoreNotSupport('endpoint', 'create');
    }

    public function update(string|int $panicName, array $parameters): array
    {
        throw new PanicControlStoreNotSupport('endpoint', 'update');
    }

    public function count(): int
    {
        return count($this->all());
    }
}
