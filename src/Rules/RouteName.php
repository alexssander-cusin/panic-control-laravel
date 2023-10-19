<?php

namespace PanicControl\Rules;

use Illuminate\Support\Facades\Route;
use PanicControl\Contracts\RuleContract;

class RouteName extends Rule implements RuleContract
{
    public function rule(array $parameters): ?bool
    {
        foreach ($parameters as $parameter) {
            if (Route::currentRouteName() === $parameter) {
                return true;
            }
        }

        return false;
    }
}
