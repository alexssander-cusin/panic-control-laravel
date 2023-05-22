<?php

namespace PanicControl\Commands;

use Illuminate\Console\Command;
use PanicControl\Models\PanicControl as PanicControlModel;

class PanicControlDesactiveCommand extends Command
{
    public $signature = 'panic-control:desactive {name}';

    public $description = 'Desativa um Panic Control';

    public function handle(): int
    {
        $panic = PanicControlModel::where('name', $this->argument('name'))->first();

        if (! $panic) {
            $this->error('Panic Control não encontrado.');

            return self::FAILURE;
        }

        $panic->update([
            'status' => false,
        ]);

        $this->info('Panic Control desativado com sucesso.');

        return self::SUCCESS;
    }
}
