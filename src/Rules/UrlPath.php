<?php

namespace PanicControl\Rules;

use Illuminate\Support\Facades\Request;
use PanicControl\Contracts\Rule as RuleContract;

class UrlPath extends Rule implements RuleContract
{
    public function rule(array $parameters): bool|null
    {
        foreach ($parameters as $parameter) {
            if (Request::path() === $parameter) {
                return true;
            }
        }

        return false;
    }
}
