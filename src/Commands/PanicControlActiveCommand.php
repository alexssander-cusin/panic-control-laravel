<?php

namespace PanicControl\Commands;

use Illuminate\Console\Command;
use PanicControl\Models\PanicControl as PanicControlModel;

class PanicControlActiveCommand extends Command
{
    public $signature = 'panic-control:active {name}';

    public $description = 'Ativa um Panic Control';

    public function handle(): int
    {
        $panic = PanicControlModel::where('name', $this->argument('name'))->first();

        if (! $panic) {
            $this->error('Panic Control não encontrado.');

            return self::FAILURE;
        }

        $panic->update([
            'status' => true,
        ]);

        $this->info('Panic Control ativado com sucesso.');

        return self::SUCCESS;
    }
}
