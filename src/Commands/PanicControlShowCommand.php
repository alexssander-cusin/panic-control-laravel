<?php

namespace PanicControl\Commands;

use Illuminate\Console\Command;
use PanicControl\Models\PanicControl as PanicControlModel;

class PanicControlShowCommand extends Command
{
    public $signature = 'panic-control:show {name}';

    public $description = 'Comando para visualizar um Panic Control';

    public function handle(): int
    {
        $panic = PanicControlModel::where('name', $this->argument('name'))->first();

        if (! $panic) {
            $this->error('Panic Control não encontrado.');

            return self::FAILURE;
        }

        foreach ($panic->toArray() as $key => $value) {
            $this->info($key.': '.$value);
        }

        return self::SUCCESS;
    }
}
