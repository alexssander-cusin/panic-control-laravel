<?php

namespace PanicControl\Rules;

use Illuminate\Support\Facades\Request;
use PanicControl\Contracts\RuleContract;

class UrlPath extends Rule implements RuleContract
{
    public function rule(array $parameters): ?bool
    {
        foreach ($parameters as $parameter) {
            if (Request::path() === $parameter) {
                return true;
            }
        }

        return false;
    }
}
