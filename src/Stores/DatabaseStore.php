<?php

namespace PanicControl\Stores;

use PanicControl\Contracts\Store;
use PanicControl\Models\PanicControl;

final class DatabaseStore implements Store
{
    public function all(): array
    {
        return PanicControl::all()->keyBy('name')->toArray();
    }
}
