<?php

namespace PanicControl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \PanicControl\PanicControl
 */
class PanicControl extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \PanicControl\PanicControl::class;
    }
}
