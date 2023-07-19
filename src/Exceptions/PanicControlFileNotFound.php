<?php

namespace PanicControl\Exceptions;

use Exception;

class PanicControlFileNotFound extends Exception
{
    public function __construct()
    {
        parent::__construct('Panic Control file not found');
    }
}
