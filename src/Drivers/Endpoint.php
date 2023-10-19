<?php

namespace PanicControl\Drivers;

use Illuminate\Support\Facades\Http;
use PanicControl\Exceptions\PanicControlDriverNotSupport;
use PanicControl\PanicControlAbstract;

class Endpoint extends PanicControlAbstract
{
    protected $key = 'endpoint';

    public function all(): array
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

    public function save(string|int $panicName = null, array $parameters): array
    {
        throw new PanicControlDriverNotSupport('endpoint', 'update');
    }

    public function count(): int
    {
        return count($this->all());
    }
}
