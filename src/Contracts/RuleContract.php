<?php

namespace PanicControl\Contracts;

interface RuleContract
{
    public function rule(array $parameters): ?bool;
}
