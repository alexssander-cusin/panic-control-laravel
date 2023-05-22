<?php

namespace PanicControl\Rules;

use Illuminate\Support\Facades\Route;
use PanicControl\Contracts\Rule as RuleContract;

class RouteName implements RuleContract
{
    public function rule(array $parameters): bool|null
    {
        foreach ($parameters as $parameter) {
            if (Route::currentRouteName() === $parameter) {
                return true;
            }
        }

        return false;
    }
}
