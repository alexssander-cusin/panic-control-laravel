<?php

namespace PanicControl\Commands;

use Illuminate\Console\Command;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Facades\PanicControl;

class PanicControlShowCommand extends Command
{
    public $signature = 'panic-control:show {name}';

    public $description = 'Comando para visualizar um Panic Control';

    public function handle(): int
    {
        try {
            $panic = PanicControl::find($this->argument('name'));
        } catch (PanicControlDoesNotExist $th) {
            $this->error($th->getMessage());

            return self::FAILURE;
        }

        foreach ($panic as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $this->info($key.': '.$value);
        }

        return self::SUCCESS;
    }
}
