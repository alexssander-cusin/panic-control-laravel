<?php

namespace PanicControl\Facades;

use Illuminate\Support\Facades\Facade;

class PanicControl extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'panic_control';
    }
}
