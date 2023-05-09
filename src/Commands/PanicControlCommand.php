<?php

namespace PanicControl\Commands;

use Illuminate\Console\Command;

class PanicControlCommand extends Command
{
    public $signature = 'panic-control-laravel';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
