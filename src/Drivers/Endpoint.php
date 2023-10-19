<?php

namespace PanicControl\Drivers;

use Illuminate\Support\Facades\Http;
use PanicControl\Contracts\PanicControlContract;
use PanicControl\Exceptions\PanicControlDriverNotSupport;
use PanicControl\PanicControlAbstract;

class Endpoint extends PanicControlAbstract implements PanicControlContract
{
    protected string $key = 'endpoint';

    public function getAll(): array
    {
        return collect(Http::get(config('panic-control.drivers.endpoint.url'))->json())
            ->keyBy('name')
            ->toArray();
    }

    public function validator(string|bool $ignore = false): array
    {
        throw new PanicControlDriverNotSupport('endpoint', 'create');
    }

    public function store(array $parameters): array
    {
        throw new PanicControlDriverNotSupport('endpoint', 'create');
    }

    public function update(string|int $panicName = null, array $parameters): array
    {
        throw new PanicControlDriverNotSupport('endpoint', 'update');
    }

    public function count(): int
    {
        return count($this->all());
    }
}
