<?php

namespace PanicControl\Rules;

use Illuminate\Support\Facades\Auth;
use PanicControl\Contracts\Rule as RuleContract;

class User extends Rule implements RuleContract
{
    public function rule(array $parameters): ?bool
    {
        if (
            Auth::check() &&
            (
                in_array(Auth::user()->id, $parameters) ||
                in_array(Auth::user()->email, $parameters)
            )
        ) {
            return true;
        }

        return false;
    }
}
