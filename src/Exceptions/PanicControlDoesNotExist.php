<?php

namespace PanicControl\Exceptions;

use Exception;

class PanicControlDoesNotExist extends Exception
{
    public function __construct(protected string $name)
    {
        parent::__construct("Panic Control: {$this->name} does not exist.");
    }

    /**
     * Get the exception's context information.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return ['name' => $this->name];
    }
}
