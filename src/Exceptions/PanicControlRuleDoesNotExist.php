<?php

namespace PanicControl\Exceptions;

use Exception;

class PanicControlRuleDoesNotExist extends Exception
{
    public function __construct(string $rule)
    {
        $this->rule = $rule;
        parent::__construct("Panic Control: Rule {$this->rule} does not exist.");
    }

    /**
     * Get the exception's context information.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return ['rule' => $this->rule];
    }
}
