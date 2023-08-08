<?php

namespace PanicControl\Commands;

use Illuminate\Console\Command;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Exceptions\PanicControlStoreNotSupport;
use PanicControl\Facades\PanicControl;

class PanicControlActiveCommand extends Command
{
    public $signature = 'panic-control:active {name}';

    public $description = 'Ativa um Panic Control';

    public function handle(): int
    {
        $panicName = $this->argument('name');

        try {
            $panic = PanicControl::find($panicName);
        } catch (PanicControlDoesNotExist $th) {
            $this->error("Panic Control: {$panicName} não encontrado.");

            return self::FAILURE;
        }

        try {
            PanicControl::update($panicName, ['status' => true] + $panic);
        } catch (PanicControlStoreNotSupport $th) {
            $this->error("Panic Control: Store {$th->context()['store']} does not support update method.");

            return self::FAILURE;
        }

        $this->info("Panic Control: {$panicName} ativado com sucesso.");

        return self::SUCCESS;
    }
}
