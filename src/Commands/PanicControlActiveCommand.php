<?php

namespace PanicControl\Commands;

use Illuminate\Console\Command;
use PanicControl\Models\PanicControl as PanicControlModel;

class PanicControlActiveCommand extends Command
{
    public $signature = 'panic-control:active {service}';

    public $description = 'Ativa um Panic Control';

    public function handle(): int
    {
        $panic = PanicControlModel::where('service', $this->argument('service'))->first();

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
