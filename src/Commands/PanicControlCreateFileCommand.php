<?php

namespace PanicControl\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PanicControlCreateFileCommand extends Command
{
    public $signature = 'panic-control:create-file';

    public $description = 'Comando criar arquivo panic-control.json';

    public function handle(): int
    {
        if (config('panic-control.default') !== 'file') {
            $this->error('O store configurado não é do tipo FILE.');

            return self::FAILURE;
        }

        $config = config('panic-control.drivers.file');

        Storage::disk($config['disk'])->put($config['path'], file_get_contents(__DIR__.'/../../resources/panic-control-template.json'));

        $this->info('Arquivo criado com sucesso.');

        return self::SUCCESS;
    }
}
