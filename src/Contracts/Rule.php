<?php

namespace PanicControl\Contracts;

interface Rule
{
    public function rule(array $parameters): ?bool;
}
