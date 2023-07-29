<?php

namespace PanicControl\Rules;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Lottery;
use PanicControl\Contracts\Rule as RuleContract;

class Sampling extends Rule implements RuleContract
{
    public function rule(array $parameters): ?bool
    {
        $sessionName = "panic-control.{$this->panic['name']}.sampling";

        if (Session::has($sessionName)) {
            return Session::get($sessionName);
        }

        $value = Lottery::odds($parameters['chance'], $parameters['out_of'])->choose();

        Session::put($sessionName, $value);

        return $value;
    }
}
