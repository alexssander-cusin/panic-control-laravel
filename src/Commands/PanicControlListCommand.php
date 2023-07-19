<?php

namespace PanicControl\Commands;

use Illuminate\Console\Command;
use PanicControl\Facades\PanicControl;
use Symfony\Component\Console\Helper\Table;

class PanicControlListCommand extends Command
{
    public $signature = 'panic-control:list';

    public $description = 'Lista todos os Panic Control';

    public function handle(): int
    {
        $panic = PanicControl::all();

        if (empty($panic)) {
            $this->error('Nenhum Panic Control encontrado.');

            return self::FAILURE;
        }

        $table = new Table($this->output);

        $table->setHeaders([
            'Name', 'Status', 'Description',
        ]);

        foreach ($panic as $key => $value) {
            $table->addRow([
                $value['name'],
                $value['status'] ? '<bg=green>Active</>' : '<bg=red>Inactive</>',
                $value['description'],
            ]);
        }

        $table->render();

        return self::SUCCESS;
    }
}
