<?php

namespace PanicControl\Exceptions;

use Exception;

class PanicControlDriverNotSupport extends Exception
{
    public function __construct(protected string $store, protected string $method)
    {
        parent::__construct("Panic Control: Driver {$this->store} does not support {$this->method} method.");
    }

    /**
     * Get the exception's context information.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'store' => $this->store,
            'method' => $this->method,
        ];
    }
}
