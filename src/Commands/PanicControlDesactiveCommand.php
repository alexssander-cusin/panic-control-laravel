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
        $panic = $this->argument('name');
        $panics = PanicControl::all();
        $updated = false;

        foreach ($panics as $key => $value) {
            if ($panic == $key) {
                $updated = PanicControl::update($key, ['status' => false] + $value);
                break;
            }
        }

        if (! $updated) {
            $this->error("Panic Control: {$panic} não encontrado.");

            return self::FAILURE;
        }

        $this->info("Panic Control: {$panic} desativado com sucesso.");

        return self::SUCCESS;
    }
}
