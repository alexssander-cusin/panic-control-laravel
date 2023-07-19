<?php

namespace PanicControl\Commands;

use Illuminate\Console\Command;
use PanicControl\Facades\PanicControl;

class PanicControlDesactiveCommand extends Command
{
    public $signature = 'panic-control:desactive {name}';

    public $description = 'Desativa um Panic Control';

    public function handle(): int
    {
        $panicName = $this->argument('name');

        try {
            $panic = PanicControl::find($panicName);
        } catch (PanicControlDoesNotExist $th) {
            $this->error("Panic Control: {$panic} não encontrado.");

            return self::FAILURE;
        }

        PanicControl::update($panicName, ['status' => false] + $panic);

        $this->info("Panic Control: {$panicName} desativado com sucesso.");

        return self::SUCCESS;
    }
}
