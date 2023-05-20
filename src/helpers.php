<?php

use PanicControl\Facades\PanicControl;

if (! function_exists('getPanicControlActive')) {
    function getPanicControlActive(string $panic): bool
    {
        return PanicControl::check($panic);
    }
}
