<?php

namespace PanicControl;

use Illuminate\Support\Manager;
use PanicControl\Drivers\Database;
use PanicControl\Drivers\Endpoint;
use PanicControl\Drivers\File;

class PanicControlManager extends Manager
{
    public function getDefaultDriver()
    {
        return config('panic-control.default');
    }

    public function createDatabaseDriver()
    {
        return new PanicControl(new Database());
    }

    public function createFileDriver()
    {
        return new PanicControl(new File());
    }

    public function createEndpointDriver()
    {
        return new PanicControl(new Endpoint());
    }
}
