<?php

namespace PanicControl\Drivers;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PanicControl\Contracts\PanicControlContract;
use PanicControl\Exceptions\PanicControlDoesNotExist;
use PanicControl\Exceptions\PanicControlFileNotFound;
use PanicControl\Facades\PanicControl;
use PanicControl\PanicControlAbstract;

class File extends PanicControlAbstract implements PanicControlContract
{
    protected $key = 'file';

    public function getAll(): array
    {
        $storage = Storage::disk(config('panic-control.drivers.file.disk'));

        return $storage->exists(config('panic-control.drivers.file.path'))
            ? collect(json_decode($storage->get(config('panic-control.drivers.file.path')), true))->keyBy('name')->toArray()
            : throw new PanicControlFileNotFound();
    }

    public function validator(string|bool $ignore = false): array
    {
        return [
            'name' => [
                'max:255',
                function (string $attribute, mixed $value, Closure $fail) use ($ignore) {
                    if ($ignore == $value) {
                        return;
                    }

                    try {
                        PanicControl::driver('file')->find($value);
                    } catch (PanicControlFileNotFound $th) {
                        return;
                    } catch (PanicControlDoesNotExist $th) {
                        return;
                    }

                    $fail("The {$value} exists.");
                },
            ],
            'description' => 'max:255',
        ];
    }

    public function store(array $parameters): array
    {
        try {
            $panics = $this->all();
        } catch (PanicControlFileNotFound $th) {
            $panics = [];
        }

        $panics[] = $parameters + [
            'id' => (empty($panics)) ? 1 : max(array_column($panics, 'id')) + 1,
            'rules' => [],
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        Storage::disk(config('panic-control.drivers.file.disk'))
            ->put(config('panic-control.drivers.file.path'), json_encode($panics, JSON_PRETTY_PRINT));

        return Arr::last($panics);
    }

    public function update(string|int $panicName, array $parameters): array
    {
        $panic = PanicControl::find($panicName);

        if (empty($panic)) {
            Log::error('Panic Control: não encontrado.', ['name' => $panicName, 'parameters' => $parameters]);
            throw new PanicControlDoesNotExist($panicName);
        }

        $parameters = array_merge(
            $panic,
            $parameters
        );

        $panics = $this->all();

        $panics[$parameters['name']] = $parameters;

        Storage::disk(config('panic-control.drivers.file.disk'))
            ->put(config('panic-control.drivers.file.path'), json_encode(array_values($panics), JSON_PRETTY_PRINT));

        return $parameters;
    }

    public function count(): int
    {
        return count($this->all());
    }
}
